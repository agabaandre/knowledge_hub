<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ExpertsRepository;
use App\Repositories\ForumsRepository;

class AdminController extends Controller
{
    private $publicationsRepo,$authorsRepo,$expertsRepo,$forumsRepo;

    public function __construct(PublicationsRepository $publicationsRepo,
    AuthorsRepository $authorsRepo, QuotesRepository $quotesRepo,
    ExpertsRepository $expertsRepo,ForumsRepository $forumsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
        $this->expertsRepo      = $expertsRepo;
        $this->forumsRepo       = $forumsRepo;
    }

    public function index(Request $request){

        $request['rows']      = 10;
        $request['order_by_visits'] = true;

		$data['publications'] = $this->publicationsRepo->get($request);
        $data['authors'] = $this->authorsRepo->get($request);
        $data['experts'] = $this->expertsRepo->get($request);
        $data['forums']  = $this->forumsRepo->get($request);

        return view('admin.dashboard.index',$data);
    }

    public function rccdashboards(Request $request){
        return view('admin.dashboard.rcc');
    }



}
