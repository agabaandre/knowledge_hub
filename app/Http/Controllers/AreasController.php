<?php

namespace App\Http\Controllers;

use App\Repositories\AreasRepository;
use Illuminate\Http\Request;
use App\Repositories\ThemesRepository;

class AreasController extends Controller
{
    private $areasRepo;

    public function __construct(AreasRepository $areasRepo)
    {
        $this->areasRepo = $areasRepo;
    }

    public function index(Request $request){
        $data['areas'] = $this->areasRepo->get($request);
        return view('areas.index',$data);
    }

    
}
