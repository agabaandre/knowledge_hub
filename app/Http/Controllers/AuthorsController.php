<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\PublicationsRepository;
use App\Repositories\QuotesRepository;

class AuthorsController extends Controller
{
    private $publicationsRepo,$authorsRepo;

    public function __construct(PublicationsRepository $publicationsRepo, 
    AuthorsRepository $authorsRepo)
    {
        $this->publicationsRepo = $publicationsRepo;
        $this->authorsRepo      = $authorsRepo;
    }

    public function index(Request $request){

        $data['search'] = (object) $request->all();
        $data['authors'] = $this->authorsRepo->get($request);
        return view('authors.index',$data);
    }

    
}
