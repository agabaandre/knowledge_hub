<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use Illuminate\Http\Request;
class AssetsController extends Controller
{
    private $assetsRepo;

    public function __construct( AssetsRepository $assetsRepo)
    {
        $this->assetsRepo       = $assetsRepo;
    }

    public function index(Request $request){

        $data['assets'] = $this->assetsRepo->get($request);
        return view('assets.index',$data);
    }

    public function details(Request $request){
        $data['asset'] = $this->assetsRepo->find($request->id);
        return view('assets.details',$data);
    }

}
