<?php

namespace App\Http\Controllers;

use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }
    
    public function index(Request $request){

        
        $request['rows']      = 10;

        $data['publications'] = $this->publicationsRepo->get($request);
        $data['recent']       = $this->publicationsRepo->get($request);
        $data['authors']      = $this->authorsRepo->get($request);
        $data['categories']   = $this->get_categories();
        $data['featured']     = $this->publicationsRepo->get($request);
        $data['tags']	      = $this->publicationsRepo->get_tags();
		$data['types']        = $this->publicationsRepo->get_types();
        $data['quotes']       = $this->quotesRepo->get($request);
		$data['subthemes']		  = $this->publicationsRepo->get_subthemes();
        $data['is_home']      = true;

        return view('home.index',$data);
    }


    
	private function get_categories(){

		return array(
			[
				"title"=>"Health Security Themes",
				"icon"=>"fa fa-heart",
				'link'=>"browse/healththemes",
				"image"=>"theme.png",
				"stats"=> 0
			],
			[
				"title"=>"Resource Contibuting Authors",
				"icon"=>"fa fa-business-time",
				'link'=>"browse/authors",
				"image"=>"author.png",
				"stats"=> 0
			],
			[
				"title"=>"Geographical Coverage",
				"icon"=>"fa fa-map-pin",
				'link'=>"browse/areas",
				"image"=>"location.png",
				"stats"=>0
			],
			[
				"title"=>"Public Discussion Forums",
				"icon"=>"fa fa-comments",
				'link'=>"forums",
				"image"=>"location.png",
				"stats"=>0
			]
		);
	}

}
