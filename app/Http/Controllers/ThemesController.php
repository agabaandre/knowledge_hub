<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ThemesRepository;

class ThemesController extends Controller
{
    private $themesRepo;

    public function __construct(ThemesRepository $themesRepo)
    {
        $this->themesRepo = $themesRepo;
    }

    public function index(Request $request){

        $data['themes'] = $this->themesRepo->get($request);
        return view('themes.index',$data);
    }

    public function subthemes(Request $request){
        $data['subthemes'] = $this->themesRepo->get_subthemes($request);
        return view('themes.subthemes',$data);
    }

}
