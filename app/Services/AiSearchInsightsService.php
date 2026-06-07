<?php

namespace App\Services;

use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\Publication;
use App\Repositories\PublicationsRepository;
use App\Support\AiConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiSearchInsightsService
{
    public function __construct(
        private PublicationsRepository $publicationsRepo
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function generate(
        Request $request,
        mixed $publicationsPaginator,
        Collection $searchForums,
        Collection $searchCommunities,
        Collection $federatedPublications = new Collection
    ): ?array {
        if (! (bool) (settings()->enable_ai_search ?? false)) {
            return null;
        }

        $term = trim((string) ($request->term ?? ''));
        if ($term === '' || mb_strlen($term) < 2) {
            return null;
        }

        if (AiConfig::resolveChatProviderForFeature('ai_search') === null) {
            return null;
        }

        $cacheKey = 'ai_search_insights:'.md5($term.'|'.$this->filterFingerprint($request));

        try {
            return Cache::remember($cacheKey, now()->addMinutes(15), function () use (
                $request,
                $term,
                $publicationsPaginator,
                $searchForums,
                $searchCommunities,
                $federatedPublications
            ) {
                return $this->buildInsights(
                    $request,
                    $term,
                    $publicationsPaginator,
                    $searchForums,
                    $searchCommunities,
                    $federatedPublications
                );
            });
        } catch (\Throwable $e) {
            Log::debug('ai_search_insights.failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildInsights(
        Request $request,
        string $term,
        mixed $publicationsPaginator,
        Collection $searchForums,
        Collection $searchCommunities,
        Collection $federatedPublications
    ): ?array {
        $publicationCatalog = $this->publicationCatalogForAi($request, 15);
        $forumCatalog = $this->forumCatalog($searchForums);
        $communityCatalog = $this->communityCatalog($searchCommunities);
        $federatedCatalog = $this->federatedPublicationCatalog($federatedPublications);

        if ($publicationCatalog === [] && $forumCatalog === [] && $communityCatalog === [] && $federatedCatalog === []) {
            return null;
        }

        $totalPublications = method_exists($publicationsPaginator, 'total')
            ? (int) $publicationsPaginator->total()
            : count($publicationCatalog);

        $system = 'You are Khub AI on the Africa CDC Knowledge Hub search results page. '
            .'Write a brief, helpful overview (like a search engine AI overview) for the user query. '
            .'Use ONLY the catalog data provided from this platform. Do not invent hub resources. '
            .'You may suggest up to 3 authoritative external websites (WHO, Africa CDC, peer-reviewed sources) when helpful. '
            .'Keep overview under 80 words and key_points to 3 short bullets. '
            .'Return strict JSON with keys: overview (string), key_points (array of strings), '
            .'featured_publication_ids (array of int, max 3 from catalog), featured_forum_ids (array of int, max 2), '
            .'featured_community_ids (array of int, max 2), external_resources (array of {title, url, note} max 3).';

        $userPayload = [
            'query' => $term,
            'total_publications_matching' => $totalPublications,
            'publications' => $publicationCatalog,
            'forums' => $forumCatalog,
            'communities' => $communityCatalog,
            'partner_hub_publications' => $federatedCatalog,
        ];

        $result = app(AiCompletionService::class)->completeForFeature('ai_search', [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)],
        ], 700, null, true);

        if (! ($result['ok'] ?? false)) {
            return null;
        }

        $decoded = json_decode((string) ($result['content'] ?? ''), true);
        if (! is_array($decoded) || trim((string) ($decoded['overview'] ?? '')) === '') {
            return null;
        }

        return $this->normalizeInsights($decoded, $publicationCatalog, $forumCatalog, $communityCatalog);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function publicationCatalogForAi(Request $request, int $limit): array
    {
        $aiRequest = clone $request;
        $aiRequest->merge(['rows' => $limit, 'page' => 1]);

        $rows = $this->publicationsRepo->get($aiRequest);
        $collection = method_exists($rows, 'getCollection') ? $rows->getCollection() : collect($rows);

        $catalog = [];
        foreach ($collection as $pub) {
            if (! $pub instanceof Publication) {
                continue;
            }
            $desc = strip_tags((string) ($pub->description ?? ''));
            $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $catalog[] = [
                'id' => (int) $pub->id,
                'title' => Str::limit(strip_tags((string) ($pub->title ?? '')), 160),
                'excerpt' => Str::limit(trim($desc), 220),
                'category' => optional($pub->data_category)->name ?? optional($pub->category)->category_name ?? null,
                'theme' => optional($pub->sub_theme)->description ?? optional($pub->theme)->description ?? null,
                'url' => publication_url($pub),
            ];
        }

        return $catalog;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function forumCatalog(Collection $forums): array
    {
        $catalog = [];
        foreach ($forums->take(8) as $forum) {
            if (! $forum instanceof Forum) {
                continue;
            }
            $desc = strip_tags((string) ($forum->forum_description ?? ''));
            $catalog[] = [
                'id' => (int) $forum->id,
                'title' => Str::limit(strip_tags((string) ($forum->forum_title ?? '')), 140),
                'excerpt' => Str::limit(html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 180),
                'comments' => (int) ($forum->total_comments ?? 0),
                'url' => forum_thread_url($forum),
            ];
        }

        return $catalog;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function communityCatalog(Collection $communities): array
    {
        $catalog = [];
        foreach ($communities->take(6) as $community) {
            if (! $community instanceof CommunityOfPractice) {
                continue;
            }
            $catalog[] = [
                'id' => (int) $community->id,
                'name' => Str::limit((string) ($community->community_name ?? ''), 120),
                'excerpt' => Str::limit(strip_tags((string) ($community->description ?? '')), 160),
                'url' => community_detail_url($community),
            ];
        }

        return $catalog;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function federatedPublicationCatalog(Collection $items): array
    {
        $catalog = [];
        foreach ($items->take(5) as $row) {
            $catalog[] = [
                'title' => Str::limit(strip_tags((string) ($row->title ?? '')), 140),
                'hub' => (string) ($row->federation_hub_name ?? 'Partner hub'),
                'url' => (string) ($row->federation_source_url ?? ''),
            ];
        }

        return $catalog;
    }

    /**
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     * @param  list<array<string, mixed>>  $communityCatalog
     * @return array<string, mixed>
     */
    private function normalizeInsights(array $decoded, array $publicationCatalog, array $forumCatalog, array $communityCatalog): array
    {
        $pubById = collect($publicationCatalog)->keyBy('id');
        $forumById = collect($forumCatalog)->keyBy('id');
        $communityById = collect($communityCatalog)->keyBy('id');

        $pickPublications = [];
        foreach ((array) ($decoded['featured_publication_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($pubById->has($id)) {
                $pickPublications[] = $pubById->get($id);
            }
        }

        $pickForums = [];
        foreach ((array) ($decoded['featured_forum_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($forumById->has($id)) {
                $pickForums[] = $forumById->get($id);
            }
        }

        $pickCommunities = [];
        foreach ((array) ($decoded['featured_community_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($communityById->has($id)) {
                $pickCommunities[] = $communityById->get($id);
            }
        }

        $external = [];
        foreach ((array) ($decoded['external_resources'] ?? []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $url = trim((string) ($row['url'] ?? ''));
            if ($url === '' || ! preg_match('#^https?://#i', $url)) {
                continue;
            }
            $external[] = [
                'title' => Str::limit(trim((string) ($row['title'] ?? 'Resource')), 100),
                'url' => $url,
                'note' => Str::limit(trim((string) ($row['note'] ?? '')), 120),
            ];
        }

        $keyPoints = [];
        foreach ((array) ($decoded['key_points'] ?? []) as $point) {
            $point = trim((string) $point);
            if ($point !== '') {
                $keyPoints[] = Str::limit($point, 200);
            }
        }

        return [
            'overview' => Str::limit(trim((string) ($decoded['overview'] ?? '')), 500),
            'key_points' => array_slice($keyPoints, 0, 4),
            'publications' => array_slice($pickPublications, 0, 3),
            'forums' => array_slice($pickForums, 0, 2),
            'communities' => array_slice($pickCommunities, 0, 2),
            'external_resources' => array_slice($external, 0, 3),
        ];
    }

    private function filterFingerprint(Request $request): string
    {
        $keys = [
            'thematic_area_id', 'sub_thematic_area_id', 'country_id', 'data_category_id',
            'author_id', 'file_type_id', 'rcc', 'tag',
        ];
        $parts = [];
        foreach ($keys as $key) {
            $parts[] = $key.'='.json_encode($request->input($key));
        }

        return implode(';', $parts);
    }
}
