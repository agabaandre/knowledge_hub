<?php

namespace App\Http\Controllers;


use App\Models\Region;
use App\Models\SubjectArea;
use App\Repositories\GraphsRepository;
use App\Support\MetricsCache;
use Illuminate\Http\Request;
class GraphController extends Controller
{
    private $dashRepo;

    public function __construct(GraphsRepository $dashRepo)
    {
        $this->dashRepo = $dashRepo;
    }

	public function index(Request $request)
	{
		
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";

		$filter = $request->all();
		$current_year   = date('Y');

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

	public function rcc_admin(Request $request)
	{
		$filter = $request->all();

		return view('admin.dashboard.rcc', [
			'title' => 'RCC Dashboard',
			'filter' => $filter,
			'regions' => Region::query()->orderBy('region_name')->get(),
			'subjectareas' => SubjectArea::query()->where('is_active', true)->orderBy('name')->get(),
			'indicators' => $this->dashRepo->get_published_map_indicators(),
			'years' => ($years = array_values(array_filter($this->dashRepo->get_periods_years()))) !== []
				? $years
				: [(int) date('Y')],
			'countries' => $this->dashRepo->get_countries($filter, true),
			'initial_payload' => $this->rccDashboardPayload($filter),
			'regions_json' => Region::query()->orderBy('region_name')->get()->map(fn ($r) => [
				'id' => (int) $r->id,
				'name' => $r->region_name,
			])->values(),
		]);
	}

	public function rcc_data(Request $request)
	{
		$filter = $request->all();
		$payload = $this->rccDashboardPayload($filter);

		return response()->json(array_merge($payload, [
			'cache' => [
				'redis' => MetricsCache::redisAvailable(),
				'ttl_seconds' => MetricsCache::ttl('rcc'),
			],
		]));
	}

	private function rccDashboardPayload(array $filter): array
	{
		$cacheKey = 'rcc_dashboard_'.md5(serialize($filter));

		return MetricsCache::store()->remember(
			$cacheKey,
			MetricsCache::ttl('rcc'),
			fn () => $this->dashRepo->get_rcc_dashboard_payload($filter)
		);
	}

	public function get_year_data($filter, $current_year)
	{
		// Use the optimized method that gets both current and previous year data in a single query
		$results = $this->dashRepo->get_country_kpis_with_previous_year($filter, $current_year);
		return $results;
	}

	public function kpi_comparison()
	{
		$data['title']     = "RRC Dashboards";
		$data['uptitle']   = "RCC Dashboards";
		$data['kpis']      =  $this->dashRepo->get_kpis();
		$data['years']     =  $this->dashRepo->get_periods_years();

		return view('dashboards.kpi', $data);
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
		$filter = $request->all();
		$use_country_filter = (isset($filter['country_id']));

		//if country selected, compare year values for the country
		if($use_country_filter):
			$data = $this->dashRepo->countries_data($filter);
	    else:
	    	$data = $this->dashRepo->kpi_data($filter);
	    endif;

		die(json_encode($data));
	}

    
}
