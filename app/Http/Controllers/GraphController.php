<?php

namespace App\Http\Controllers;


use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;
class GraphController extends Controller
{
    private $dashRepo;

    public function __construct(DashboardRepository $dashRepo)
    {
        $this->dashRepo = $dashRepo;
    }


	public function index(Request $request)
	{
		
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";

		$filter = $request->all();
		$current_year   = "2022"; //date('Y');

		$data['countries']    = $this->dashRepo->get_countries($filter)->toArray();
		$data['subjectareas'] = $this->dashRepo->get_subjectareas();

        $data['filter']       = $filter;
		$data['year']         = $current_year;

		$subject_area_id      = (isset($filter['subject_area']))?$filter['subject_area']:null;//subject area option

		foreach ($this->dashRepo->get_subject_area($subject_area_id) as $key=>$subject_area):

			$filter['subject_area']  = $subject_area->id;
			$data['years_data'][$key]['subject_area']    = $subject_area->name;
			$data['years_data'][$key]['subject_area_id'] = $subject_area->id;
			$data['years_data'][$key]['data'] = $this->get_year_data($filter, $current_year);

			$graph_filter = $filter;
			$graph_filter['period_year'] = $current_year;

	    endforeach;

        $data['years']     =  $this->dashRepo->get_periods_years();

		return view('dashboards.home', $data);

	}

	public function get_year_data($filter, $current_year)
	{

		$previous_year         = $current_year - 1;
		$filter['period_year'] = $current_year;
		
		$results = $this->dashRepo->get_country_kpis($filter);

		foreach ($results as $row) {

			unset($filter['kpi_ids']);

			$filter['kpi_id']      = $row->kpi_id;
			$filter['period_year'] = $previous_year;
			$prev_year             = $this->dashRepo->get_country_kpis($filter, true);
			$row->previous_year    = (isset($prev_year->kpi_value)) ? $prev_year->kpi_value : 0;
		}

		return $results;
	}

	public function kpi_comparison()
	{
		$data['title']     = "RRC Dashboards";
		$data['uptitle']   = "RCC Dashboards";
		$data['kpis']      =  $this->dashRepo->get_kpis();
		$data['years']     =  $this->dashRepo->get_periods_years();

		return view('dashboards.kpi_comparison', $data);
	}


	public function country_comparison()
	{
		$data['title']     = "RRC Dashboards";
		$data['uptitle']   = "RCC Dashboards";
		$data['kpis']      =  $this->dashRepo->get_kpis();
		$data['countries'] = $this->dashRepo->get_countries();

		return view('dashboards.country_comparison', $data);
	}


	public function country_comparison_data(Request $request)
	{
		$data = $this->dashRepo->countries_data($request->all());
		die(json_encode($data));
	}

	public function kpi_comparison_data(Request $request)
	{
        $subject_area_id = $request->subject_area_id;
    
		$filter = $request->all();


		if(isset($filter['country_id']) && intval($filter['country_id'])>0):
			$data = $this->dashRepo->countries_data($filter);
	    else:
	    	$data = $this->dashRepo->kpi_data($filter);
	    endif;

		die(json_encode($data));
	}

    
}
