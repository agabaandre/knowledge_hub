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
            .'Answer follow-up questions using ONLY the catalog JSON (publication descriptions, forum excerpts, communities, indicators). '
            .'Read description and excerpt fields carefully before answering. '
            .'When the user asks for documents, policies, or evidence, cite specific catalog items by id. '
            .'Tone: professional public health brief. No markdown or HTML. '
            .'If the catalog lacks an answer, say so and suggest refining the search. '
            .'Never invent titles, URLs, or statistics. '
            .'Return strict JSON: {"reply":"string max 1200 chars","publication_ids":[int],"forum_ids":[int],"community_ids":[int],"indicator_ids":[int]}';

        $userPayload = [
            'search_query' => $term,
            'filters' => $catalog['filters'] ?? [],
            'catalog' => $compactCatalog,
            'user_message' => $message,
        ];

        $messages = [
            ['role' => 'system', 'content' => $system],
        ];

        foreach ($history as $turn) {
            $messages[] = ['role' => 'user', 'content' => (string) ($turn['user'] ?? '')];
            $messages[] = ['role' => 'assistant', 'content' => (string) ($turn['assistant'] ?? '')];
        }

        $messages[] = ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)];

        $result = $this->completion->completeForFeatureWithFallback('ai_search_chat', $messages, 1400, null, true);
        if (! ($result['ok'] ?? false)) {
            Log::debug('ai_search_chat.failed', ['term' => $term, 'error' => $result['error'] ?? 'unknown']);

            $decoded = $this->fallbackReply($message, $term, $catalog);
            if ($decoded === null) {
                return ['ok' => false, 'error' => 'Khub AI is temporarily unavailable. Please try again shortly.'];
            }
        } else {
            $decoded = $this->decodePayload((string) ($result['content'] ?? ''));
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
     * @param  array<string, mixed>  $catalog
     * @return array<string, mixed>|null
     */
    private function fallbackReply(string $message, string $term, array $catalog): ?array
    {
        $publicationIds = [];
        $forumIds = [];
        $communityIds = [];
        $indicatorIds = [];
        $sentences = [__('publications.search.ai_fallback_for_term', ['term' => $term])];

        foreach (array_slice((array) ($catalog['publications'] ?? []), 0, 3) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $publicationIds[] = $id;
            $title = trim((string) ($row['title'] ?? ''));
            $excerpt = Str::limit(trim((string) ($row['excerpt'] ?? $row['description'] ?? '')), 180);
            if ($title !== '') {
                $sentences[] = $excerpt !== '' ? $title.': '.$excerpt : $title;
            }
        }

        foreach (array_slice((array) ($catalog['forums'] ?? []), 0, 2) as $row) {
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

        foreach (array_slice((array) ($catalog['health_topics'] ?? []), 0, 2) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? $row['title'] ?? ''));
            if ($name !== '') {
                $sentences[] = __('publications.search.ai_fallback_point_topic', ['topic' => $name]);
            }
        }

        if (count($sentences) <= 1) {
            return null;
        }

        $sentences[] = __('publications.search.ai_chat_fallback_hint');

        return [
            'reply' => Str::limit(implode(' ', $sentences), 1200),
            'publication_ids' => $publicationIds,
            'forum_ids' => $forumIds,
            'community_ids' => $communityIds,
            'indicator_ids' => $indicatorIds,
        ];
    }
}
