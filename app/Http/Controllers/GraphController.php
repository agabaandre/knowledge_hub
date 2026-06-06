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
		$meta = $this->rccPageMetadata($filter);
		$regions = $meta['regions'];
		$defaultPeriodYear = $meta['default_period_year'];

		return view('admin.dashboard.rcc', [
			'title' => 'RCC Dashboard',
			'filter' => $filter,
			'default_period_year' => $defaultPeriodYear,
			'regions' => $regions,
			'subjectareas' => $meta['subjectareas'],
			'indicators' => $meta['indicators'],
			'years' => $meta['years'],
			'countries' => $meta['countries'],
			'initial_payload' => null,
			'regions_json' => $regions->map(fn ($r) => [
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

	private function rccPageMetadata(array $filter): array
	{
		$store = MetricsCache::store();
		$ttl = MetricsCache::ttl('rcc_meta');

		$regions = $store->remember('rcc_meta_regions', $ttl, fn () => Region::query()
			->orderBy('region_name')
			->get(['id', 'region_name']));

		$subjectareas = $store->remember('rcc_meta_subjectareas', $ttl, fn () => SubjectArea::query()
			->where('is_active', true)
			->orderBy('name')
			->get(['id', 'name']));

		$years = $store->remember('rcc_meta_years_published_desc', $ttl, function () {
			$years = $this->dashRepo->get_periods_years(true, true);

			return $years !== [] ? $years : [(int) date('Y')];
		});
		$defaultPeriodYear = $years[0] ?? (int) date('Y');

		$indicators = $store->remember('rcc_meta_indicators', $ttl, fn () => $this->dashRepo->get_published_map_indicators());

		$countriesKey = 'rcc_meta_countries_'.md5(serialize([
			'region_id' => (int) ($filter['region_id'] ?? 0),
		]));
		$countries = $store->remember($countriesKey, MetricsCache::ttl('rcc'), fn () => $this->dashRepo->get_countries($filter, true));

		return array_merge(compact('regions', 'subjectareas', 'years', 'indicators', 'countries'), [
			'default_period_year' => $defaultPeriodYear,
		]);
	}

	private function normalizedRccFilter(array $filter): array
	{
		$normalized = [];
		foreach (['region_id', 'country_id', 'subject_area', 'kpi_id', 'period_year'] as $key) {
			if (! empty($filter[$key])) {
				$normalized[$key] = (int) $filter[$key];
			}
		}

		$availableYears = $this->dashRepo->get_periods_years(true, true);
		$latestYear = $availableYears[0] ?? $this->dashRepo->get_latest_period_year(true);

		if (
			empty($normalized['period_year'])
			|| ($availableYears !== [] && ! in_array($normalized['period_year'], $availableYears, true))
		) {
			$normalized['period_year'] = $latestYear;
		}

		return $normalized;
	}

	private function rccDashboardPayload(array $filter): array
	{
		$normalized = $this->normalizedRccFilter($filter);
		$cacheKey = 'rcc_dashboard_v2_'.md5(json_encode($normalized));

		return MetricsCache::store()->remember(
			$cacheKey,
			MetricsCache::ttl('rcc'),
			fn () => $this->dashRepo->get_rcc_dashboard_payload($normalized)
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
