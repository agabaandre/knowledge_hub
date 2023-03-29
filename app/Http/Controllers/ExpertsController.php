<?php

namespace App\Http\Controllers;

use App\Repositories\AssetsRepository;
use App\Repositories\ExpertsRepository;
use Illuminate\Http\Request;
class ExpertsController extends Controller
{
    private $expertsRepo;

    public function __construct( ExpertsRepository $expertsRepo)
    {
        $this->expertsRepo       = $expertsRepo;
    }

    public function index(Request $request){

        $data['experts'] = $this->expertsRepo->get($request);
        return view('experts.index',$data);
    }

}
