<?php

namespace App\Http\Controllers;

use App\Repositories\AreasRepository;
use App\Repositories\AuthorsRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ThemesRepository;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Services\HomeTopSearchesService;

class HomeController extends Controller
{
    public const HOME_EVENTS_INFINITE_ROWS = 6;

    private $publicationsRepo,$authorsRepo,$quotesRepo,$areasRepo,$forumsRepo,$themesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,
	AreasRepository $areasRepo,ForumsRepository $forumsRepo,ThemesRepository $themesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
		$this->areasRepo        = $areasRepo;
		$this->forumsRepo 		= $forumsRepo;
		$this->themesRepo       = $themesRepo;
    }
    
    public function index(Request $request){
        $topSearches = app(HomeTopSearchesService::class);
        $data['recent'] = $topSearches->publications($request, 0, HomeTopSearchesService::INITIAL_LIMIT);
        $data['topSearchesTotal'] = $topSearches->total($request);
        $data['topSearchesInitial'] = HomeTopSearchesService::INITIAL_LIMIT;
        $data['topSearchesPageSize'] = HomeTopSearchesService::LOAD_MORE_LIMIT;
        $data['authors']       = $this->authorsRepo->get($request);
        $data['categories']   = $this->get_categories();
        // Recommended: strict featured pool + (when logged in) preference & tag-based picks,
        // merged and diversified across parent health themes (not a block of recent IDs from one theme).
        $data['featured'] = $this->publicationsRepo->homeRecommendedPublications($request, auth()->id(), 6);

        $data['tags']	      = $this->publicationsRepo->get_tags();
		$data['types']        = $this->publicationsRepo->get_types();
        $data['quotes']       = $this->quotesRepo->get($request);
		$data['subthemes']	  = $this->publicationsRepo->get_subthemes();
		$data['themes']		  = $this->themesRepo->get($request);
        $initiativesRequest = clone $request;
		$initiativesRequest['category']  = 10;
        $data['initiatives'] = $this->publicationsRepo->get($initiativesRequest);
        // Active events only: events where enddate has not passed
        $today = \Carbon\Carbon::today()->startOfDay();
        $eventsQuery = Event::query()
            ->where(function ($w) {
                $w->where('status', 'active')
                  ->orWhereNull('status');
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('enddate')
                  ->orWhereDate('enddate', '>=', $today);
            })
            ->orderBy('startdate', 'asc');

        $data['homeEventsInfiniteScroll'] = $this->homeEventsInfiniteScrollEnabled();
        $data['eventsTotal'] = (clone $eventsQuery)->count();
        if ($data['homeEventsInfiniteScroll']) {
            $data['events'] = (clone $eventsQuery)->take(self::HOME_EVENTS_INFINITE_ROWS)->get();
        } else {
            $data['events'] = (clone $eventsQuery)->take(12)->get();
        }
        $data['is_home']      = true;
        $data['healthEmergencies'] = \App\Support\HealthEmergencyTags::from($data['tags'] ?? []);

        return view('home.index',$data);
    }

    public function topSearchesPage(Request $request)
    {
        if (! (settings()->show_top_searches ?? false)) {
            return response()->json(['ok' => false, 'error' => 'disabled'], 403);
        }

        $offset = max(0, (int) $request->input('offset', HomeTopSearchesService::INITIAL_LIMIT));
        $limit = max(1, min(20, (int) $request->input('limit', HomeTopSearchesService::LOAD_MORE_LIMIT)));

        $service = app(HomeTopSearchesService::class);
        $items = $service->publications($request, $offset, $limit);
        $total = $service->total($request);
        $loadedCount = min($total, $offset + $items->count());

        $theme = trim((string) site_theme());
        $itemsView = ($theme === 'theme1.')
            ? 'home.partials.theme1.top_searches_list_items'
            : 'home.partials.top_searches_list_items';

        return response()->json([
            'ok' => true,
            'html' => view($itemsView, [
                'recent' => $items,
                'listOffset' => $offset,
            ])->render(),
            'loaded_count' => $loadedCount,
            'total' => $total,
            'has_more' => $loadedCount < $total,
        ]);
    }

    public function eventsPage(Request $request)
    {
        if (! (settings()->show_events ?? false) || ! $this->homeEventsInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'disabled'], 403);
        }

        $offset = max(0, (int) $request->input('offset', self::HOME_EVENTS_INFINITE_ROWS));
        $limit = max(1, min(20, (int) $request->input('limit', self::HOME_EVENTS_INFINITE_ROWS)));

        $today = \Carbon\Carbon::today()->startOfDay();
        $eventsQuery = Event::query()
            ->where(function ($w) {
                $w->where('status', 'active')
                  ->orWhereNull('status');
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('enddate')
                  ->orWhereDate('enddate', '>=', $today);
            })
            ->orderBy('startdate', 'asc');

        $total = (clone $eventsQuery)->count();
        $events = (clone $eventsQuery)->skip($offset)->take($limit)->get();
        $loadedCount = min($total, $offset + $events->count());

        $theme = trim((string) site_theme());
        $itemsView = ($theme === 'theme1.')
            ? 'home.partials.theme1.event_list_items'
            : 'home.partials.theme1.event_list_items';

        return response()->json([
            'ok' => true,
            'html' => view($itemsView, ['events' => $events])->render(),
            'loaded_count' => $loadedCount,
            'total' => $total,
            'has_more' => $loadedCount < $total,
        ]);
    }

    protected function homeEventsInfiniteScrollEnabled(): bool
    {
        return (settings()->home_events_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }


    
	private function get_categories() {
		return [
			[
				"title" => __('home_sections.card_health_topics'),
				"title_key" => 'home_sections.card_health_topics',
				"icon" => "fa fa-shield-alt",
				"link" => "health-topics",
				"description" => __('home_sections.card_health_topics_desc'),
				"description_key" => 'home_sections.card_health_topics_desc',
				"stats" => $this->themesRepo->count()
			],
			[
				"title" => __('home_sections.card_contributing_sources'),
				"title_key" => 'home_sections.card_contributing_sources',
				"icon" => "fa fa-pen-nib",
				"link" => "browse/authors",
				"description" => __('home_sections.card_contributing_sources_desc'),
				"description_key" => 'home_sections.card_contributing_sources_desc',
				"stats" => $this->authorsRepo->count()
			],
			[
				"title" => __('home_sections.card_geographical_coverage'),
				"title_key" => 'home_sections.card_geographical_coverage',
				"icon" => "fa fa-map-marker-alt",
				"link" => "browse/areas",
				"description" => __('home_sections.card_geographical_coverage_desc'),
				"description_key" => 'home_sections.card_geographical_coverage_desc',
				"stats" => $this->areasRepo->count()
			],
			[
				"title" => __('home_sections.card_discussion_forums'),
				"title_key" => 'home_sections.card_discussion_forums',
				"icon" => "fa fa-comments",
				"link" => "forums",
				"description" => __('home_sections.card_discussion_forums_desc'),
				"description_key" => 'home_sections.card_discussion_forums_desc',
				"stats" => $this->forumsRepo->count()
			]
		];
	}
	
	

}
