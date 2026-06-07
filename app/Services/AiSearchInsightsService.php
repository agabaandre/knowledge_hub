<?php

namespace App\Services;

use App\Models\Author;
use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\ThemeticArea;
use App\Repositories\PublicationsRepository;
use App\Support\AiConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiSearchInsightsService
{
    public function __construct(
        private PublicationsRepository $publicationsRepo,
        private AiInternetSearchService $internetSearch
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
        $healthTopicCatalog = $this->healthTopicCatalog($term);
        $themeCatalog = $this->themeCatalog($term);
        $subThemeCatalog = $this->subThemeCatalog($term);
        $authorCatalog = $this->authorCatalog($term, $publicationCatalog);
        $internetResults = $this->internetSearch->search($term, 3);

        if ($publicationCatalog === [] && $forumCatalog === [] && $communityCatalog === [] && $federatedCatalog === []
            && $healthTopicCatalog === [] && $internetResults === []) {
            return null;
        }

        $totalPublications = method_exists($publicationsPaginator, 'total')
            ? (int) $publicationsPaginator->total()
            : count($publicationCatalog);

        $system = 'You are Khub AI on the Africa CDC Knowledge Hub search results page. '
            .'Write a brief, helpful overview (like a search engine AI overview) for the user query. '
            .'Use ONLY the catalog data provided from this platform. Do not invent hub resources. '
            .'Health topics, thematic areas, sub-themes, and contributors are provided for context—prefer citing them when relevant. '
            .'Internet search results are provided separately for external context; you may reference them but do not treat them as on-platform resources. '
            .'Never include private contact details (emails, phone numbers, postal addresses). '
            .'Keep overview under 80 words and key_points to 3 short bullets. '
            .'Return strict JSON with keys: overview (string), key_points (array of strings), '
            .'featured_publication_ids (array of int, max 3 from catalog), featured_forum_ids (array of int, max 2), '
            .'featured_community_ids (array of int, max 2), featured_health_topic_ids (array of int, max 3), '
            .'external_resources (array of {title, url, note} max 3).';

        $userPayload = [
            'query' => $term,
            'total_publications_matching' => $totalPublications,
            'publications' => $publicationCatalog,
            'forums' => $forumCatalog,
            'communities' => $communityCatalog,
            'partner_hub_publications' => $federatedCatalog,
            'health_topics' => $healthTopicCatalog,
            'thematic_areas' => $themeCatalog,
            'sub_thematic_areas' => $subThemeCatalog,
            'contributors' => $authorCatalog,
            'internet_results' => $internetResults,
        ];

        $result = app(AiCompletionService::class)->completeForFeature('ai_search', [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)],
        ], 700, null, true);

        if (! ($result['ok'] ?? false)) {
            return $this->fallbackInsights($internetResults, $healthTopicCatalog, $publicationCatalog, $forumCatalog, $communityCatalog);
        }

        $decoded = json_decode((string) ($result['content'] ?? ''), true);
        if (! is_array($decoded) || trim((string) ($decoded['overview'] ?? '')) === '') {
            return $this->fallbackInsights($internetResults, $healthTopicCatalog, $publicationCatalog, $forumCatalog, $communityCatalog);
        }

        return $this->normalizeInsights(
            $decoded,
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $healthTopicCatalog,
            $internetResults,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog
        );
    }

    /**
     * @param  list<array<string, mixed>>  $internetResults
     * @param  list<array<string, mixed>>  $healthTopicCatalog
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     * @param  list<array<string, mixed>>  $communityCatalog
     * @return array<string, mixed>|null
     */
    private function fallbackInsights(
        array $internetResults,
        array $healthTopicCatalog,
        array $publicationCatalog,
        array $forumCatalog,
        array $communityCatalog
    ): ?array {
        if ($internetResults === [] && $healthTopicCatalog === [] && $publicationCatalog === []) {
            return null;
        }

        return $this->normalizeInsights(
            ['overview' => '', 'key_points' => [], 'featured_publication_ids' => [], 'featured_forum_ids' => [], 'featured_community_ids' => [], 'featured_health_topic_ids' => [], 'external_resources' => []],
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $healthTopicCatalog,
            $internetResults,
            [],
            [],
            []
        );
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
            $author = $pub->author;
            $catalog[] = [
                'id' => (int) $pub->id,
                'title' => Str::limit(strip_tags((string) ($pub->title ?? '')), 160),
                'excerpt' => Str::limit(trim($desc), 220),
                'category' => optional($pub->data_category)->name ?? optional($pub->category)->category_name ?? null,
                'theme' => optional($pub->sub_theme)->description ?? optional($pub->theme)->description ?? null,
                'thematic_area_id' => (int) (optional($pub->sub_theme)->thematic_area_id ?? optional($pub->theme)->id ?? 0) ?: null,
                'sub_thematic_area_id' => (int) ($pub->sub_thematic_area_id ?? 0) ?: null,
                'author_id' => $author ? (int) $author->id : null,
                'author_name' => $author ? Str::limit((string) $author->name, 80) : null,
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
     * @return list<array<string, mixed>>
     */
    private function healthTopicCatalog(string $term): array
    {
        $query = Tag::query();
        if (Schema::hasColumn('tags', 'is_health_topic')) {
            $query->where('is_health_topic', true);
        } elseif (Schema::hasColumn('tags', 'is_health_emergency')) {
            $query->where('is_health_emergency', true);
        }

        $like = '%'.$term.'%';
        $query->where(function ($q) use ($like) {
            $q->where('tag_text', 'like', $like);
            if (Schema::hasColumn('tags', 'overview')) {
                $q->orWhere('overview', 'like', $like);
            }
        })
            ->orderBy('tag_text')
            ->limit(10);

        $catalog = [];
        foreach ($query->get() as $tag) {
            if (! $tag instanceof Tag) {
                continue;
            }
            $overview = Schema::hasColumn('tags', 'overview')
                ? strip_tags((string) ($tag->overview ?? ''))
                : '';
            $catalog[] = [
                'id' => (int) $tag->id,
                'name' => Str::limit((string) ($tag->tag_text ?? ''), 100),
                'overview' => Str::limit(html_entity_decode($overview, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 200),
                'url' => health_topic_url($tag),
            ];
        }

        return $catalog;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function themeCatalog(string $term): array
    {
        $like = '%'.$term.'%';

        return ThemeticArea::query()
            ->where('description', 'like', $like)
            ->orderBy('description')
            ->limit(12)
            ->get(['id', 'description'])
            ->map(function ($theme) {
                return [
                    'id' => (int) $theme->id,
                    'name' => Str::limit(strip_tags((string) ($theme->description ?? '')), 120),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function subThemeCatalog(string $term): array
    {
        $like = '%'.$term.'%';

        return SubThemeticArea::query()
            ->with(['theme:id,description'])
            ->where(function ($q) use ($like) {
                $q->where('description', 'like', $like)
                    ->orWhereHas('theme', function ($themeQuery) use ($like) {
                        $themeQuery->where('description', 'like', $like);
                    });
            })
            ->orderBy('description')
            ->limit(15)
            ->get(['id', 'description', 'thematic_area_id'])
            ->map(function ($subTheme) {
                return [
                    'id' => (int) $subTheme->id,
                    'name' => Str::limit(strip_tags((string) ($subTheme->description ?? '')), 120),
                    'thematic_area_id' => (int) ($subTheme->thematic_area_id ?? 0) ?: null,
                    'thematic_area' => Str::limit(strip_tags((string) optional($subTheme->theme)->description), 100),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Public contributor metadata only — no email, phone, or address.
     *
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @return list<array<string, mixed>>
     */
    private function authorCatalog(string $term, array $publicationCatalog): array
    {
        $authorIds = [];
        foreach ($publicationCatalog as $row) {
            if (! empty($row['author_id'])) {
                $authorIds[(int) $row['author_id']] = true;
            }
        }

        $query = Author::query()
            ->select(['id', 'name', 'slug', 'orcid', 'is_organsiation'])
            ->withCount('publications')
            ->where(function ($q) use ($term, $authorIds) {
                $q->where('name', 'like', '%'.$term.'%');
                if ($authorIds !== []) {
                    $q->orWhereIn('id', array_keys($authorIds));
                }
            })
            ->orderBy('name')
            ->limit(12);

        $catalog = [];
        foreach ($query->get() as $author) {
            if (! $author instanceof Author) {
                continue;
            }
            $catalog[] = $this->sanitizeAuthorForAi($author);
        }

        return $catalog;
    }

    /**
     * @return array<string, mixed>
     */
    private function sanitizeAuthorForAi(Author $author): array
    {
        return [
            'id' => (int) $author->id,
            'name' => Str::limit((string) ($author->name ?? ''), 100),
            'is_organisation' => (bool) ($author->is_organsiation ?? false),
            'orcid' => $author->orcid ? (string) $author->orcid : null,
            'url' => author_publications_url($author),
            'publication_count' => (int) ($author->publications_count ?? 0),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     * @param  list<array<string, mixed>>  $communityCatalog
     * @param  list<array<string, mixed>>  $healthTopicCatalog
     * @param  list<array<string, mixed>>  $internetResults
     * @param  list<array<string, mixed>>  $themeCatalog
     * @param  list<array<string, mixed>>  $subThemeCatalog
     * @param  list<array<string, mixed>>  $authorCatalog
     * @return array<string, mixed>
     */
    private function normalizeInsights(
        array $decoded,
        array $publicationCatalog,
        array $forumCatalog,
        array $communityCatalog,
        array $healthTopicCatalog,
        array $internetResults,
        array $themeCatalog,
        array $subThemeCatalog,
        array $authorCatalog
    ): array {
        $pubById = collect($publicationCatalog)->keyBy('id');
        $forumById = collect($forumCatalog)->keyBy('id');
        $communityById = collect($communityCatalog)->keyBy('id');
        $healthTopicById = collect($healthTopicCatalog)->keyBy('id');

        $pickPublications = [];
        foreach ((array) ($decoded['featured_publication_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($pubById->has($id)) {
                $pickPublications[] = $pubById->get($id);
            }
        }
        if ($pickPublications === [] && $publicationCatalog !== []) {
            $pickPublications = array_slice($publicationCatalog, 0, 3);
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

        $pickHealthTopics = [];
        foreach ((array) ($decoded['featured_health_topic_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($healthTopicById->has($id)) {
                $pickHealthTopics[] = $healthTopicById->get($id);
            }
        }
        if ($pickHealthTopics === [] && $healthTopicCatalog !== []) {
            $pickHealthTopics = array_slice($healthTopicCatalog, 0, 3);
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

        $overview = Str::limit(trim((string) ($decoded['overview'] ?? '')), 500);
        if ($overview === '' && ($pickHealthTopics !== [] || $internetResults !== [] || $pickPublications !== [])) {
            $overview = __('publications.search.ai_overview_fallback');
        }

        return [
            'overview' => $overview,
            'key_points' => array_slice($keyPoints, 0, 4),
            'publications' => array_slice($pickPublications, 0, 3),
            'forums' => array_slice($pickForums, 0, 2),
            'communities' => array_slice($pickCommunities, 0, 2),
            'health_topics' => array_slice($pickHealthTopics, 0, 3),
            'internet_results' => array_slice($internetResults, 0, 3),
            'thematic_areas' => array_slice($themeCatalog, 0, 6),
            'sub_thematic_areas' => array_slice($subThemeCatalog, 0, 8),
            'contributors' => array_slice($authorCatalog, 0, 6),
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
