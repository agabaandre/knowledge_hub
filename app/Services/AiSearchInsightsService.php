<?php

namespace App\Services;

use App\Models\Author;
use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\Kpi;
use App\Models\Publication;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\ThemeticArea;
use App\Repositories\GraphsRepository;
use App\Repositories\PublicationsRepository;
use App\Support\AiConfig;
use App\Support\MetricsCache;
use App\Support\SearchCache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AiSearchInsightsService
{
    public function __construct(
        private PublicationsRepository $publicationsRepo,
        private HybridRecordsSearchService $hybridRecordsSearch,
        private GraphsRepository $graphsRepo,
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

        $cacheKey = 'ai_search_insights:v'.SearchCache::aiInsightsVersion().':'.md5($term.'|'.$this->filterFingerprint($request));
        $cacheStore = SearchCache::store();

        try {
            $cached = $cacheStore->get($cacheKey);
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
                $cacheStore->put($cacheKey, $built, MetricsCache::ttl('ai_search'));
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
            'overview', 'key_points', 'publications', 'forums', 'communities',
            'health_topics', 'indicators', 'scholarly_sources', 'internet_results', 'external_resources',
            'thematic_areas', 'sub_thematic_areas', 'contributors', 'document_highlights',
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
        $indicatorCatalog = $this->indicatorCatalogForAi($term);
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
            $authorCatalog,
            $indicatorCatalog
        )) {
            return null;
        }

        $totalPublications = method_exists($publicationsPaginator, 'total')
            ? (int) $publicationsPaginator->total()
            : count($publicationCatalog);

        $allowedSiteLabels = AiConfig::allowedSearchSiteLabels();

        $system = 'You are Khub AI, the search assistant for the Africa CDC Knowledge Hub—a professional public health knowledge platform for Africa. '
            .'Produce a concise research brief for the user query. Read each publication description and forum excerpt in the catalog before summarizing—ground every claim in that text. '
            .'Tone: authoritative, neutral, and precise—suitable for public health professionals and policymakers. '
            .'Avoid filler, marketing language, hedging, and first-person voice. Use plain text only (no markdown, HTML, or bullet characters in strings). '
            .'Use ONLY the catalog data provided for on-platform resources. Never invent titles, URLs, counts, or authors. '
            .'Health topics, thematic areas, sub-themes, and contributors are context—reference them when directly relevant. '
            .'Internet search results come from '.$allowedSiteLabels.'; cite them only as external scholarly evidence, not as Khub resources. '
            .'For external_resources, use '.$allowedSiteLabels.' links only—never suggest WHO, Africa CDC, or other general websites. '
            .'Never include private contact details (emails, phone numbers, postal addresses). '
            .'overview: exactly 3 sentences, max 95 words. Sentence 1 defines the topic in public-health terms (Africa context when appropriate). '
            .'Sentence 2 synthesizes themes from publication descriptions and forum excerpts in the catalog. '
            .'Sentence 3 states what Khub holds for this query using total_publications_matching when > 0; do not list individual resource titles. '
            .'analysis_summary: 2-4 sentences (max 120 words) explaining how catalog descriptions relate to the query—mention specific themes, interventions, or evidence types found in excerpts. '
            .'key_points: exactly 3 objects {text, hub_url}. text: 8-14 words, directly about the search query and grounded in catalog descriptions. '
            .'hub_url: MUST be copied exactly from publications[].url, forums[].url, health_topics[].url, communities[].url, or member_state_indicators[].url in the catalog. Never use internet_results or external URLs. Use each hub URL at most once. '
            .'Return strict JSON with keys: overview (string), analysis_summary (string), key_points (array of 3 {text, hub_url}), '
            .'featured_publication_ids (array of int, max 3 from catalog), featured_forum_ids (array of int, max 2), '
            .'featured_community_ids (array of int, max 2), featured_health_topic_ids (array of int, max 3), '
            .'featured_indicator_ids (array of int, max 3 from member_state_indicators), '
            .'external_resources (array of {title, url, note} max 3).';

        $userPayload = [
            'query' => $term,
            'total_publications_matching' => $totalPublications,
            'publications' => $publicationCatalog,
            'forums' => $forumCatalog,
            'communities' => $communityCatalog,
            'partner_hub_publications' => $federatedCatalog,
            'health_topics' => $healthTopicCatalog,
            'member_state_indicators' => $indicatorCatalog,
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
                        $totalPublications,
                        $indicatorCatalog
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
            $authorCatalog,
            $indicatorCatalog
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
     * @param  list<array<string, mixed>>  $indicatorCatalog
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
        array $authorCatalog = [],
        array $indicatorCatalog = []
    ): bool {
        return $publicationCatalog !== []
            || $forumCatalog !== []
            || $communityCatalog !== []
            || $federatedCatalog !== []
            || $healthTopicCatalog !== []
            || $internetResults !== []
            || $themeCatalog !== []
            || $subThemeCatalog !== []
            || $authorCatalog !== []
            || $indicatorCatalog !== [];
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
        array $authorCatalog = [],
        array $indicatorCatalog = []
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
            $authorCatalog,
            $indicatorCatalog
        )) {
            return null;
        }

        $normalized = $this->normalizeInsights(
            ['overview' => '', 'key_points' => [], 'featured_publication_ids' => [], 'featured_forum_ids' => [], 'featured_community_ids' => [], 'featured_health_topic_ids' => [], 'featured_indicator_ids' => [], 'external_resources' => []],
            $publicationCatalog,
            $forumCatalog,
            $communityCatalog,
            $healthTopicCatalog,
            $internetResults,
            $themeCatalog,
            $subThemeCatalog,
            $authorCatalog,
            $term,
            $totalPublications,
            $indicatorCatalog
        );

        return self::isDisplayable($normalized) ? $normalized : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function publicationCatalogForAi(Request $request, int $limit): array
    {
        $collection = $this->hybridRecordsSearch->publicationsForAi($request, $limit);

        $catalog = [];
        foreach ($collection as $pub) {
            if (! $pub instanceof Publication) {
                continue;
            }
            $desc = $this->plainPublicationDescription($pub);
            $author = $pub->author;
            $catalog[] = [
                'id' => (int) $pub->id,
                'title' => Str::limit(strip_tags((string) ($pub->title ?? '')), 160),
                'description' => Str::limit($desc, 1200),
                'excerpt' => Str::limit($desc, 400),
                'associated_authors' => Str::limit(strip_tags((string) ($pub->associated_authors ?? '')), 120),
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
     * Published member-state indicators (OWID KPIs) matching the search term.
     *
     * @return list<array<string, mixed>>
     */
    private function indicatorCatalogForAi(string $term, int $limit = 8): array
    {
        $term = trim($term);
        if ($term === '' || mb_strlen($term) < 2) {
            return [];
        }

        $publishedIds = $this->graphsRepo->get_published_map_indicators()->pluck('id')->map(fn ($id) => (int) $id)->all();
        if ($publishedIds === []) {
            return [];
        }

        $like = '%'.$term.'%';
        $rows = Kpi::query()
            ->with('subjectArea:id,name')
            ->where('status', 'published')
            ->whereIn('id', $publishedIds)
            ->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('subjectArea', function ($subjectQuery) use ($like) {
                        $subjectQuery->where('name', 'like', $like);
                    });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $catalog = [];
        foreach ($rows as $kpi) {
            if (! $kpi instanceof Kpi) {
                continue;
            }

            $desc = strip_tags((string) ($kpi->description ?? ''));
            $desc = html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $catalog[] = [
                'id' => (int) $kpi->id,
                'name' => Str::limit(strip_tags((string) ($kpi->name ?? '')), 140),
                'excerpt' => Str::limit(trim($desc), 180),
                'subject_area' => Str::limit((string) optional($kpi->subjectArea)->name, 80),
                'unit_label' => Str::limit((string) ($kpi->unit_label ?? ''), 60),
                'url' => route('countries').'?kpi_id='.(int) $kpi->id,
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
            $desc = $this->plainForumDescription($forum);
            $catalog[] = [
                'id' => (int) $forum->id,
                'title' => Str::limit(strip_tags((string) ($forum->forum_title ?? '')), 140),
                'description' => Str::limit($desc, 800),
                'excerpt' => Str::limit($desc, 350),
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
     * @param  list<array<string, mixed>>  $indicatorCatalog
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
        int $totalPublications = 0,
        array $indicatorCatalog = []
    ): array {
        $pubById = collect($publicationCatalog)->keyBy('id');
        $forumById = collect($forumCatalog)->keyBy('id');
        $communityById = collect($communityCatalog)->keyBy('id');
        $healthTopicById = collect($healthTopicCatalog)->keyBy('id');
        $indicatorById = collect($indicatorCatalog)->keyBy('id');

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

        $pickIndicators = [];
        foreach ((array) ($decoded['featured_indicator_ids'] ?? []) as $id) {
            $id = (int) $id;
            if ($indicatorById->has($id)) {
                $pickIndicators[] = $indicatorById->get($id);
            }
        }
        if ($pickIndicators === [] && $indicatorCatalog !== []) {
            $pickIndicators = array_slice($indicatorCatalog, 0, 3);
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
            || $pickCommunities !== []
            || $pickIndicators !== [];
        $hasDisplayableContent = $hasHubContent
            || $scholarlySources !== []
            || $themeCatalog !== []
            || $subThemeCatalog !== []
            || $authorCatalog !== [];

        $hubLinkCatalog = $this->buildHubLinkCatalog(
            array_slice($pickHealthTopics, 0, 3),
            array_slice($pickPublications, 0, 3),
            array_slice($pickForums, 0, 2),
            array_slice($pickCommunities, 0, 2),
            array_slice($pickIndicators, 0, 3)
        );

        $keyPoints = $this->normalizeKeyPoints((array) ($decoded['key_points'] ?? []), $hubLinkCatalog);

        if (trim($overview) === '' && $hasDisplayableContent) {
            $overview = $this->composeFallbackOverview(
                $term,
                $totalPublications,
                $pickHealthTopics,
                $pickPublications,
                $pickForums
            );
        }

        $analysisSummary = Str::limit(trim((string) ($decoded['analysis_summary'] ?? '')), 800);
        if ($analysisSummary === '' && $hasDisplayableContent) {
            $analysisSummary = $this->composeFallbackAnalysisSummary($publicationCatalog, $forumCatalog, $term);
        }

        if ($keyPoints === [] && $hasDisplayableContent) {
            $keyPoints = $this->composeFallbackKeyPoints($hubLinkCatalog, $term);
        }

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
            'analysis_summary' => $analysisSummary,
            'key_points' => array_slice($keyPoints, 0, 3),
            'publications' => array_slice($pickPublications, 0, 3),
            'forums' => array_slice($pickForums, 0, 2),
            'communities' => array_slice($pickCommunities, 0, 2),
            'health_topics' => $healthTopics,
            'indicators' => array_slice($pickIndicators, 0, 4),
            'document_highlights' => $this->buildDocumentHighlights($publicationCatalog, $forumCatalog),
            'scholarly_sources' => array_slice($scholarlySources, 0, 4),
            'internet_results' => array_slice($scholarlySources, 0, 4),
            'thematic_areas' => array_slice($themeCatalog, 0, 6),
            'sub_thematic_areas' => array_slice($subThemeCatalog, 0, 8),
            'contributors' => array_slice($authorCatalog, 0, 6),
            'external_resources' => [],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $internetResults
     * @param  list<array<string, mixed>>  $external
     * @return list<array<string, mixed>>
     */
    private function mergeScholarlySources(array $internetResults, array $external): array
    {
        $merged = [];
        $seenUrls = [];
        $seenTitles = [];

        foreach (array_merge($internetResults, $external) as $row) {
            if (! is_array($row)) {
                continue;
            }

            $url = trim((string) ($row['url'] ?? ''));
            if ($url === '' || ! preg_match('#^https?://#i', $url)) {
                continue;
            }

            $urlKey = $this->normalizeSourceUrlKey($url);
            $titleKey = mb_strtolower(trim((string) ($row['title'] ?? '')));
            if (isset($seenUrls[$urlKey]) || ($titleKey !== '' && isset($seenTitles[$titleKey]))) {
                continue;
            }

            $seenUrls[$urlKey] = true;
            if ($titleKey !== '') {
                $seenTitles[$titleKey] = true;
            }

            $merged[] = [
                'title' => Str::limit(trim((string) ($row['title'] ?? 'Resource')), 120),
                'url' => $url,
                'snippet' => Str::limit(trim((string) ($row['snippet'] ?? $row['note'] ?? '')), 180),
                'label' => trim((string) ($row['label'] ?? 'Resource')),
                'icon' => trim((string) ($row['icon'] ?? 'fa-graduation-cap')) ?: 'fa-graduation-cap',
                'source' => trim((string) ($row['source'] ?? 'external')),
                'is_portal' => $this->isPortalSearchUrl($url),
            ];
        }

        $articles = [];
        $portals = [];
        $hostsWithArticles = [];

        foreach ($merged as $item) {
            if ($item['is_portal']) {
                $portals[] = $item;

                continue;
            }

            $articles[] = $item;
            $hostsWithArticles[$this->sourceHost($item['url'])] = true;
        }

        $filtered = $articles;
        foreach ($portals as $portal) {
            if (! isset($hostsWithArticles[$this->sourceHost($portal['url'])])) {
                $filtered[] = $portal;
            }
        }

        return array_slice($filtered, 0, 6);
    }

    /**
     * @param  list<array<string, mixed>>  $healthTopics
     * @param  list<array<string, mixed>>  $publications
     * @param  list<array<string, mixed>>  $forums
     * @param  list<array<string, mixed>>  $communities
     * @param  list<array<string, mixed>>  $indicators
     * @return list<array{url: string, title: string, label: string, icon: string}>
     */
    private function buildHubLinkCatalog(
        array $healthTopics,
        array $publications,
        array $forums,
        array $communities,
        array $indicators = []
    ): array {
        $catalog = [];

        foreach ($healthTopics as $topic) {
            $url = trim((string) ($topic['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $catalog[] = [
                'url' => $url,
                'title' => Str::limit(trim((string) ($topic['name'] ?? '')), 120),
                'label' => __('publications.search.health_topics'),
                'icon' => 'fa-heart-pulse',
            ];
        }

        foreach ($publications as $publication) {
            $url = trim((string) ($publication['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $catalog[] = [
                'url' => $url,
                'title' => Str::limit(trim((string) ($publication['title'] ?? '')), 120),
                'label' => __('publications.publication'),
                'icon' => 'fa-file-lines',
            ];
        }

        foreach ($forums as $forum) {
            $url = trim((string) ($forum['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $catalog[] = [
                'url' => $url,
                'title' => Str::limit(trim((string) ($forum['title'] ?? '')), 120),
                'label' => __('publications.search.forum_discussion'),
                'icon' => 'fa-comments',
            ];
        }

        foreach ($communities as $community) {
            $url = trim((string) ($community['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $catalog[] = [
                'url' => $url,
                'title' => Str::limit(trim((string) ($community['name'] ?? '')), 120),
                'label' => __('publications.search.community'),
                'icon' => 'fa-users',
            ];
        }

        foreach ($indicators as $indicator) {
            $url = trim((string) ($indicator['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $catalog[] = [
                'url' => $url,
                'title' => Str::limit(trim((string) ($indicator['name'] ?? '')), 120),
                'label' => __('publications.search.member_state_indicator'),
                'icon' => 'fa-chart-column',
            ];
        }

        return $catalog;
    }

    /**
     * @param  list<mixed>  $raw
     * @param  list<array{url: string, title: string, label: string, icon: string}>  $hubSources
     * @return list<array{text: string, url: string|null, label: string|null, icon: string|null, external: bool}>
     */
    private function normalizeKeyPoints(array $raw, array $hubSources): array
    {
        $points = [];
        $usedUrls = [];

        foreach ($raw as $item) {
            $text = '';
            $hubUrl = '';

            if (is_string($item)) {
                $text = $this->cleanKeyPointText($item);
            } elseif (is_array($item)) {
                $text = $this->cleanKeyPointText((string) ($item['text'] ?? $item['point'] ?? ''));
                $hubUrl = trim((string) ($item['hub_url'] ?? $item['source_url'] ?? $item['url'] ?? ''));
            }

            if ($text === '') {
                continue;
            }

            $source = $this->findHubSource($hubSources, $hubUrl, $usedUrls);
            if ($source !== null) {
                $usedUrls[(string) $source['url']] = true;
            }

            $points[] = [
                'text' => Str::limit($text, 160),
                'url' => $source['url'] ?? null,
                'label' => $source['label'] ?? null,
                'icon' => $source['icon'] ?? null,
                'external' => false,
            ];

            if (count($points) >= 3) {
                break;
            }
        }

        return $points;
    }

    /**
     * @param  list<array{url: string, title: string, label: string, icon: string}>  $hubSources
     * @param  array<string, bool>  $usedUrls
     * @return array{url: string, title: string, label: string, icon: string}|null
     */
    private function findHubSource(array $hubSources, string $preferredUrl, array $usedUrls): ?array
    {
        if ($preferredUrl !== '') {
            foreach ($hubSources as $source) {
                if ($this->normalizeSourceUrlKey((string) $source['url']) === $this->normalizeSourceUrlKey($preferredUrl)) {
                    return $source;
                }
            }
        }

        foreach ($hubSources as $source) {
            $url = (string) ($source['url'] ?? '');
            if ($url !== '' && ! isset($usedUrls[$url])) {
                return $source;
            }
        }

        return null;
    }

    /**
     * @param  list<array{url: string, title: string, label: string, icon: string}>  $hubSources
     * @return list<array{text: string, url: string|null, label: string|null, icon: string|null, external: bool}>
     */
    private function composeFallbackKeyPoints(array $hubSources, string $term): array
    {
        $points = [];

        foreach (array_slice($hubSources, 0, 3) as $source) {
            $title = trim((string) ($source['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $points[] = [
                'text' => $title,
                'url' => $source['url'],
                'label' => $source['label'],
                'icon' => $source['icon'],
                'external' => false,
            ];
        }

        if ($points === [] && $term !== '') {
            $points[] = [
                'text' => __('publications.search.ai_fallback_point_explore', ['term' => $term]),
                'url' => null,
                'label' => null,
                'icon' => null,
                'external' => false,
            ];
        }

        return $points;
    }

    private function normalizeSourceUrlKey(string $url): string
    {
        $url = trim(strtolower($url));
        $url = rtrim($url, '/');
        $url = (string) preg_replace('/#.*$/', '', $url);
        $url = (string) preg_replace('/[?&](utm_[^=&]+|ref|source)=[^&]*/', '', $url);

        return rtrim($url, '?&');
    }

    private function sourceHost(string $url): string
    {
        return strtolower((string) parse_url($url, PHP_URL_HOST));
    }

    private function isPortalSearchUrl(string $url): bool
    {
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        $query = strtolower((string) parse_url($url, PHP_URL_QUERY));

        return str_contains($path, '/search')
            || str_contains($query, 'term=')
            || str_contains($query, 'simplequery=')
            || str_contains($query, 'q=');
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
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     */
    private function composeFallbackAnalysisSummary(array $publicationCatalog, array $forumCatalog, string $term): string
    {
        $parts = [];
        if ($publicationCatalog !== []) {
            $sample = collect($publicationCatalog)->take(2)->pluck('title')->filter()->implode('; ');
            if ($sample !== '') {
                $parts[] = __('publications.search.ai_analysis_publications', ['sample' => Str::limit($sample, 160)]);
            }
        }
        if ($forumCatalog !== []) {
            $parts[] = __('publications.search.ai_analysis_forums', ['count' => count($forumCatalog)]);
        }
        if ($parts === [] && $term !== '') {
            $parts[] = __('publications.search.ai_analysis_generic', ['term' => $term]);
        }

        return Str::limit(implode(' ', $parts), 800);
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

    public function requestFingerprint(Request $request): string
    {
        $term = trim((string) ($request->term ?? ''));

        return md5(mb_strtolower($term).'|'.$this->filterFingerprint($request));
    }

    /**
     * Shared catalog payload for AI insights and follow-up chat.
     *
     * @return array<string, mixed>
     */
    public function buildCatalogPayload(
        Request $request,
        Collection $searchForums,
        Collection $searchCommunities,
        Collection $federatedPublications = new Collection,
        int $publicationLimit = 20
    ): array {
        return [
            'filters' => $this->filterFingerprint($request),
            'publications' => $this->publicationCatalogForAi($request, $publicationLimit),
            'forums' => $this->forumCatalog($searchForums),
            'communities' => $this->communityCatalog($searchCommunities),
            'partner_hub_publications' => $this->federatedPublicationCatalog($federatedPublications),
            'health_topics' => $this->healthTopicCatalog(trim((string) ($request->term ?? ''))),
            'indicators' => $this->indicatorCatalogForAi(trim((string) ($request->term ?? ''))),
            'thematic_areas' => $this->themeCatalog(trim((string) ($request->term ?? ''))),
            'sub_thematic_areas' => $this->subThemeCatalog(trim((string) ($request->term ?? ''))),
            'contributors' => $this->authorCatalog(
                trim((string) ($request->term ?? '')),
                $this->publicationCatalogForAi($request, min($publicationLimit, 15))
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return list<array<string, mixed>>
     */
    public function resolveDocumentsFromIds(
        array $catalog,
        array $publicationIds,
        array $forumIds,
        array $communityIds,
        array $indicatorIds
    ): array {
        $documents = [];
        $pubById = collect($catalog['publications'] ?? [])->keyBy('id');
        $forumById = collect($catalog['forums'] ?? [])->keyBy('id');
        $communityById = collect($catalog['communities'] ?? [])->keyBy('id');
        $indicatorById = collect($catalog['indicators'] ?? [])->keyBy('id');

        foreach ($publicationIds as $id) {
            $row = $pubById->get((int) $id);
            if (! is_array($row)) {
                continue;
            }
            $documents[] = [
                'type' => 'publication',
                'id' => (int) $row['id'],
                'title' => (string) ($row['title'] ?? ''),
                'excerpt' => (string) ($row['excerpt'] ?? $row['description'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        foreach ($forumIds as $id) {
            $row = $forumById->get((int) $id);
            if (! is_array($row)) {
                continue;
            }
            $documents[] = [
                'type' => 'forum',
                'id' => (int) $row['id'],
                'title' => (string) ($row['title'] ?? ''),
                'excerpt' => (string) ($row['excerpt'] ?? $row['description'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        foreach ($communityIds as $id) {
            $row = $communityById->get((int) $id);
            if (! is_array($row)) {
                continue;
            }
            $documents[] = [
                'type' => 'community',
                'id' => (int) $row['id'],
                'title' => (string) ($row['name'] ?? ''),
                'excerpt' => (string) ($row['excerpt'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        foreach ($indicatorIds as $id) {
            $row = $indicatorById->get((int) $id);
            if (! is_array($row)) {
                continue;
            }
            $documents[] = [
                'type' => 'indicator',
                'id' => (int) $row['id'],
                'title' => (string) ($row['name'] ?? ''),
                'excerpt' => (string) ($row['excerpt'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return array_slice($documents, 0, 8);
    }

    /**
     * @param  list<array<string, mixed>>  $publicationCatalog
     * @param  list<array<string, mixed>>  $forumCatalog
     * @return list<array<string, mixed>>
     */
    private function buildDocumentHighlights(array $publicationCatalog, array $forumCatalog): array
    {
        $highlights = [];

        foreach (array_slice($publicationCatalog, 0, 5) as $row) {
            $highlights[] = [
                'type' => 'publication',
                'id' => (int) ($row['id'] ?? 0),
                'title' => (string) ($row['title'] ?? ''),
                'description' => (string) ($row['excerpt'] ?? $row['description'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
                'meta' => trim(implode(' · ', array_filter([
                    (string) ($row['theme'] ?? ''),
                    (string) ($row['category'] ?? ''),
                ]))),
            ];
        }

        foreach (array_slice($forumCatalog, 0, 3) as $row) {
            $highlights[] = [
                'type' => 'forum',
                'id' => (int) ($row['id'] ?? 0),
                'title' => (string) ($row['title'] ?? ''),
                'description' => (string) ($row['excerpt'] ?? $row['description'] ?? ''),
                'url' => (string) ($row['url'] ?? ''),
                'meta' => __('publications.search.forum_discussion'),
            ];
        }

        return $highlights;
    }

    private function plainPublicationDescription(Publication $publication): string
    {
        $desc = strip_tags((string) ($publication->description ?? ''));

        return trim(html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function plainForumDescription(Forum $forum): string
    {
        $desc = strip_tags((string) ($forum->forum_description ?? ''));

        return trim(html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
