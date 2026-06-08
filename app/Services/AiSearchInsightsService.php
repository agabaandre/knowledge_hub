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

        $cacheKey = 'ai_search_insights:v5:'.md5($term.'|'.$this->filterFingerprint($request));

        try {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && self::isDisplayable($cached)) {
                return $cached;
            }

            $built = $this->buildInsights(
                $request,
                $term,
                $publicationsPaginator,
                $searchForums,
                $searchCommunities,
                $federatedPublications
            );

            if (self::isDisplayable($built)) {
                Cache::put($cacheKey, $built, now()->addMinutes(15));
            }

            return $built;
        } catch (\Throwable $e) {
            Log::warning('ai_search_insights.failed', [
                'term' => $term,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>|null  $insights
     */
    public static function isDisplayable(?array $insights): bool
    {
        if ($insights === null || $insights === []) {
            return false;
        }

        foreach ([
            'overview', 'key_takeaways', 'key_points', 'publications', 'forums', 'communities',
            'health_topics', 'scholarly_sources', 'internet_results', 'external_resources',
            'thematic_areas', 'sub_thematic_areas', 'contributors',
        ] as $key) {
            if (! empty($insights[$key])) {
                return true;
            }
        }

        return false;
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
        $internetResults = $this->fetchInternetResults($term);

        if (! $this->hasSourceContent(
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $federatedCatalog,
            $healthTopicCatalog,
            $internetResults,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog
        )) {
            return null;
        }

        $totalPublications = method_exists($publicationsPaginator, 'total')
            ? (int) $publicationsPaginator->total()
            : count($publicationCatalog);

        $allowedSiteLabels = AiConfig::allowedSearchSiteLabels();

        $system = 'You are Khub AI, the search assistant for the Africa CDC Knowledge Hub—a professional public health knowledge platform for Africa. '
            .'Produce a concise research brief for the user query. Tone: authoritative, neutral, and precise—suitable for public health professionals and policymakers. '
            .'Avoid filler, marketing language, hedging, and first-person voice. Use plain text only (no markdown, HTML, or bullet characters in strings). '
            .'Use ONLY the catalog data provided for on-platform resources. Never invent titles, URLs, counts, or authors. '
            .'Health topics, thematic areas, sub-themes, and contributors are context—reference them when directly relevant. '
            .'Internet search results come from '.$allowedSiteLabels.'; cite them only as external scholarly evidence, not as Khub resources. '
            .'For external_resources, use '.$allowedSiteLabels.' links only—never suggest WHO, Africa CDC, or other general websites. '
            .'Never include private contact details (emails, phone numbers, postal addresses). '
            .'overview: exactly 2 sentences, max 65 words. Sentence 1 defines the topic in public-health terms (Africa context when appropriate). '
            .'Sentence 2 states what Khub holds for this query using total_publications_matching when > 0; do not list individual resource titles. '
            .'key_points: exactly 3 objects {text, source_url}. text: 8-14 words, strong keyword first. '
            .'source_url: pick the best matching URL from internet_results for that point; required when the point cites external evidence. '
            .'Do not repeat the same source_url across key_points when alternatives exist. '
            .'Leave external_resources empty—internet_results already lists scholarly sources. '
            .'Return strict JSON with keys: overview (string), key_points (array of 3 {text, source_url}), '
            .'featured_publication_ids (array of int, max 3 from catalog), featured_forum_ids (array of int, max 2), '
            .'featured_community_ids (array of int, max 2), featured_health_topic_ids (array of int, max 3), '
            .'external_resources (array, usually empty).';

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

        $aiProvider = AiConfig::resolveChatProviderForFeature('ai_search');
        if ($aiProvider !== null) {
            $result = app(AiCompletionService::class)->completeForFeature('ai_search', [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)],
            ], 900, null, true);

            if ($result['ok'] ?? false) {
                $decoded = $this->decodeAiInsightsPayload((string) ($result['content'] ?? ''));
                if (is_array($decoded) && $decoded !== []) {
                    $normalized = $this->normalizeInsights(
                        $decoded,
                        $publicationCatalog,
                        $forumCatalog,
                        $communityCatalog,
                        $healthTopicCatalog,
                        $internetResults,
                        $themeCatalog,
                        $subThemeCatalog,
                        $authorCatalog,
                        $term,
                        $totalPublications
                    );

                    if (self::isDisplayable($normalized)) {
                        return $normalized;
                    }
                }
            } else {
                Log::debug('ai_search_insights.ai_unavailable', [
                    'term' => $term,
                    'error' => $result['error'] ?? 'unknown',
                ]);
            }
        } else {
            Log::debug('ai_search_insights.no_provider', ['term' => $term]);
        }

        return $this->fallbackInsights(
            $term,
            $totalPublications,
            $internetResults,
            $healthTopicCatalog,
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog
        );
    }

    /**
     * @return list<array{title: string, url: string, snippet: string, source: string}>
     */
    private function fetchInternetResults(string $term): array
    {
        try {
            return $this->internetSearch->search($term, 3);
        } catch (\Throwable $e) {
            Log::debug('ai_search_insights.internet_failed', [
                'term' => $term,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     * @param  list<array<string, mixed>>  $communityCatalog
     * @param  list<array<string, mixed>>  $federatedCatalog
     * @param  list<array<string, mixed>>  $healthTopicCatalog
     * @param  list<array<string, mixed>>  $internetResults
     * @param  list<array<string, mixed>>  $themeCatalog
     * @param  list<array<string, mixed>>  $subThemeCatalog
     * @param  list<array<string, mixed>>  $authorCatalog
     */
    private function hasSourceContent(
        array $publicationCatalog,
        array $forumCatalog,
        array $communityCatalog,
        array $federatedCatalog,
        array $healthTopicCatalog,
        array $internetResults,
        array $themeCatalog = [],
        array $subThemeCatalog = [],
        array $authorCatalog = []
    ): bool {
        return $publicationCatalog !== []
            || $forumCatalog !== []
            || $communityCatalog !== []
            || $federatedCatalog !== []
            || $healthTopicCatalog !== []
            || $internetResults !== []
            || $themeCatalog !== []
            || $subThemeCatalog !== []
            || $authorCatalog !== [];
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
        string $term,
        int $totalPublications,
        array $internetResults,
        array $healthTopicCatalog,
        array $publicationCatalog,
        array $forumCatalog,
        array $communityCatalog,
        array $themeCatalog = [],
        array $subThemeCatalog = [],
        array $authorCatalog = []
    ): ?array {
        if (! $this->hasSourceContent(
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            [],
            $healthTopicCatalog,
            $internetResults,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog
        )) {
            return null;
        }

        $normalized = $this->normalizeInsights(
            ['overview' => '', 'key_points' => [], 'featured_publication_ids' => [], 'featured_forum_ids' => [], 'featured_community_ids' => [], 'featured_health_topic_ids' => [], 'external_resources' => []],
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $healthTopicCatalog,
            $internetResults,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog,
            $term,
            $totalPublications
        );

        return self::isDisplayable($normalized) ? $normalized : null;
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
            $rawOverview = Schema::hasColumn('tags', 'overview')
                ? (string) ($tag->overview ?? '')
                : '';
            $catalog[] = [
                'id' => (int) $tag->id,
                'name' => Str::limit((string) ($tag->tag_text ?? ''), 100),
                'overview' => plain_text_excerpt_from_html($rawOverview, 200),
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
        array $authorCatalog,
        string $term = '',
        int $totalPublications = 0
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
        if ($pickForums === [] && $forumCatalog !== []) {
            $pickForums = array_slice($forumCatalog, 0, 2);
        }

        $pickCommunities = [];
        foreach ((array) ($decoded['featured_community_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($communityById->has($id)) {
                $pickCommunities[] = $communityById->get($id);
            }
        }
        if ($pickCommunities === [] && $communityCatalog !== []) {
            $pickCommunities = array_slice($communityCatalog, 0, 2);
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
            if ($url === '' || ! preg_match('#^https?://#i', $url) || ! $this->isAllowedExternalUrl($url)) {
                continue;
            }
            $external[] = [
                'title' => Str::limit(trim((string) ($row['title'] ?? 'Resource')), 100),
                'url' => $url,
                'note' => Str::limit(trim((string) ($row['note'] ?? '')), 120),
            ];
        }

        $scholarlySources = $this->mergeScholarlySources($internetResults, $external);

        $overview = Str::limit(trim((string) ($decoded['overview'] ?? '')), 480);
        $hasHubContent = $pickHealthTopics !== []
            || $pickPublications !== []
            || $pickForums !== []
            || $pickCommunities !== [];
        $hasDisplayableContent = $hasHubContent
            || $scholarlySources !== []
            || $themeCatalog !== []
            || $subThemeCatalog !== []
            || $authorCatalog !== [];

        if (trim($overview) === '' && $hasDisplayableContent) {
            $overview = $this->composeFallbackOverview(
                $term,
                $totalPublications,
                $pickHealthTopics,
                $pickPublications,
                $pickForums
            );
        }

        $keyTakeaways = $this->buildKeyTakeaways((array) ($decoded['key_points'] ?? []), $scholarlySources);

        if ($keyTakeaways === [] && $hasDisplayableContent) {
            $keyTakeaways = $this->composeFallbackKeyTakeaways(
                $term,
                $totalPublications,
                $pickHealthTopics,
                $pickPublications,
                $pickForums,
                $pickCommunities,
                $scholarlySources
            );
        }

        $keyPoints = array_values(array_map(
            fn (array $takeaway) => (string) ($takeaway['text'] ?? ''),
            $keyTakeaways
        ));

        $healthTopics = array_map(function (array $topic): array {
            if (! empty($topic['overview'])) {
                $topic['overview'] = plain_text_excerpt_from_html((string) $topic['overview'], 200);
            }

            return $topic;
        }, array_slice($pickHealthTopics, 0, 3));

        return [
            'query' => $term,
            'hub_matches' => $totalPublications,
            'overview' => $overview,
            'key_takeaways' => array_slice($keyTakeaways, 0, 3),
            'key_points' => array_slice($keyPoints, 0, 3),
            'publications' => array_slice($pickPublications, 0, 3),
            'forums' => array_slice($pickForums, 0, 2),
            'communities' => array_slice($pickCommunities, 0, 2),
            'health_topics' => $healthTopics,
            'scholarly_sources' => $scholarlySources,
            'internet_results' => $scholarlySources,
            'thematic_areas' => array_slice($themeCatalog, 0, 6),
            'sub_thematic_areas' => array_slice($subThemeCatalog, 0, 8),
            'contributors' => array_slice($authorCatalog, 0, 6),
            'external_resources' => [],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $internetResults
     * @param  list<array<string, mixed>>  $external
     * @return list<array{text: string, url: string, label: string, icon: string, snippet: string}>
     */
    private function mergeScholarlySources(array $internetResults, array $external): array
    {
        $merged = [];
        $seenUrls = [];
        $seenSourceIds = [];

        $candidates = $internetResults;
        foreach ($external as $row) {
            $candidates[] = [
                'title' => (string) ($row['title'] ?? 'Resource'),
                'url' => (string) ($row['url'] ?? ''),
                'snippet' => (string) ($row['note'] ?? ''),
                'source' => 'external',
                'label' => (string) ($row['title'] ?? 'External resource'),
                'icon' => 'fa-arrow-up-right-from-square',
            ];
        }

        foreach ($candidates as $row) {
            if (! is_array($row)) {
                continue;
            }

            $url = trim((string) ($row['url'] ?? ''));
            if ($url === '' || ! preg_match('#^https?://#i', $url)) {
                continue;
            }

            $urlKey = strtolower(rtrim($url, '/'));
            if (isset($seenUrls[$urlKey])) {
                continue;
            }

            $sourceId = strtolower(trim((string) ($row['source'] ?? '')));
            if ($sourceId !== '' && $sourceId !== 'external' && isset($seenSourceIds[$sourceId])) {
                continue;
            }

            $seenUrls[$urlKey] = true;
            if ($sourceId !== '' && $sourceId !== 'external') {
                $seenSourceIds[$sourceId] = true;
            }

            $merged[] = [
                'title' => Str::limit(trim((string) ($row['title'] ?? '')), 120),
                'url' => $url,
                'label' => Str::limit(trim((string) ($row['label'] ?? $row['title'] ?? 'Scholarly source')), 80),
                'icon' => (string) ($row['icon'] ?? 'fa-graduation-cap'),
                'snippet' => Str::limit(trim((string) ($row['snippet'] ?? '')), 160),
            ];
        }

        return array_slice($merged, 0, 4);
    }

    /**
     * @param  list<mixed>  $rawPoints
     * @param  list<array<string, mixed>>  $scholarlySources
     * @return list<array{text: string, url: string, label: string}>
     */
    private function buildKeyTakeaways(array $rawPoints, array $scholarlySources): array
    {
        $takeaways = [];
        $usedUrls = [];

        foreach ($rawPoints as $point) {
            $text = '';
            $url = '';

            if (is_array($point)) {
                $text = $this->cleanKeyPointText((string) ($point['text'] ?? $point['title'] ?? ''));
                $url = trim((string) ($point['source_url'] ?? $point['url'] ?? ''));
            } else {
                $text = $this->cleanKeyPointText((string) $point);
            }

            if ($text === '') {
                continue;
            }

            $linked = $this->resolveScholarlyLink($url, $scholarlySources, $usedUrls);
            if ($linked !== null) {
                $usedUrls[$linked['url_key']] = true;
            }

            $takeaways[] = [
                'text' => Str::limit($text, 160),
                'url' => $linked['url'] ?? '',
                'label' => $linked['label'] ?? '',
            ];
        }

        $sourceIndex = 0;
        foreach ($takeaways as &$takeaway) {
            if (($takeaway['url'] ?? '') !== '' || $scholarlySources === []) {
                continue;
            }

            while ($sourceIndex < count($scholarlySources)) {
                $candidate = $scholarlySources[$sourceIndex];
                $sourceIndex++;
                $urlKey = strtolower(rtrim((string) ($candidate['url'] ?? ''), '/'));
                if ($urlKey === '' || isset($usedUrls[$urlKey])) {
                    continue;
                }
                $usedUrls[$urlKey] = true;
                $takeaway['url'] = (string) $candidate['url'];
                $takeaway['label'] = (string) ($candidate['label'] ?? '');
                break;
            }
        }
        unset($takeaway);

        return $takeaways;
    }

    /**
     * @param  list<array<string, mixed>>  $scholarlySources
     * @param  array<string, bool>  $usedUrls
     * @return array{url: string, label: string, url_key: string}|null
     */
    private function resolveScholarlyLink(string $url, array $scholarlySources, array $usedUrls): ?array
    {
        if ($url !== '' && preg_match('#^https?://#i', $url) && $this->isAllowedExternalUrl($url)) {
            $urlKey = strtolower(rtrim($url, '/'));
            if (! isset($usedUrls[$urlKey])) {
                foreach ($scholarlySources as $source) {
                    if (strtolower(rtrim((string) ($source['url'] ?? ''), '/')) === $urlKey) {
                        return [
                            'url' => $url,
                            'label' => (string) ($source['label'] ?? ''),
                            'url_key' => $urlKey,
                        ];
                    }
                }

                return ['url' => $url, 'label' => '', 'url_key' => $urlKey];
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeAiInsightsPayload(string $content): ?array
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

        return null;
    }

    private function cleanKeyPointText(string $point): string
    {
        $point = trim($point);
        if ($point === '') {
            return '';
        }

        $cleaned = preg_replace('/^[\-\*\x{2022}\d\.\)]\s*/u', '', $point);

        return trim((string) ($cleaned ?? $point));
    }

    /**
     * @param  list<array<string, mixed>>  $healthTopics
     * @param  list<array<string, mixed>>  $publications
     * @param  list<array<string, mixed>>  $forums
     */
    private function composeFallbackOverview(
        string $term,
        int $totalPublications,
        array $healthTopics,
        array $publications,
        array $forums
    ): string {
        $sentences = [];

        $topicNames = collect($healthTopics)->pluck('name')->filter()->take(2)->values();
        if ($topicNames->isNotEmpty()) {
            $sentences[] = __('publications.search.ai_fallback_with_topics', [
                'topics' => $topicNames->implode(__('publications.search.ai_fallback_topic_joiner')),
            ]);
        } elseif ($term !== '') {
            $sentences[] = __('publications.search.ai_fallback_for_term', ['term' => $term]);
        }

        if ($totalPublications > 0) {
            $sentences[] = __('publications.search.ai_fallback_publication_count', [
                'count' => number_format($totalPublications),
            ]);
        } elseif ($publications !== [] || $forums !== []) {
            $sentences[] = __('publications.search.ai_fallback_curated_below');
        } else {
            $sentences[] = __('publications.search.ai_fallback_review_sources');
        }

        return Str::limit(implode(' ', $sentences), 480);
    }

    /**
     * @param  list<array<string, mixed>>  $healthTopics
     * @param  list<array<string, mixed>>  $publications
     * @param  list<array<string, mixed>>  $forums
     * @param  list<array<string, mixed>>  $communities
     * @param  list<array<string, mixed>>  $scholarlySources
     * @return list<array{text: string, url: string, label: string}>
     */
    private function composeFallbackKeyTakeaways(
        string $term,
        int $totalPublications,
        array $healthTopics,
        array $publications,
        array $forums,
        array $communities,
        array $scholarlySources
    ): array {
        $takeaways = [];

        foreach ($scholarlySources as $source) {
            $snippet = trim((string) ($source['snippet'] ?? ''));
            $text = $snippet !== ''
                ? Str::limit($snippet, 120)
                : Str::limit((string) ($source['title'] ?? __('publications.search.ai_fallback_point_scholarly')), 120);

            $takeaways[] = [
                'text' => $text,
                'url' => (string) ($source['url'] ?? ''),
                'label' => (string) ($source['label'] ?? ''),
            ];

            if (count($takeaways) >= 3) {
                return $takeaways;
            }
        }

        foreach (collect($healthTopics)->pluck('name')->filter()->take(2) as $name) {
            $takeaways[] = [
                'text' => __('publications.search.ai_fallback_point_topic', ['topic' => $name]),
                'url' => '',
                'label' => '',
            ];
        }

        if ($totalPublications > 0 && count($takeaways) < 3) {
            $takeaways[] = [
                'text' => __('publications.search.ai_fallback_point_publications', [
                    'count' => number_format($totalPublications),
                ]),
                'url' => '',
                'label' => '',
            ];
        }

        if ($forums !== [] && count($takeaways) < 3) {
            $takeaways[] = [
                'text' => __('publications.search.ai_fallback_point_forums', [
                    'count' => count($forums),
                ]),
                'url' => '',
                'label' => '',
            ];
        }

        if ($communities !== [] && count($takeaways) < 3) {
            $takeaways[] = [
                'text' => __('publications.search.ai_fallback_point_communities', [
                    'count' => count($communities),
                ]),
                'url' => '',
                'label' => '',
            ];
        }

        if ($takeaways === [] && $term !== '') {
            $takeaways[] = [
                'text' => __('publications.search.ai_fallback_point_explore', ['term' => $term]),
                'url' => '',
                'label' => '',
            ];
        }

        return array_slice($takeaways, 0, 3);
    }

    private function isAllowedExternalUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        if (str_contains($host, 'who.int')
            || str_contains($host, 'africacdc.org')
            || str_contains($host, 'africa-cdc')) {
            return false;
        }

        return AiConfig::urlMatchesAllowedSearchSite($url);
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
