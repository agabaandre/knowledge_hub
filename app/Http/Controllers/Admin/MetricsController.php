<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MetricsRepository;

class MetricsController extends Controller
{
    private $metricsRepository;

    public function __construct(MetricsRepository $metricsRepository)
    {
        $this->metricsRepository = $metricsRepository;
    }

    public function index(Request $request){

        $data['visits_by_country'] = $this->metricsRepository->country_access();
        $data['signups_by_country']= $this->metricsRepository->country_signups();
        $data['monthly_signups']= $this->metricsRepository->monthly_signups();
        $data['monthly_publications']= $this->metricsRepository->monthly_publications();
        
        //dd(json_encode($data));

        return view('admin.metrics.index',["chart_data"=>$data]);
    }


  
}
