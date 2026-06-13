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
            12
        );

        if (! $this->hasChatCatalogContent($catalog)) {
            return ['ok' => false, 'error' => 'No hub resources matched your search. Try different keywords or filters.'];
        }

        $compactCatalog = $this->compactCatalogForChat($catalog);

        $system = 'You are Khub AI, the Africa CDC Knowledge Hub search assistant. '
            .'Answer follow-up questions using ONLY the hub catalog provided in each user turn. '
            .'Read description and excerpt fields carefully before answering. '
            .'Directly address the user question; do not repeat the same generic summary for every follow-up. '
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

        $result = $this->completion->completeForFeatureWithFallback('ai_search_chat', $messages, 1400, null, true);
        if (! ($result['ok'] ?? false)) {
            $result = $this->completion->completeForFeatureWithFallback('ai_search_chat', $messages, 1400, null, false);
        }

        if (! ($result['ok'] ?? false)) {
            Log::debug('ai_search_chat.failed', ['term' => $term, 'error' => $result['error'] ?? 'unknown']);

            $decoded = $this->fallbackReply($message, $term, $catalog);
            if ($decoded === null) {
                return ['ok' => false, 'error' => 'Khub AI is temporarily unavailable. Please try again shortly.'];
            }
        } else {
            $decoded = $this->decodePayload((string) ($result['content'] ?? ''));
            if ($decoded === null || trim((string) ($decoded['reply'] ?? '')) === '') {
                $decoded = $this->fallbackReply($message, $term, $catalog);
                if ($decoded === null) {
                    return ['ok' => false, 'error' => 'Khub AI returned an empty response. Please rephrase your question.'];
                }
            }
        }
        $reply = trim((string) ($decoded['reply'] ?? ''));
        if ($reply === '') {
            return ['ok' => false, 'error' => 'Khub AI returned an empty response. Please rephrase your question.'];
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
            'reply' => Str::limit($reply, 1500),
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
    private function compactCatalogForChat(array $catalog): array
    {
        return [
            'publications' => $this->compactPublicationRows((array) ($catalog['publications'] ?? [])),
            'forums' => $this->compactForumRows((array) ($catalog['forums'] ?? [])),
            'communities' => $this->compactCommunityRows((array) ($catalog['communities'] ?? [])),
            'partner_hub_publications' => $this->compactPublicationRows((array) ($catalog['partner_hub_publications'] ?? [])),
            'health_topics' => array_slice((array) ($catalog['health_topics'] ?? []), 0, 6),
            'member_state_indicators' => array_slice((array) ($catalog['indicators'] ?? []), 0, 6),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function compactPublicationRows(array $rows): array
    {
        $compact = [];
        foreach (array_slice($rows, 0, 10) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $compact[] = [
                'id' => (int) ($row['id'] ?? 0),
                'title' => Str::limit((string) ($row['title'] ?? ''), 160),
                'excerpt' => Str::limit((string) ($row['excerpt'] ?? $row['description'] ?? ''), 320),
                'theme' => (string) ($row['theme'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return $compact;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function compactForumRows(array $rows): array
    {
        $compact = [];
        foreach (array_slice($rows, 0, 6) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $compact[] = [
                'id' => (int) ($row['id'] ?? 0),
                'title' => Str::limit((string) ($row['title'] ?? ''), 160),
                'excerpt' => Str::limit((string) ($row['excerpt'] ?? $row['description'] ?? ''), 320),
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

        if (preg_match('/\b(document|documents|resource|resources|publication|publications|paper|papers|report|reports|read|list|which one|which ones)\b/u', $text) === 1) {
            return 'list_documents';
        }
        if (preg_match('/\b(summarize|summary|summarise|overview|main theme|main themes|key theme|key themes|takeaway|takeaways)\b/u', $text) === 1) {
            return 'summarize';
        }
        if (preg_match('/\b(policy|policies|guidance|guideline|guidelines|regulation|regulations|framework|standard|standards)\b/u', $text) === 1) {
            return 'policy';
        }
        if (preg_match('/\b(compare|comparison|difference|differences|versus|vs\.?|contrast)\b/u', $text) === 1) {
            return 'compare';
        }
        if (preg_match('/\b(first|top|best|recommend|recommended|start with|should i read)\b/u', $text) === 1) {
            return 'recommend';
        }

        return 'general';
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
    private function fallbackReply(string $message, string $term, array $catalog): ?array
    {
        $keywords = $this->extractMessageKeywords($message);
        $intent = $this->detectMessageIntent($message);
        $question = Str::limit(trim($message), 140);

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
            'list_documents' => 5,
            'summarize', 'compare' => 4,
            'policy', 'recommend' => 3,
            default => 3,
        };

        $publicationIds = [];
        $forumIds = [];
        $communityIds = [];
        $indicatorIds = [];
        $sentences = [];

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
            $excerpt = Str::limit(trim((string) ($row['excerpt'] ?? $row['description'] ?? '')), 180);
            if ($title === '') {
                continue;
            }
            if ($intent === 'summarize' && $excerpt !== '') {
                $sentences[] = $title.': '.$excerpt;
            } elseif ($intent === 'compare') {
                $theme = trim((string) ($row['theme'] ?? ''));
                $sentences[] = $theme !== '' ? $title.' ('.$theme.')' : $title;
            } else {
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

        if ($added === 0 && $publicationIds === [] && $forumIds === []) {
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
            'reply' => Str::limit(implode(' ', $sentences), 1200),
            'publication_ids' => array_values(array_unique($publicationIds)),
            'forum_ids' => array_values(array_unique($forumIds)),
            'community_ids' => $communityIds,
            'indicator_ids' => $indicatorIds,
        ];
    }
}
