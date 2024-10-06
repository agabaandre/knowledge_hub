<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;

class DashboardsController extends Controller
{
    private $publicationsRepo,$authorsRepo,$quotesRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->quotesRepo       = $quotesRepo;
    }

    public function details(Request $request){

        $publication          =  $this->publicationsRepo->find($request->resource);
        $data['dashboard']  = $publication;       
        return view('admin.dashboard.dashboards',$data);
    }

  
}
