<?php

namespace App\Http\Controllers;

use App\Repositories\AreasRepository;
use App\Repositories\AuthorsRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Repositories\ThemesRepository;
use Illuminate\Http\Request;

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

        
     
        $data['publications'] = $this->publicationsRepo->get($request);
        $data['recent']       = $data['publications']; //$this->publicationsRepo->get($request);
        $data['authors']      = $this->authorsRepo->get($request);
        $data['categories']   = $this->get_categories();
		$request['is_featured'] = 1;
        $data['featured']     = $this->publicationsRepo->get($request);
        $data['tags']	      = $this->publicationsRepo->get_tags();
		$data['types']        = $this->publicationsRepo->get_types();
        $data['quotes']       = $this->quotesRepo->get($request);
		$data['subthemes']	  = $this->publicationsRepo->get_subthemes();
		$data['themes']		  = $this->themesRepo->get($request);
        $data['is_home']      = true;

        return view('home.index',$data);
    }


    
	private function get_categories(){

		return array(
			[
				"title"=>"Health Security Themes",
				"icon"=>"fa fa-shield",
				'link'=>"browse/themes",
				"image"=>"health.png",
				"stats"=> $this->themesRepo->count()
			],
			[
				"title"=>"Resource Contibuting Sources",
				"icon"=>"fa fa-pen-nib",
				'link'=>"browse/authors",
				"image"=>"resource.png",
				"stats"=> $this->authorsRepo->count()
			],
			[
				"title"=>"Geographical Coverage",
				"icon"=>"fa fa-map-pin",
				'link'=>"browse/areas",
				"image"=>"map.png",
				"stats"=>$this->areasRepo->count()
			],
			[
				"title"=>"Public Discussion Forums",
				"icon"=>"fa fa-comments",
				'link'=>"forums",
				"image"=>"forum.png",
				"stats"=>$this->forumsRepo->count()
			]
		);
	}

}
