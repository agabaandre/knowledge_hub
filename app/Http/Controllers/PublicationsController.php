<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Models\Publication;
use App\Models\SubThemeticArea;
use App\Models\ThemeticArea;
use App\Support\ContributorsSeo;
use App\Support\PublicationSeo;
use App\Support\RecordsSearchSeo;
use App\Support\RecordsSearchFragmentCache;
use App\Support\PublicationSearchQuery;
use App\Services\ContributorBadgeAwardService;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\CommsOfPracticeRepository;
use App\Models\SearchLog;
use App\Services\FederatedContentService;
use App\Services\HomeTopSearchesService;
use App\Services\HybridRecordsSearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PublicationsController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo,$forumsRepo,$commsRepo,$federationContent,$recordsSearch;

    public function __construct(
        PublicationsRepository $publicationsRepo,
        AuthorsRepository $authorsRepo,
        QuotesRepository $quotesRepo,
        ForumsRepository $forumsRepo,
        CommsOfPracticeRepository $commsRepo,
        FederatedContentService $federationContent,
        HybridRecordsSearchService $recordsSearch
    ) {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
        $this->forumsRepo       = $forumsRepo;
        $this->commsRepo        = $commsRepo;
        $this->federationContent = $federationContent;
        $this->recordsSearch    = $recordsSearch;
    }

    public function show(Request $request, ?string $slug = null){
        if ($slug) {
            $data['publication'] = $this->publicationsRepo->findBySlug($slug);
        } else {
            if (! $request->id) {
                abort(404);
            }

            $data['publication'] = $this->publicationsRepo->find($request->id);

            if ($data['publication'] && seo_friendly_urls_enabled() && ! empty($data['publication']->slug)) {
                return redirect()->to(publication_url($data['publication']), 301);
            }
        }

        if(!$data['publication'])
            abort(404);
        
        // Get related publications - prioritize by tags, then themes/sub-themes
        $tagIds = $data['publication']->tag_ids ?? [];
        $thematicAreaId = $data['publication']->thematic_area_id ?? null;
        $subThematicAreaId = $data['publication']->sub_thematic_area_id ?? null;
        
        $relatedPubs = collect();
        
        // Priority 1: Publications with matching tags (up to 10)
        if (!empty($tagIds)) {
            $taggedPubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('tag_id', $tagIds);
                })
                ->with(['author', 'tags.tag']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $taggedPubsQuery->where('is_admin_only_access', 0);
            }
            
            $taggedPubs = $taggedPubsQuery->inRandomOrder()->take(10)->get();
            $relatedPubs = $relatedPubs->merge($taggedPubs);
        }
        
        // Priority 2: If we don't have enough, add publications with same thematic area
        if ($relatedPubs->count() < 10 && $thematicAreaId) {
            $needed = 10 - $relatedPubs->count();
            $existingIds = $relatedPubs->pluck('id')->toArray();
            $existingIds[] = $data['publication']->id;
            
            $thematicPubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereNotIn('id', $existingIds)
                ->whereHas('sub_theme', function($q) use ($thematicAreaId) {
                    $q->where('thematic_area_id', $thematicAreaId);
                })
                ->with(['author', 'tags.tag', 'sub_theme']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $thematicPubsQuery->where('is_admin_only_access', 0);
            }
            
            $thematicPubs = $thematicPubsQuery->inRandomOrder()->take($needed)->get();
            $relatedPubs = $relatedPubs->merge($thematicPubs);
        }
        
        // Priority 3: If we still don't have enough, add publications with same sub-theme
        if ($relatedPubs->count() < 10 && $subThematicAreaId) {
            $needed = 10 - $relatedPubs->count();
            $existingIds = $relatedPubs->pluck('id')->toArray();
            $existingIds[] = $data['publication']->id;
            
            $subthemePubsQuery = \App\Models\Publication::where('is_version', 0)
                ->where('is_active', 'Active')
                ->where('is_approved', 1)
                ->where('id', '!=', $data['publication']->id)
                ->whereNotIn('id', $existingIds)
                ->where('sub_thematic_area_id', $subThematicAreaId)
                ->with(['author', 'tags.tag', 'sub_theme']);
            
            // Apply access control for non-admin users
            if (!is_admin()) {
                $subthemePubsQuery->where('is_admin_only_access', 0);
            }
            
            $subthemePubs = $subthemePubsQuery->inRandomOrder()->take($needed)->get();
            $relatedPubs = $relatedPubs->merge($subthemePubs);
        }
        
        // Limit to 10 and remove duplicates
        $data['related_publications'] = $relatedPubs->unique('id')->take(10);
      
        return view('publications.show',$data);
    }

    public function shortened(Request $request){

        $summary              = $this->publicationsRepo->find_shortened($request->id);
        $data['publication']  = $this->publicationsRepo->find($summary->resource_id);
        $data['abstract']     = $summary;

        if(!$data['publication'] || !$summary)
        abort(404);
      
        return view('publications.abstract',$data);
    }

    public function search(Request $request)
    {
        if (seo_friendly_urls_enabled()) {
            $legacyRedirect = $this->legacyRecordsFilterRedirect($request);
            if ($legacyRedirect) {
                return $legacyRedirect;
            }
        }

        if ($request->filled('tag') && seo_friendly_urls_enabled()) {
            $tagModel = Tag::find((int) $request->tag);
            if ($tagModel && ! empty($tagModel->slug)) {
                $query = $request->query();
                unset($query['tag']);
                $target = tag_records_url($tagModel, true, $query);

                return redirect()->to($target, 301);
            }
        }

        return $this->searchWithoutLegacyTagRedirect($request);
    }

    public function searchByTag(Request $request, string $slug)
    {
        $tag = Tag::query()->where('slug', $slug)->first();
        if (! $tag) {
            abort(404);
        }

        $request->merge(['tag' => $tag->id]);

        return $this->searchWithoutLegacyTagRedirect($request);
    }

    public function searchByThematicArea(Request $request, string $slug)
    {
        $theme = ThemeticArea::query()->where('slug', $slug)->first();
        if (! $theme) {
            abort(404);
        }

        $request->merge(['thematic_area_id' => $theme->id]);

        return $this->searchWithoutLegacyTagRedirect($request);
    }

    public function searchBySubThematicArea(Request $request, string $slug)
    {
        $subTheme = SubThemeticArea::query()->where('slug', $slug)->first();
        if (! $subTheme) {
            abort(404);
        }

        $request->merge(['sub_thematic_area_id' => $subTheme->id]);

        return $this->searchWithoutLegacyTagRedirect($request);
    }

    protected function legacyRecordsFilterRedirect(Request $request): ?\Illuminate\Http\RedirectResponse
    {
        $subThemeId = $request->input('sub_thematic_area_id') ?: $request->input('subtheme');
        if ($subThemeId) {
            $subTheme = SubThemeticArea::find((int) $subThemeId);
            if ($subTheme && ! empty($subTheme->slug)) {
                $query = $request->query();
                unset($query['sub_thematic_area_id'], $query['subtheme'], $query['theme'], $query['thematic_area_id']);
                $target = sub_thematic_area_records_url($subTheme, true, $query);

                if ($target !== $request->fullUrl()) {
                    return redirect()->to($target, 301);
                }
            }
        }

        $themeId = $request->input('theme') ?: $request->input('thematic_area_id');
        if ($themeId) {
            $theme = ThemeticArea::find((int) $themeId);
            if ($theme && ! empty($theme->slug)) {
                $query = $request->query();
                unset($query['theme'], $query['thematic_area_id']);
                $target = thematic_area_records_url($theme, true, $query);

                if ($target !== $request->fullUrl()) {
                    return redirect()->to($target, 301);
                }
            }
        }

        return null;
    }

    protected function searchWithoutLegacyTagRedirect(Request $request)
    {
        $this->prepareRecordsSearchRequest($request);
        $this->validateRecordsSearchRequest($request);

        $term = trim((string) ($request->term ?? ''));
        if ($term !== '') {
            $data = $this->buildRecordsSearchShellData($request);
        } else {
            $data = $this->buildRecordsSearchData($request);
            $this->maybeLogKeywordSearch($request, $data);
        }

        return view('publications.search', $data);
    }

    /**
     * Persist homepage / records keyword searches for admin analytics (guests: user_id null).
     */
    protected function maybeLogKeywordSearch(Request $request, array $data): void
    {
        $term = trim((string) ($request->term ?? ''));
        if ($term === '') {
            return;
        }

        try {
            SearchLog::query()->create([
                'user_id' => auth()->id(),
                'term' => mb_substr($term, 0, 255),
                'results_count' => (int) ($data['results_count'] ?? 0),
                'request_path' => '/'.$request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) ($request->userAgent() ?? ''), 0, 2000),
            ]);
        } catch (\Throwable $e) {
            Log::warning('search_logs.write_failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * JSON + HTML fragments for AJAX filtering on the records search page (same query semantics as {@see search}).
     */
    public function searchFragment(Request $request)
    {
        $this->prepareRecordsSearchRequest($request);
        $this->validateRecordsSearchRequest($request);

        $cached = RecordsSearchFragmentCache::get($request);
        if ($cached !== null) {
            return response()->json($cached);
        }

        $data = $this->buildRecordsSearchData($request, skipAi: true);
        $this->maybeLogKeywordSearch($request, $data);

        $payload = [
            'heading_html' => view('publications.partials.search_main_heading', $data)->render(),
            'body_html' => view('publications.partials.search_main_body', $data)->render(),
            'main_html' => view('publications.partials.search_main_column', $data)->render(),
            'sidebar_html' => view('publications.partials.search_sidebar_dynamic', $data)->render(),
            'page_title' => $data['pageTitle'],
            'meta_description' => strip_tags($data['pageDescription']),
            'canonical_url' => $data['canonicalUrl'],
            'structured_data' => $data['searchJsonLd'] ?? null,
            'results_count' => $data['results_count'] ?? 0,
            'search_time' => $data['search_time'] ?? null,
        ];

        RecordsSearchFragmentCache::put($request, $payload);

        return response()->json($payload);
    }

    /**
     * Khub AI assistant HTML loaded asynchronously after the search shell renders.
     */
    public function searchAiInsightsFragment(Request $request)
    {
        if (! (bool) (settings()->enable_ai_search ?? false)) {
            return response()->json(['ok' => false, 'error' => 'ai_disabled'], 403);
        }

        $this->prepareRecordsSearchRequest($request);
        $this->validateRecordsSearchRequest($request);

        $term = trim((string) ($request->term ?? ''));
        if ($term === '' || mb_strlen($term) < 2) {
            return response()->json(['ok' => false, 'error' => 'term_too_short'], 422);
        }

        $cacheKey = 'records_search_ai_fragment:v'.\App\Support\SearchCache::aiInsightsVersion().':'.md5(
            mb_strtolower(PublicationSearchQuery::normalizeTerm($term)).'|'.json_encode($request->except('page'))
        );
        $store = \App\Support\MetricsCache::store();
        $cached = $store->get($cacheKey);
        if (is_array($cached) && ! empty($cached['assistant_html'])) {
            return response()->json($cached);
        }

        $data = $this->buildRecordsSearchData($request, skipAi: true, skipHeavyExtras: true);

        try {
            $data['aiSearchInsights'] = app(\App\Services\AiSearchInsightsService::class)->generate(
                $request,
                $data['publications'],
                $data['searchForums'],
                $data['searchCommunities'],
                $data['federatedPublications']
            );
        } catch (\Throwable $e) {
            $data['aiSearchInsights'] = null;
        }

        $assistantHtml = '';
        if ($data['aiSearchEnabled'] ?? false) {
            try {
                $assistantHtml = view('publications.partials.ai_search_assistant', $data)->render();
            } catch (\Throwable $e) {
                Log::warning('records_search.ai_fragment_view_failed', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $payload = [
            'ok' => true,
            'assistant_html' => $assistantHtml,
        ];

        if ($assistantHtml !== '') {
            $store->put($cacheKey, $payload, \App\Support\MetricsCache::ttl('ai_search'));
        }

        return response()->json($payload);
    }

    /**
     * Append the next page of publication cards for infinite-scroll search results.
     */
    public function searchPublicationsPage(Request $request)
    {
        if (! $this->searchInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $this->prepareRecordsSearchRequest($request);
        $this->validateRecordsSearchRequest($request);
        $request->merge(['rows' => HomeTopSearchesService::SEARCH_INFINITE_ROWS]);

        $publicationRequest = $this->recordsSearch->preparePublicationSearchRequest($request);
        $publications = $this->publicationsRepo->get($publicationRequest);

        $page = (int) $publications->currentPage();
        $perPage = (int) $publications->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($publications->total(), $listOffset + $publications->count());

        return response()->json([
            'ok' => true,
            'html' => view('publications.partials.publications_list_items', [
                'publications' => $publications,
                'listOffset' => $listOffset,
            ])->render(),
            'current_page' => $page,
            'last_page' => (int) $publications->lastPage(),
            'has_more' => $publications->hasMorePages(),
            'total' => (int) $publications->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function searchInfiniteScrollEnabled(): bool
    {
        return (settings()->search_pagination_mode ?? 'pagination') === 'infinite_scroll';
    }

    protected function prepareRecordsSearchRequest(Request $request): void
    {
        $request->merge([
            'term' => is_string($request->term)
                ? \App\Support\PublicationSearchQuery::normalizeTerm($request->term)
                : null,
        ]);
    }

    protected function validateRecordsSearchRequest(Request $request): void
    {
        $request->validate([
            'term' => 'nullable|string|max:255',
            'thematic_area_id' => 'nullable|integer',
            'theme' => 'nullable|integer',
            'subtheme' => 'nullable|integer',
            'sub_thematic_area_id' => 'nullable|integer',
            'author' => 'nullable|integer',
            'author_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'tag' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1|max:10000',
            'data_category_id' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_array($value)) {
                    foreach ($value as $id) {
                        if (! is_numeric($id) || (int) $id < 1) {
                            $fail(__('Invalid category filter.'));
                            return;
                        }
                    }
                } elseif ($value !== null && $value !== '' && $value !== 'all' && (! is_numeric($value) || (int) $value < 1)) {
                    $fail(__('Invalid category filter.'));
                }
            }],
            'file_category_id' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_array($value)) {
                    foreach ($value as $id) {
                        if (! is_numeric($id) || (int) $id < 1) {
                            $fail(__('Invalid sub category filter.'));
                            return;
                        }
                    }
                } elseif ($value !== null && $value !== '' && $value !== 'all' && (! is_numeric($value) || (int) $value < 1)) {
                    $fail(__('Invalid sub category filter.'));
                }
            }],
            'file_type_id' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_array($value)) {
                    foreach ($value as $id) {
                        if (! is_numeric($id) || (int) $id < 1) {
                            $fail(__('Invalid file type filter.'));
                            return;
                        }
                    }
                } elseif ($value !== null && $value !== '' && $value !== 'all' && (! is_numeric($value) || (int) $value < 1)) {
                    $fail(__('Invalid file type filter.'));
                }
            }],
            'file_type' => ['nullable', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_array($value)) {
                    foreach ($value as $id) {
                        if (! is_numeric($id) || (int) $id < 1) {
                            $fail(__('Invalid file type filter.'));
                            return;
                        }
                    }
                } elseif ($value !== null && $value !== '' && $value !== 'all' && (! is_numeric($value) || (int) $value < 1)) {
                    $fail(__('Invalid file type filter.'));
                }
            }],
        ]);
    }

    protected function buildRecordsSearchShellData(Request $request): array
    {
        $request->merge([
            'thematic_area_id' => $request->theme ?? $request->thematic_area_id,
        ]);

        if ($this->searchInfiniteScrollEnabled()) {
            $request->merge([
                'page' => 1,
                'rows' => HomeTopSearchesService::SEARCH_INFINITE_ROWS,
            ]);
        }

        $perPage = max(1, (int) ($request->rows ?? HomeTopSearchesService::SEARCH_INFINITE_ROWS));
        $emptyPublications = new LengthAwarePaginator(
            [],
            0,
            $perPage,
            1,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $data['sub_themes'] = ($request->thematic_area_id) ? $this->publicationsRepo->get_subthemes($request) : [];
        $data['publications'] = $emptyPublications;
        $data['search'] = (object) $request->all();
        $data['searchForums'] = collect();
        $data['searchCommunities'] = collect();
        $data['federatedPublications'] = collect();
        $data['federatedForums'] = collect();
        $data['federationBrowseEnabled'] = $this->federationContent->federationConsumerEnabled();
        $data['results_count'] = null;
        $data['search_time'] = null;
        $data['latestPublications'] = collect();
        $data['tags'] = Tag::popularByEngagement(20);

        $seo = RecordsSearchSeo::build(
            $request,
            $data['publications'],
            0,
            $data['searchForums'],
            $data['searchCommunities']
        );

        $data['pageTitle'] = $seo['pageTitle'];
        $data['pageDescription'] = $seo['pageDescription'];
        $data['pageKeywords'] = $seo['pageKeywords'];
        $data['canonicalUrl'] = $seo['canonicalUrl'];
        $data['searchJsonLd'] = $seo['searchJsonLd'];
        $data['searchHeading'] = $seo['searchHeading'];
        $data['ogType'] = 'website';
        $data['jsonLdFlags'] = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $data['aiSearchInsights'] = null;
        $data['aiSearchEnabled'] = (bool) (settings()->enable_ai_search ?? false);
        $data['searchInfiniteScroll'] = $this->searchInfiniteScrollEnabled();
        $data['searchAsyncLoad'] = true;

        return $data;
    }

    protected function buildRecordsSearchData(Request $request, bool $skipAi = false, bool $skipHeavyExtras = false): array
    {
        $request->merge([
            'thematic_area_id' => $request->theme ?? $request->thematic_area_id,
        ]);

        if ($this->searchInfiniteScrollEnabled()) {
            $request->merge([
                'page' => 1,
                'rows' => HomeTopSearchesService::SEARCH_INFINITE_ROWS,
            ]);
        }

        $startTime = microtime(true);

        $data['sub_themes'] = ($request->thematic_area_id) ? $this->publicationsRepo->get_subthemes($request) : [];

        $publicationRequest = $this->recordsSearch->preparePublicationSearchRequest($request);
        $data['publications'] = $this->publicationsRepo->get($publicationRequest);
        $data['search'] = (object) $request->all();

        $data['searchForums'] = (settings()->search_show_forums ?? true)
            ? $this->forumsRepo->searchForRecords($request, 5, false)
            : collect();
        $data['searchCommunities'] = (settings()->search_show_communities ?? true)
            ? $this->commsRepo->searchForRecords($request, 5)
            : collect();

        $data['federatedPublications'] = collect();
        $data['federatedForums'] = collect();
        $data['federationBrowseEnabled'] = $this->federationContent->federationConsumerEnabled();

        if (! $skipHeavyExtras && $data['federationBrowseEnabled'] && $request->filled('term')) {
            $federated = $this->federationContent->search($request->input('term'), 20);
            $data['federatedPublications'] = $federated['publications'];
            if (settings()->search_show_forums ?? true) {
                $data['federatedForums'] = $federated['forums']->take(5);
            }
        }

        $endTime = microtime(true);
        $data['search_time'] = round(($endTime - $startTime) * 1000, 2);
        $data['results_count'] = $data['publications']->total()
            + $data['searchForums']->count()
            + $data['searchCommunities']->count()
            + $data['federatedPublications']->count()
            + $data['federatedForums']->count();

        if (! $skipHeavyExtras) {
            $data['latestPublications'] = $this->publicationsRepo->get(new Request(['rows' => 5]));
        } else {
            $data['latestPublications'] = collect();
        }

        $data['tags'] = Tag::popularByEngagement(20);

        $seo = RecordsSearchSeo::build(
            $request,
            $data['publications'],
            $data['results_count'],
            $data['searchForums'],
            $data['searchCommunities']
        );

        $data['pageTitle'] = $seo['pageTitle'];
        $data['pageDescription'] = $seo['pageDescription'];
        $data['pageKeywords'] = $seo['pageKeywords'];
        $data['canonicalUrl'] = $seo['canonicalUrl'];
        $data['searchJsonLd'] = $seo['searchJsonLd'];
        $data['searchHeading'] = $seo['searchHeading'];
        $data['ogType'] = 'website';
        $data['jsonLdFlags'] = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $data['aiSearchInsights'] = null;
        $data['aiSearchEnabled'] = (bool) (settings()->enable_ai_search ?? false);
        $data['searchInfiniteScroll'] = $this->searchInfiniteScrollEnabled();
        $data['searchAsyncLoad'] = false;
        $data['skipAiAssistant'] = $skipAi;

        $searchTerm = trim((string) ($request->input('term', '')));
        if (! $skipAi && $data['aiSearchEnabled'] && mb_strlen($searchTerm) >= 2) {
            try {
                $data['aiSearchInsights'] = app(\App\Services\AiSearchInsightsService::class)->generate(
                    $request,
                    $data['publications'],
                    $data['searchForums'],
                    $data['searchCommunities'],
                    $data['federatedPublications']
                );
            } catch (\Throwable $e) {
                $data['aiSearchInsights'] = null;
            }
        }

        return $data;
    }

    public function author_pubs(Request $request, ?string $slug = null){

        if ($slug) {
            $author = $this->authorsRepo->findBySlug($slug);
        } elseif ($request->filled('author')) {
            $author = $this->authorsRepo->find($request->author);
            if ($author && seo_friendly_urls_enabled() && ! empty($author->slug)) {
                $redirectQuery = $request->except('author');
                $target = author_publications_url($author, true, $redirectQuery);

                if ($target !== $request->fullUrl()) {
                    return redirect()->to($target, 301);
                }
            }
        } else {
            abort(404);
        }

        if (! $author) {
            abort(404);
        }

        $request->merge(['author' => $author->id]);

        $data['author']       = $author;
        $data['contributorOrganization'] = contributor_profile_organization($author, $author->user ?? null);
        $data['publications'] = $this->publicationsRepo->get($request);
        $data['forumContributions'] = collect();
        $data['contributionStats'] = [
            'resource_contributions' => method_exists($data['publications'], 'total') ? (int) $data['publications']->total() : count($data['publications'] ?? []),
            'forum_posts' => 0,
            'forum_comments' => 0,
            'forum_contributions' => 0,
            'total_contributions' => 0,
        ];

        if (!empty($data['author']) && !empty($data['author']->user)) {
            $userId = (int) $data['author']->user->id;

            $forumPosts = \App\Models\Forum::query()
                ->where('created_by', $userId)
                ->where('status', 1)
                ->count();

            $forumComments = \App\Models\ForumComment::query()
                ->where('created_by', $userId)
                ->whereHas('forum', function ($q) {
                    $q->where('status', 1);
                })
                ->count();

            $forumContributions = $this->forumsRepo->getByUser($userId, $request, 1)->withQueryString();
            $forumContributions->setPageName('forums_page');

            $forumIds = $forumContributions->getCollection()->pluck('id')->all();
            $myCommentCountByForum = empty($forumIds)
                ? collect()
                : \App\Models\ForumComment::query()
                    ->where('created_by', $userId)
                    ->whereIn('forum_id', $forumIds)
                    ->selectRaw('forum_id, COUNT(*) as total')
                    ->groupBy('forum_id')
                    ->pluck('total', 'forum_id');

            $forumContributions->setCollection(
                $forumContributions->getCollection()->map(function ($forum) use ($userId, $myCommentCountByForum) {
                    $forum->is_authored_by_contributor = (int) $forum->created_by === $userId;
                    $forum->my_comment_count = (int) ($myCommentCountByForum[$forum->id] ?? 0);
                    return $forum;
                })
            );

            $data['forumContributions'] = $forumContributions;
            $data['contributionStats']['forum_posts'] = $forumPosts;
            $data['contributionStats']['forum_comments'] = $forumComments;
            $data['contributionStats']['forum_contributions'] = $forumPosts + $forumComments;
            $data['contributionStats']['total_contributions'] = $data['contributionStats']['resource_contributions'] + $data['contributionStats']['forum_contributions'];
        } else {
            $data['contributionStats']['total_contributions'] = $data['contributionStats']['resource_contributions'];
        }

        $stats = $data['contributionStats'];
        $canonicalQuery = [];
        if ($request->filled('page') && (int) $request->page > 1) {
            $canonicalQuery['page'] = (int) $request->page;
        }
        $data['canonicalUrl'] = author_publications_url($author, true, $canonicalQuery);
        $data['pageTitle'] = ContributorsSeo::authorProfileTitle($author, $stats);
        $data['pageDescription'] = ContributorsSeo::authorProfileDescription($author, $stats);
        $data['pageKeywords'] = ContributorsSeo::authorProfileKeywords($author, $stats);
        $data['ogType'] = 'profile';

        $logoRaw = settings()->logo ?? '';
        $data['pageImage'] = ContributorsSeo::contributorImageUrl($author)
            ?: ($logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
                ? $logoRaw
                : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png')));

        $data['authorProfileJsonLd'] = ContributorsSeo::authorProfileGraph(
            $author,
            $stats,
            $data['publications'],
            $data['canonicalUrl'],
            $data['pageDescription']
        );

        $badgeService = app(ContributorBadgeAwardService::class);
        $badgePeriod = $badgeService->defaultPeriod();
        $data['badgeDrilldownYear'] = (int) $badgePeriod['year'];
        $data['badgeDrilldownMonth'] = (int) $badgePeriod['month'];
        $data['lifetimeBadge'] = null;
        $data['communityBadgeStarCount'] = 0;

        if (! empty($data['author']->user)) {
            $data['author']->user->loadMissing('lifetimeBadge.badgeType');
            $data['lifetimeBadge'] = $data['author']->user->lifetimeBadge;
            if ($data['lifetimeBadge'] && $data['lifetimeBadge']->badge_type_id) {
                $data['communityBadgeStarCount'] = $badgeService
                    ->communityContributionsForMonth(
                        (int) $data['author']->user->id,
                        $data['badgeDrilldownYear'],
                        $data['badgeDrilldownMonth']
                    )
                    ->count();
            }
        }

        $data['authorCommunities'] = collect();
        if (! empty($data['author']->user)) {
            $memberUserId = (int) $data['author']->user->id;
            $data['authorCommunities'] = \App\Models\CommunityOfPractice::query()
                ->select(['community_of_practices.id', 'community_of_practices.community_name', 'community_of_practices.slug'])
                ->whereHas('membership', function ($q) use ($memberUserId) {
                    $q->where('user_id', $memberUserId)
                        ->where('is_approved', 1)
                        ->where('is_active', 1);
                })
                ->withCount(['membership as approved_members_count' => function ($q) {
                    $q->where('is_approved', 1)->where('is_active', 1);
                }])
                ->orderBy('community_name')
                ->get();
        }

        return view('publications.author_pubs',$data);
    }

    public function authorBadgeCommunities(Request $request, ?string $slug = null)
    {
        if ($slug) {
            $author = $this->authorsRepo->findBySlug($slug);
        } elseif ($request->filled('author')) {
            $author = $this->authorsRepo->find($request->author);
        } else {
            abort(404);
        }

        if (! $author || ! $author->user) {
            abort(404);
        }

        $service = app(ContributorBadgeAwardService::class);
        $period = $service->defaultPeriod();
        $year = (int) $request->input('year', $period['year']);
        $month = (int) $request->input('month', $period['month']);

        $rows = $service->communityContributionsForMonth((int) $author->user->id, $year, $month);

        return response()->json([
            'year' => $year,
            'month' => $month,
            'period_label' => \Carbon\Carbon::create($year, $month, 1)->format('F Y'),
            'community_count' => $rows->count(),
            'total_community_contributions' => (int) $rows->sum('contributions_count'),
            'communities' => $rows->map(function ($row) {
                return [
                    'community_id' => $row->community_of_practice_id,
                    'community_name' => $row->community->community_name ?? 'Community',
                    'community_url' => $row->community ? community_detail_url($row->community) : null,
                    'contributions_count' => (int) $row->contributions_count,
                ];
            })->values(),
        ]);
    }

    public function subtheme_pubs(Request $request){

        $data['subtheme']     = $this->publicationsRepo->get_subtheme($request->subtheme);
        $data['publications'] = $this->publicationsRepo->get($request);

        return view('publications.subtheme_pubs',$data);
    }

    public function autocomplete(Request $request){

        $searches = $this->publicationsRepo->get(
            $this->recordsSearch->preparePublicationSearchRequest($request),
            true
        );
        return response()->json($searches);
    }

    public function add_favourite(Request $request){
        $id = $request->input('id');
        if (!$id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Publication ID required'], 400);
            }
            return back();
        }
        if (!auth()->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please login to add favorites'], 401);
            }
            return redirect()->guest(route('login'));
        }
        $this->publicationsRepo->add_favourite($id);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'favourited' => true]);
        }
        return back();
    }

    public function remove_favourite(Request $request){
        $id = $request->input('id');
        if (!$id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Publication ID required'], 400);
            }
            return back();
        }
        if (!auth()->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please login to remove favorites'], 401);
            }
            return redirect()->guest(route('login'));
        }
        $this->publicationsRepo->remove_favourite($id);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'favourited' => false]);
        }
        return back();
    }

    public function comment(Request $request){
        if (! auth()->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please login to comment.'], 401);
            }
            abort(403, 'Please login to comment.');
        }

        $request->validate([
            'publication_id' => 'required|integer|exists:publication,id',
            'comment' => 'required|string|max:20000',
        ]);

        $commentText = trim((string) $request->input('comment'));
        if ($commentText === '') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Comment is required.'], 422);
            }
            return back()->withErrors(['comment' => 'Comment is required.'])->withInput();
        }

        $wordCount = count(preg_split('/\s+/u', $commentText, -1, PREG_SPLIT_NO_EMPTY));
        if ($wordCount > 300) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Comments are limited to 300 words.'], 422);
            }
            return back()->withErrors(['comment' => 'Comments are limited to 300 words.'])->withInput();
        }

        $request->merge(['comment' => $commentText]);

        // Validate reCAPTCHA for full-page submissions only
        $recaptchaSiteKey = config('recaptcha.api_site_key');
        $isLocalhost = in_array($request->getHost(), ['localhost', '127.0.0.1']) ||
                       app()->environment('local', 'testing');

        if (! $request->ajax() && ! $request->wantsJson() && $recaptchaSiteKey && ! empty($recaptchaSiteKey) && ! $isLocalhost) {
            if (! $request->filled('g-recaptcha-response')) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'Please complete the CAPTCHA to proceed.',
                ])->withInput();
            }

            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (! \Biscolab\ReCaptcha\Facades\ReCaptcha::validate($recaptchaResponse)) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.',
                ])->withInput();
            }
        }

        $comment = $this->publicationsRepo->save_comment($request);

        if ($request->ajax() || $request->wantsJson()) {
            $comment->load('user');
            $publication = Publication::with(['comments.user'])->find($request->publication_id);
            $approvedCount = $publication
                ? $publication->comments->filter(function ($row) {
                    $status = $row->status ?? null;
                    if ($status === 'rejected' || $status === 'pending') {
                        return false;
                    }
                    return $status === 'approved' || $status === null;
                })->count()
                : 0;
            $isApproved = ($comment->status ?? null) === 'approved' || ($comment->status ?? null) === null;

            return response()->json([
                'success' => true,
                'message' => $isApproved ? 'Comment posted successfully.' : 'Comment submitted and awaiting approval.',
                'pending_approval' => ! $isApproved,
                'comment_count' => $approvedCount,
                'comment' => $comment,
                'comment_html' => $isApproved
                    ? view('partials.publications.publication_comment_item_mini', ['comment' => $comment])->render()
                    : null,
            ]);
        }

        return back()->with('success', 'Comment saved successfully.');
    }

    public function request_content(Request $request){

        if($request->getMethod()=='POST'){
            // Validate and verify captcha
            $request->merge([
                'title' => is_string($request->title) ? strip_tags(trim($request->title)) : $request->title,
            ]);
            $val_rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'country_id' => 'nullable|integer',
                'email' => 'nullable|email',
            ];

            $messages = [];

            // Check if we're on localhost or local environment
            $isLocalhost = in_array($request->getHost(), ['localhost', '127.0.0.1']) || 
                           app()->environment('local', 'testing');

            // Add reCAPTCHA validation if site key is configured AND not on localhost
            $recaptchaSiteKey = config('recaptcha.api_site_key');
            if ($recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost) {
                $val_rules['g-recaptcha-response'] = 'required';
                $messages['g-recaptcha-response.required'] = 'Please complete the CAPTCHA to proceed.';
            }

            $request->validate($val_rules, $messages);

            // Validate reCAPTCHA response if provided and not on localhost
            if ($recaptchaSiteKey && !empty($recaptchaSiteKey) && !$isLocalhost) {
                if (!$request->filled('g-recaptcha-response')) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'Please complete the CAPTCHA to proceed.'
                    ])->withInput();
                }
                
                $recaptchaResponse = $request->input('g-recaptcha-response');
                if (!\Biscolab\ReCaptcha\Facades\ReCaptcha::validate($recaptchaResponse)) {
                    return back()->withErrors([
                        'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.'
                    ])->withInput();
                }
            }

            $saved = $this->publicationsRepo->save_content_request($request);
            if($saved):
                $data = ['message'=>'Request submitted successfully','status'=>'success','data'=>$saved];
            else:
                $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
            endif;
        
            return back()->with($data);;
        }
            

        return view('publications.content_request');
    }

   
}
