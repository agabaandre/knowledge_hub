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
        $data['recent']        = $data['publications']; //$this->publicationsRepo->get($request);
        $data['authors']       = $this->authorsRepo->get($request);
        $data['categories']   = $this->get_categories();
		$request['is_featured'] = 1;
        $data['featured']     = $this->publicationsRepo->get($request,false,true);
        $data['tags']	      = $this->publicationsRepo->get_tags();
		$data['types']        = $this->publicationsRepo->get_types();
        $data['quotes']       = $this->quotesRepo->get($request);
		$data['subthemes']	  = $this->publicationsRepo->get_subthemes();
		$data['themes']		  = $this->themesRepo->get($request);
		$request['category']  = 10;
        $data['initiatives'] = $this->publicationsRepo->get($request);
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
