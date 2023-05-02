<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
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
      
        return view('admin.dashboard.index',$data);
    }



}
