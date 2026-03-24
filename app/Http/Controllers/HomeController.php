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
        $data['publications']  = $this->publicationsRepo->get($request);
        $data['recent']        = collect($data['publications']->items())->take(6);
        $data['authors']       = $this->authorsRepo->get($request);
        $data['categories']   = $this->get_categories();
        $featuredRequest = clone $request;
        $featuredRequest['is_featured'] = 1;
        $featuredPublications = collect($this->publicationsRepo->get($featuredRequest, false, true)->items());

        // Recommended feed:
        // - Logged in: featured first, then publications related to favorite tags
        //   and "Your Interests" (profile preferences/subthemes)
        // - Logged out: featured only
        if (auth()->check()) {
            $relatedByTags = $this->publicationsRepo->relatedByFavoriteTags(auth()->id(), 20);
            $recommendedByPreferences = $this->publicationsRepo->recommendedByPreferences(auth()->id(), 20);
            $data['featured'] = $featuredPublications
                ->concat($recommendedByPreferences)
                ->concat($relatedByTags)
                ->unique('id')
                ->take(6)
                ->values();
        } else {
            $data['featured'] = $featuredPublications->take(6)->values();
        }

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

        return view('home.index',$data);
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
