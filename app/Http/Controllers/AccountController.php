<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\PublicationsRepository;
use Illuminate\Http\Request;
class AccountController extends Controller
{
    private $publicationsRepo;

    public function __construct( PublicationsRepository $publicationsRepo)
    {
        $this->publicationsRepo       = $publicationsRepo;
    }


    public function profile(Request $request){

        $data['profile'] = current_user();
        
        return view('account.profile',$data);
    }


    public function favourites(Request $request){

        $data['favourites'] = $this->publicationsRepo->get($request);
        return view('account.favourites',$data);
    }


    public function publications(Request $request){

        $data['publications'] = $this->publicationsRepo->get($request);
        return view('account.mypublications',$data);
    }


    public function publish(Request $request){

        return view('account.create');
    }


    public function submit_publication(Request $request){

        $data['publications'] = $this->publicationsRepo->get($request);
        return view('account.publications',$data);
    }

}
