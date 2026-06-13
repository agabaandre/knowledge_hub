<?php

namespace App\Services;

use App\Support\MetricsCache;
use App\Support\SearchCache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiSearchChatService
{
    private const MAX_HISTORY = 8;

    public function __construct(
        private AiSearchInsightsService $insightsService,
        private AiCompletionService $completion
    ) {
    }

    /**
     * @return array{ok: bool, conversation_id?: string, reply?: string, documents?: list<array<string, mixed>>, error?: string}
     */
    public function respond(
        Request $request,
        string $message,
        ?string $conversationId,
        Collection $searchForums,
        Collection $searchCommunities,
        Collection $federatedPublications = new Collection
    ): array {
        if (! (bool) (settings()->enable_ai_search ?? false)) {
            return ['ok' => false, 'error' => 'AI search is not enabled.'];
        }

        $term = trim((string) ($request->term ?? ''));
        $message = trim($message);
        if ($message === '' || mb_strlen($message) < 2) {
            return ['ok' => false, 'error' => 'Please enter a question (at least 2 characters).'];
        }

        if ($term === '' || mb_strlen($term) < 2) {
            return ['ok' => false, 'error' => 'Run a search first so Khub AI has context for your question.'];
        }

        $conversationId = $this->normalizeConversationId($conversationId);
        $fingerprint = $this->insightsService->requestFingerprint($request);
        $history = $this->loadHistory($conversationId, $fingerprint);

        $catalog = $this->insightsService->buildCatalogPayload(
            $request,
            $searchForums,
            $searchCommunities,
            $federatedPublications,
            15
        );

        if (! $this->hasChatCatalogContent($catalog)) {
            return ['ok' => false, 'error' => 'No hub resources matched your search. Try different keywords or filters.'];
        }

        $intent = $this->detectMessageIntent($message);
        $compactCatalog = $this->compactCatalogForChat($catalog, $intent);
        $maxTokens = in_array($intent, ['analyze', 'summarize', 'compare'], true) ? 2200 : 1400;

        $system = 'You are Khub AI, the Africa CDC Knowledge Hub search assistant. '
            .'Answer follow-up questions using ONLY the hub catalog provided in each user turn. '
            .'Read description and excerpt fields carefully before answering. '
            .'Directly address the user question; do not repeat the same generic summary for every follow-up. '
            .'When the user asks to unpack, explain, or analyze document(s), synthesize the description text into clear plain-language points (purpose, population/setting, methods or scope, findings, limitations). '
            .'For multiple documents, compare how they differ; do not paste the same overview for each item. '
            .'When the user asks for documents, policies, or evidence, cite specific catalog items by id. '
            .'Tone: professional public health brief. No markdown or HTML. '
            .'If the catalog lacks an answer, say so and suggest refining the search. '
            .'Never invent titles, URLs, or statistics. '
            .'Return strict JSON: {"reply":"string max 1200 chars","publication_ids":[int],"forum_ids":[int],"community_ids":[int],"indicator_ids":[int]}';

        $messages = [
            ['role' => 'system', 'content' => $system],
        ];

        foreach ($history as $turn) {
            $messages[] = ['role' => 'user', 'content' => (string) ($turn['user'] ?? '')];
            $messages[] = ['role' => 'assistant', 'content' => (string) ($turn['assistant'] ?? '')];
        }

        $messages[] = ['role' => 'user', 'content' => $this->buildUserTurnContent($term, $message, $compactCatalog)];

        $result = $this->completion->completeForFeatureWithFallback('ai_search_chat', $messages, $maxTokens, null, true);
        if (! ($result['ok'] ?? false)) {
            $result = $this->completion->completeForFeatureWithFallback('ai_search_chat', $messages, $maxTokens, null, false);
        }

        if (! ($result['ok'] ?? false)) {
            Log::debug('ai_search_chat.failed', ['term' => $term, 'error' => $result['error'] ?? 'unknown']);

            $decoded = $this->fallbackReply($message, $term, $catalog, count($history));
            if ($decoded === null) {
                return ['ok' => false, 'error' => 'Khub AI is temporarily unavailable. Please try again shortly.'];
            }
        } else {
            $decoded = $this->decodePayload((string) ($result['content'] ?? ''));
            if ($decoded === null || trim((string) ($decoded['reply'] ?? '')) === '') {
                $decoded = $this->fallbackReply($message, $term, $catalog, count($history));
                if ($decoded === null) {
                    return ['ok' => false, 'error' => 'Khub AI returned an empty response. Please rephrase your question.'];
                }
            }
        }
        $reply = trim((string) ($decoded['reply'] ?? ''));
        if ($reply === '') {
            return ['ok' => false, 'error' => 'Khub AI returned an empty response. Please rephrase your question.'];
        }

        if ($this->isRepetitiveReply($reply, $history)) {
            $alternate = $this->fallbackReply($message, $term, $catalog, count($history) + 1);
            if ($alternate !== null && trim((string) ($alternate['reply'] ?? '')) !== '') {
                $decoded = $alternate;
                $reply = trim((string) ($decoded['reply'] ?? ''));
            }
        }

        $documents = $this->insightsService->resolveDocumentsFromIds(
            $catalog,
            (array) ($decoded['publication_ids'] ?? []),
            (array) ($decoded['forum_ids'] ?? []),
            (array) ($decoded['community_ids'] ?? []),
            (array) ($decoded['indicator_ids'] ?? [])
        );

        $history[] = ['user' => $message, 'assistant' => Str::limit($reply, 1500)];
        $history = array_slice($history, -self::MAX_HISTORY);
        $this->storeHistory($conversationId, $fingerprint, $history);

        return [
            'ok' => true,
            'conversation_id' => $conversationId,
            'reply' => Str::limit($reply, in_array($intent, ['analyze', 'summarize', 'compare'], true) ? 1800 : 1500),
            'documents' => $documents,
        ];
    }

    public function resetConversation(?string $conversationId): void
    {
        $conversationId = $this->normalizeConversationId($conversationId);
        SearchCache::store()->forget($this->historyKey($conversationId));
    }

    private function normalizeConversationId(?string $conversationId): string
    {
        $conversationId = trim((string) $conversationId);
        if ($conversationId !== '' && preg_match('/^[a-zA-Z0-9\-_]{8,64}$/', $conversationId) === 1) {
            return $conversationId;
        }

        return (string) Str::uuid();
    }

    /**
     * @return list<array{user: string, assistant: string}>
     */
    private function loadHistory(string $conversationId, string $fingerprint): array
    {
        $stored = SearchCache::store()->get($this->historyKey($conversationId));
        if (! is_array($stored) || ($stored['fingerprint'] ?? '') !== $fingerprint) {
            return [];
        }

        $history = $stored['messages'] ?? [];

        return is_array($history) ? $history : [];
    }

    /**
     * @param  list<array{user: string, assistant: string}>  $history
     */
    private function storeHistory(string $conversationId, string $fingerprint, array $history): void
    {
        SearchCache::store()->put(
            $this->historyKey($conversationId),
            ['fingerprint' => $fingerprint, 'messages' => $history],
            MetricsCache::ttl('ai_search_chat')
        );
    }

    private function historyKey(string $conversationId): string
    {
        return 'ai_search_chat:v'.SearchCache::aiInsightsVersion().':'.$conversationId;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodePayload(string $content): ?array
    {
        $content = trim($content);
        if ($content === '') {
            return null;
        }

        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $content, $matches) === 1) {
            $decoded = json_decode((string) ($matches[1] ?? ''), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($content, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return ['reply' => $content];
    }

    /**
     * @param  array<string, mixed>  $catalog
     */
    private function hasChatCatalogContent(array $catalog): bool
    {
        foreach (['publications', 'forums', 'communities', 'health_topics', 'indicators', 'partner_hub_publications'] as $key) {
            if (! empty($catalog[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>
     */
    private function compactCatalogForChat(array $catalog, string $intent = 'general'): array
    {
        $richIntent = in_array($intent, ['analyze', 'summarize', 'compare'], true);
        $textLimit = $richIntent ? 900 : 320;

        return [
            'publications' => $this->compactPublicationRows((array) ($catalog['publications'] ?? []), $textLimit),
            'forums' => $this->compactForumRows((array) ($catalog['forums'] ?? []), $richIntent ? 500 : 320),
            'communities' => $this->compactCommunityRows((array) ($catalog['communities'] ?? [])),
            'partner_hub_publications' => $this->compactPublicationRows((array) ($catalog['partner_hub_publications'] ?? []), $textLimit),
            'health_topics' => array_slice((array) ($catalog['health_topics'] ?? []), 0, 6),
            'member_state_indicators' => array_slice((array) ($catalog['indicators'] ?? []), 0, 6),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function compactPublicationRows(array $rows, int $textLimit = 320): array
    {
        $compact = [];
        foreach (array_slice($rows, 0, 10) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $body = trim((string) ($row['description'] ?? $row['excerpt'] ?? ''));
            $compact[] = [
                'id' => (int) ($row['id'] ?? 0),
                'title' => Str::limit((string) ($row['title'] ?? ''), 160),
                'excerpt' => Str::limit($body, $textLimit),
                'theme' => (string) ($row['theme'] ?? ''),
                'category' => (string) ($row['category'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return $compact;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function compactForumRows(array $rows, int $textLimit = 320): array
    {
        $compact = [];
        foreach (array_slice($rows, 0, 6) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $compact[] = [
                'id' => (int) ($row['id'] ?? 0),
                'title' => Str::limit((string) ($row['title'] ?? ''), 160),
                'excerpt' => Str::limit((string) ($row['excerpt'] ?? $row['description'] ?? ''), $textLimit),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return $compact;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function compactCommunityRows(array $rows): array
    {
        $compact = [];
        foreach (array_slice($rows, 0, 4) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $compact[] = [
                'id' => (int) ($row['id'] ?? 0),
                'name' => Str::limit((string) ($row['name'] ?? ''), 120),
                'excerpt' => Str::limit((string) ($row['excerpt'] ?? ''), 240),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return $compact;
    }

    /**
     * @param  array<string, mixed>  $compactCatalog
     */
    private function buildUserTurnContent(string $term, string $message, array $compactCatalog): string
    {
        $lines = [
            'Search query: '.$term,
            'User question: '.$message,
            '',
            'Hub catalog (JSON):',
            json_encode($compactCatalog, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
        ];

        return implode("\n", $lines);
    }

    /**
     * @return list<string>
     */
    private function extractMessageKeywords(string $text): array
    {
        $text = mb_strtolower(trim($text));
        if ($text === '') {
            return [];
        }

        $stopWords = [
            'about', 'after', 'also', 'and', 'are', 'ask', 'can', 'could', 'does', 'for', 'from',
            'have', 'help', 'how', 'into', 'just', 'know', 'like', 'more', 'most', 'need', 'please',
            'related', 'show', 'some', 'tell', 'that', 'the', 'their', 'them', 'then', 'there',
            'these', 'they', 'this', 'those', 'very', 'what', 'when', 'where', 'which', 'who',
            'why', 'with', 'would', 'your', 'you', 'any', 'all', 'our', 'was', 'were', 'will',
        ];

        $parts = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $keywords = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === 'npack') {
                $part = 'unpack';
            }
            if ($part === '' || mb_strlen($part) < 3 || in_array($part, $stopWords, true)) {
                continue;
            }
            $keywords[] = $part;
        }

        return array_values(array_unique($keywords));
    }

    private function detectMessageIntent(string $message): string
    {
        $text = mb_strtolower(trim($message));

        if (preg_match('/\b(unpack|un\s*pack|npack|explain|analyze|analyse|analysis|break\s*down|breakdown|walk me through|deep dive|help me understand|interpret|clarify|what does .+ mean|what is .+ about|tell me more about)\b/u', $text) === 1) {
            return 'analyze';
        }
        if (preg_match('/\b(summarize|summary|summarise|overview|main theme|main themes|key theme|key themes|takeaway|takeaways)\b/u', $text) === 1) {
            return 'summarize';
        }
        if (preg_match('/\b(compare|comparison|difference|differences|versus|vs\.?|contrast)\b/u', $text) === 1) {
            return 'compare';
        }
        if (preg_match('/\b(policy|policies|guidance|guideline|guidelines|regulation|regulations|framework|standard|standards)\b/u', $text) === 1) {
            return 'policy';
        }
        if (preg_match('/\b(first|top|best|recommend|recommended|start with|should i read)\b/u', $text) === 1) {
            return 'recommend';
        }
        if (preg_match('/\b(list|show|which one|which ones)\b/u', $text) === 1
            && preg_match('/\b(document|documents|resource|resources|publication|publications|paper|papers|report|reports)\b/u', $text) === 1) {
            return 'list_documents';
        }

        return 'general';
    }

    /**
     * @param  list<array{user: string, assistant: string}>  $history
     */
    private function isRepetitiveReply(string $reply, array $history): bool
    {
        if ($history === []) {
            return false;
        }

        $last = (string) ($history[array_key_last($history)]['assistant'] ?? '');
        if ($last === '') {
            return false;
        }

        similar_text(mb_strtolower($reply), mb_strtolower($last), $percent);

        return $percent >= 72.0;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function rotateCatalogRows(array $rows, int $turnOffset, int $limit): array
    {
        if ($rows === []) {
            return [];
        }

        $limit = max(1, min($limit, count($rows)));
        if (count($rows) <= $limit) {
            return $rows;
        }

        $offset = $turnOffset % max(1, count($rows) - $limit + 1);

        return array_values(array_slice($rows, $offset, $limit));
    }

    /**
     * @param  list<string>  $keywords
     * @return list<string>
     */
    private function pickRelevantSentences(string $text, array $keywords, int $limit = 3): array
    {
        $text = trim($text);
        if ($text === '') {
            return [];
        }

        $sentences = preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [$text];
        $scored = [];
        foreach ($sentences as $index => $sentence) {
            $sentence = trim($sentence);
            if ($sentence === '' || mb_strlen($sentence) < 30) {
                continue;
            }
            $score = $this->scoreCatalogRow(['title' => '', 'excerpt' => $sentence], $keywords);
            $scored[] = ['sentence' => $sentence, 'score' => $score, 'index' => $index];
        }

        if ($scored === []) {
            return [Str::limit($text, 240)];
        }

        usort($scored, static function (array $a, array $b): int {
            $byScore = ($b['score'] <=> $a['score']);
            if ($byScore !== 0) {
                return $byScore;
            }

            return ($a['index'] <=> $b['index']);
        });

        return array_values(array_map(
            static fn (array $item): string => Str::limit($item['sentence'], 240),
            array_slice($scored, 0, $limit)
        ));
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<string>  $keywords
     */
    private function buildDocumentUnpackSection(array $row, array $keywords, int $sentenceLimit = 2): string
    {
        $title = trim((string) ($row['title'] ?? ''));
        if ($title === '') {
            return '';
        }

        $body = trim((string) ($row['description'] ?? $row['excerpt'] ?? ''));
        $sentences = $this->pickRelevantSentences($body, $keywords, $sentenceLimit);
        if ($sentences === []) {
            return $title;
        }

        return $title.': '.implode(' ', $sentences);
    }

    /**
     * @param  list<array<string, mixed>>  $publications
     * @param  list<string>  $keywords
     * @return list<string>
     */
    private function extractSharedThemes(array $publications, array $keywords): array
    {
        $terms = [];
        foreach ($publications as $row) {
            if (! is_array($row)) {
                continue;
            }
            $blob = mb_strtolower(((string) ($row['title'] ?? '')).' '.((string) ($row['theme'] ?? '')).' '.((string) ($row['excerpt'] ?? $row['description'] ?? '')));
            foreach (['mpox', 'monkeypox', 'vaccine', 'vaccination', 'systematic review', 'meta-analysis', 'africa', 'genomic', 'sequencing', 'epidemiology', 'clinical', 'policy', 'guidance'] as $needle) {
                if (str_contains($blob, $needle)) {
                    $terms[] = $needle;
                }
            }
        }

        $terms = array_values(array_unique($terms));

        return array_slice($terms, 0, 4);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<string>  $keywords
     */
    private function scoreCatalogRow(array $row, array $keywords): int
    {
        if ($keywords === []) {
            return 0;
        }

        $haystack = mb_strtolower(implode(' ', array_filter([
            (string) ($row['title'] ?? ''),
            (string) ($row['name'] ?? ''),
            (string) ($row['excerpt'] ?? $row['description'] ?? ''),
            (string) ($row['theme'] ?? ''),
            (string) ($row['category'] ?? ''),
        ])));

        $score = 0;
        foreach ($keywords as $keyword) {
            if ($keyword === '') {
                continue;
            }
            if (str_contains($haystack, $keyword)) {
                $score += 3;
            }
        }

        return $score;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function rankCatalogRows(array $rows, array $keywords, string $term): array
    {
        $searchKeywords = $this->extractMessageKeywords($term);
        $allKeywords = array_values(array_unique(array_merge($keywords, $searchKeywords)));

        $scored = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $score = $this->scoreCatalogRow($row, $allKeywords);
            $scored[] = ['row' => $row, 'score' => $score];
        }

        if ($scored === []) {
            return [];
        }

        usort($scored, static fn (array $a, array $b): int => ($b['score'] <=> $a['score']));

        $hasMatches = ($scored[0]['score'] ?? 0) > 0;
        if (! $hasMatches) {
            return array_map(static fn (array $item): array => $item['row'], array_slice($scored, 0, 5));
        }

        return array_values(array_map(
            static fn (array $item): array => $item['row'],
            array_filter($scored, static fn (array $item): bool => ($item['score'] ?? 0) > 0)
        ));
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>|null
     */
    private function fallbackReply(string $message, string $term, array $catalog, int $turnOffset = 0): ?array
    {
        $keywords = $this->extractMessageKeywords($message);
        $intent = $this->detectMessageIntent($message);
        $question = Str::limit(trim($message), 140);
        $text = mb_strtolower(trim($message));
        $singularDocument = preg_match('/\b(the|this|that|a)\s+(document|paper|report|resource)\b/u', $text) === 1;

        $publications = $this->rankCatalogRows((array) ($catalog['publications'] ?? []), $keywords, $term);
        $forums = $this->rankCatalogRows((array) ($catalog['forums'] ?? []), $keywords, $term);

        if ($intent === 'policy') {
            $publications = array_values(array_filter(
                $publications,
                static fn (array $row): bool => preg_match(
                    '/\b(policy|policies|guidance|guideline|regulation|framework|standard|protocol|strategy|plan)\b/i',
                    ((string) ($row['title'] ?? '')).' '.((string) ($row['excerpt'] ?? $row['description'] ?? ''))
                ) === 1
            ));
            if ($publications === []) {
                $publications = $this->rankCatalogRows((array) ($catalog['publications'] ?? []), ['policy', 'guidance'], $term);
            }
        }

        $publicationLimit = match ($intent) {
            'analyze' => $singularDocument ? 1 : 3,
            'list_documents' => 5,
            'summarize', 'compare' => 4,
            'policy', 'recommend' => 3,
            default => 3,
        };

        $publications = $this->rotateCatalogRows($publications, $turnOffset, max($publicationLimit, 3));

        $publicationIds = [];
        $forumIds = [];
        $communityIds = [];
        $indicatorIds = [];
        $sentences = [];

        if ($intent === 'analyze') {
            $sentences[] = $singularDocument
                ? 'Unpacking the most relevant Khub document for "'.$question.'":'
                : 'Unpacking the top matching Khub documents for "'.$question.'":';

            foreach (array_slice($publications, 0, $publicationLimit) as $index => $row) {
                $id = (int) ($row['id'] ?? 0);
                if ($id <= 0) {
                    continue;
                }
                $publicationIds[] = $id;
                $section = $this->buildDocumentUnpackSection($row, $keywords, $singularDocument ? 4 : 2);
                if ($section === '') {
                    continue;
                }
                $sentences[] = ($index + 1).') '.$section;
            }

            if (count($publicationIds) > 1) {
                $themes = $this->extractSharedThemes(array_slice($publications, 0, $publicationLimit), $keywords);
                if ($themes !== []) {
                    $sentences[] = 'Shared focus across these sources: '.implode(', ', $themes).'.';
                }
            }
        } else {
            $sentences[] = match ($intent) {
                'list_documents' => 'For your question "'.$question.'", these Khub publications look most relevant:',
                'summarize' => 'For "'.$question.'", the strongest themes across matching Khub publications are:',
                'policy' => 'For "'.$question.'", these policy or guidance resources stand out in your search results:',
                'compare' => 'For "'.$question.'", compare these highlighted Khub resources:',
                'recommend' => 'For "'.$question.'", start with these Khub resources:',
                default => 'For "'.$question.'", here is what stands out in your current Khub search results:',
            };

            $added = 0;
            foreach (array_slice($publications, 0, $publicationLimit) as $row) {
                $id = (int) ($row['id'] ?? 0);
                if ($id <= 0) {
                    continue;
                }
                $publicationIds[] = $id;
                $title = trim((string) ($row['title'] ?? ''));
                if ($title === '') {
                    continue;
                }

                if ($intent === 'summarize') {
                    $sentences[] = $this->buildDocumentUnpackSection($row, $keywords, 2);
                } elseif ($intent === 'compare') {
                    $theme = trim((string) ($row['theme'] ?? ''));
                    $sentences[] = $theme !== '' ? $title.' ('.$theme.')' : $title;
                } else {
                    $excerpt = Str::limit(trim((string) ($row['excerpt'] ?? $row['description'] ?? '')), 180);
                    $sentences[] = $excerpt !== '' ? $title.': '.$excerpt : $title;
                }
                $added++;
            }

            if ($intent !== 'policy' || $added < 2) {
                foreach (array_slice($forums, 0, 2) as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $id = (int) ($row['id'] ?? 0);
                    if ($id <= 0) {
                        continue;
                    }
                    $forumIds[] = $id;
                    $title = trim((string) ($row['title'] ?? ''));
                    if ($title !== '') {
                        $sentences[] = __('publications.search.forum_discussion').': '.$title;
                    }
                }
            }
        }

        if ($publicationIds === [] && $forumIds === []) {
            foreach (array_slice((array) ($catalog['health_topics'] ?? []), 0, 2) as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $name = trim((string) ($row['name'] ?? $row['title'] ?? ''));
                if ($name !== '') {
                    $sentences[] = __('publications.search.ai_fallback_point_topic', ['topic' => $name]);
                }
            }
        }

        if (count($sentences) <= 1) {
            return null;
        }

        $sentences[] = __('publications.search.ai_chat_fallback_hint');

        return [
            'reply' => Str::limit(implode(' ', $sentences), 1500),
            'publication_ids' => array_values(array_unique($publicationIds)),
            'forum_ids' => array_values(array_unique($forumIds)),
            'community_ids' => $communityIds,
            'indicator_ids' => $indicatorIds,
        ];
    }
}
