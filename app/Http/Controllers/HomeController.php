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
        $data['events'] = Event::query()
            ->where(function($w){
                $w->where('status', 'active')
                  ->orWhereNull('status');
            })
            ->where(function($q) use ($today){
                // Event is active if enddate is null or enddate has not passed (enddate >= today)
                $q->whereNull('enddate')
                  ->orWhereDate('enddate', '>=', $today);
            })
            ->orderBy('startdate', 'asc')
            ->take(12)
            ->get();
        $data['is_home']      = true;
        $data['healthEmergencies'] = collect($data['tags'] ?? [])
            ->filter(fn ($tag) => ! empty($tag->is_health_emergency))
            ->values();

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


    
	private function get_categories() {
		return [
			[
				"title" => "Health Topics",
				"icon" => "fa fa-shield-alt",
				"link" => "health-topics",
				"description" => "Explore diseases, conditions, and key health issues.",
				"stats" => $this->themesRepo->count()
			],
			[
				"title" => "Resource Contributing Sources",
				"icon" => "fa fa-pen-nib",
				"link" => "browse/authors",
				"description" => "Organizations and authors that provide verified resources.",
				"stats" => $this->authorsRepo->count()
			],
			[
				"title" => "Geographical Coverage",
				"icon" => "fa fa-map-marker-alt",
				"link" => "browse/areas",
				"description" => "View data and documents by regions and countries.",
				"stats" => $this->areasRepo->count()
			],
			[
				"title" => "Public Discussion Forums",
				"icon" => "fa fa-comments",
				"link" => "forums",
				"description" => "Join discussions on pressing health topics and challenges.",
				"stats" => $this->forumsRepo->count()
			]
		];
	}
	
	

}
