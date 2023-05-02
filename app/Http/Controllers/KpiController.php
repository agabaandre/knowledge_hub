<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\IndicatorsRepository;


class KpiController extends Controller
{
    private $indicatorsRepo;

    public function __construct(IndicatorsRepository $indicatorsRepo)
    {
        $this->indicatorsRepo = $indicatorsRepo;
    }

    public function index(Request $request){

        $data['search'] = (object) $request->all();
        $data['indicators'] = $this->indicatorsRepo->get($request);
        return view('kpi.index',$data);
    }

    public function save(Request $request){

        $this->indicatorsRepo->save($request);
        return back();
    }

    public function data(Request $request){

        $data['search'] = (object) $request->all();
        $data['data'] = $this->indicatorsRepo->get_data($request);
        return view('kpi.index',$data);
    }

    
}
