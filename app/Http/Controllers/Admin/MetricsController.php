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

      //cache for 6 hours
        $minutes = 60*6;
        
        $chart_data  = cache()->remember('metrics_chart_data',$minutes, function () {

            $data['visits_by_country'] = $this->metricsRepository->country_access();
            $data['signups_by_country']= $this->metricsRepository->country_signups();
            $data['monthly_signups']= $this->metricsRepository->monthly_signups();
            $data['monthly_publications']= $this->metricsRepository->monthly_publications();

            return $data;
        });

        return view('admin.metrics.index',["chart_data"=>$chart_data]);
    }


  
}
