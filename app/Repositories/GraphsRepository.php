<?php
namespace App\Repositories;

use App\Models\Author;
use App\Models\Country;
use App\Models\Kpi;
use App\Models\KpiData;
use App\Models\Region;
use App\Models\SubjectArea;
use App\Models\SubThemeticArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraphsRepository extends SharedRepo{


	 // Retrieves KPIData based on the provided filters, from specified columns, if any
    public function get($filter = [], $group_by = null, $columns = null)
	{

        $query = KpiData::orderBY('kpi_id','desc');

		if (!empty($filter)) {
			foreach ($filter as $key => $value) {

				///if filter has subject area, get kpis for that subject area
				if($key=='subject_area'):
						$kpi_ids = $this->get_kpis($filter,true);

						//if no kpis for subject area return empty array
						if(count($kpi_ids) == 0)
							return []; 

						//if there're kpis, get there data
						if(count($kpi_ids) > 0)
                        $query->whereIn('kpi_id', $kpi_ids);
				endif;

				if($key=='region_id'):

					//region was selected, retrieve kpi data for countries in that region
					$country_ids = $this->region_countries($filter['region_id']);
					
					if(count($country_ids) > 0)
					$query->whereIn('country_id', $country_ids->toArray());

				endif;

				//kpi was supplied in filter, retrieve for just that kpi
				if ($key=='kpi_id')
                  $query->where($key, $value);
					
			}

			
		if(isset($filter['country_id']))

		   //if country selected, retrieve kpi data for that country

			$query->where('country_id', $filter['country_id']);
		}


		$this->access_filter($query,true);

       //if group by was provided , use that to group
		if ($group_by)
        $query->groupBy($group_by);

		$results = ($columns)?$query->get($columns):$query->get();

		return $results->toArray();
	}


	//Retrieve subtheme by tematic area id
	public function subject_sub_themes($id){

        $subtheme_ids= SubThemeticArea::where('thematic_area_id',$id)->get()->pluck('id');
		return $subtheme_ids;
	}

	public function get_subject_area($id=null)
	{
		if($id):
			return SubjectArea::where('id', $id)->get();
		else:
			return SubjectArea::all();
	    endif;
	}

	public function get_country($id)
	{
		return Country::find($id);
	}

	public function get_countries($filter=null, $publishedOnly = false)
	{
		$use_filters = (isset($filter['region_id']) && $filter['region_id']>0)?true:false;

		// Use a more efficient query to get countries that have KPI data
		$data_countries = DB::table('kpi_data_view as kdv')
			->when($publishedOnly, function ($query) {
				$query->join('kpi', 'kpi.id', '=', 'kdv.kpi_id')
					->where('kpi.status', 'published');
			})
			->select('kdv.country_id')
			->distinct()
			->pluck('kdv.country_id');

		$countries = DB::table('country')
			->where('national','National')
			->whereIn('id',$data_countries)
			->when($use_filters, function ($query) use($filter) {
				return $query->where('region_id',$filter['region_id']);
			})
			->get();

		return $countries;
	}

	public function get_data_kpis($filter=[], $publishedOnly = false)
	{
		// Use a more efficient query to get KPI IDs that have data
		$query = DB::table('kpi_data_view as kdv')
			->join('kpi', 'kpi.id', '=', 'kdv.kpi_id')
			->select('kdv.kpi_id')
			->distinct();

		if ($publishedOnly) {
			$query->where('kpi.status', 'published');
		}

		if(isset($filter['region_id'])){
			$country_ids = $this->region_countries($filter['region_id']);
			if(count($country_ids) > 0) {
				$query->whereIn('kdv.country_id', $country_ids->toArray());
			}
		}

		// Apply access filter more efficiently
		$user = current_user();
		if($user && $user->access_level) {
			$level = $user->access_level;
			
			if($level->level_name == "Country" && states_enabled()) {
				$query->where('kdv.country_id', $user->country_id);
			} elseif($level->level_name == "RCC" && states_enabled()) {
				// Get RCC countries directly
				$rcc_countries = Country::where('region_id', $user->country->region_id)->pluck('id');
				$query->whereIn('kdv.country_id', $rcc_countries);
			}
		}

		$kpi_ids = $query->pluck('kpi_id');
		return $kpi_ids;
	}

	public function get_periods_years(bool $publishedOnly = false, bool $descending = false): array
	{
		$query = DB::table('kpi_data_view as kdv')
			->select('kdv.period_year')
			->distinct();

		if ($publishedOnly) {
			$query->join('kpi', 'kpi.id', '=', 'kdv.kpi_id')
				->where('kpi.status', 'published');
		}

		$years = $query->pluck('period_year')
			->map(fn ($year) => (int) $year)
			->filter(fn ($year) => $year > 0)
			->unique()
			->values()
			->all();

		$descending ? rsort($years) : sort($years);

		return $years;
	}

	public function get_latest_period_year(bool $publishedOnly = true): int
	{
		$years = $this->get_periods_years($publishedOnly, true);

		return $years[0] ?? (int) date('Y');
	}

	public function get_kpis($filter = [], $only_ids=false, $publishedOnly = false)
	{

		$query = Kpi::whereIn('id',$this->get_data_kpis($filter, $publishedOnly));

		if ($publishedOnly) {
			$query->where('status', 'published');
		}

		if(!empty($filter)) {
			foreach ($filter as $key => $value) {

				if (!empty($value)) {

					if ($key == "kpi_id"){
						$query->where('id', $value);
					}

				   else if ($key == "subject_area"){
						$query->where("subject_area", $value);
				   }

				}
			}
		}

		$results = $query->get();

		if($only_ids){ //return an raary of ids
			$ids = $results->pluck('id');
            return $ids;
		}

		return $results;
	}

	public function kpi_data($filter = [], $publishedOnly = false)
	{
		$countryList = $this->get_countries($filter, $publishedOnly);
		$labels = [];
		foreach ($countryList as $country) {
			$labels[] = $country->name;
		}

		$kpis = $this->get_kpis($filter, false, $publishedOnly);
		$kpiIds = $kpis->pluck('id')->map(fn ($id) => (int) $id)->all();
		$countryIds = collect($countryList)->pluck('id')->map(fn ($id) => (int) $id)->all();

		if ($kpiIds === [] || $countryIds === []) {
			return ['labels' => $labels, 'data' => []];
		}

		$periodYear = ! empty($filter['period_year']) ? (int) $filter['period_year'] : (int) date('Y');
		$valueMap = $this->latestKpiValuesForYear($kpiIds, $countryIds, $periodYear, $publishedOnly);

		$data = [];
		foreach ($kpis as $kpi) {
			$series = ['name' => (string) $kpi->name, 'kpi_id' => (int) $kpi->id, 'data' => []];
			foreach ($countryList as $country) {
				$series['data'][] = (float) ($valueMap[$kpi->id][$country->id] ?? 0.0);
			}
			$data[] = $series;
		}

		return ['labels' => $labels, 'data' => array_values($data)];
	}

	//country wise graph
	public function countries_data($filter = [], $publishedOnly = false)
	{
		$countryId = (int) ($filter['country_id'] ?? 0);
		$kpis = $this->get_kpis($filter, false, $publishedOnly);
		$kpiIds = $kpis->pluck('id')->map(fn ($id) => (int) $id)->all();

		if ($countryId <= 0 || $kpiIds === []) {
			return ['labels' => [], 'data' => []];
		}

		$query = DB::table('kpi_data_view as kdv')
			->join('kpi', 'kpi.id', '=', 'kdv.kpi_id')
			->when($publishedOnly, fn ($q) => $q->where('kpi.status', 'published'))
			->where('kdv.country_id', $countryId)
			->whereIn('kdv.kpi_id', $kpiIds)
			->selectRaw('kdv.kpi_id, YEAR(kdv.period) as period_year, AVG(kdv.kpi_value) as kpi_value')
			->groupBy('kdv.kpi_id', DB::raw('YEAR(kdv.period)'))
			->orderBy('period_year')
			->get();

		$periods = [];
		$seriesByKpi = [];
		foreach ($query as $row) {
			$year = (int) $row->period_year;
			if (! in_array($year, $periods, true)) {
				$periods[] = $year;
			}
			$seriesByKpi[(int) $row->kpi_id][(int) $year] = (float) $row->kpi_value;
		}
		sort($periods);

		$data = [];
		foreach ($kpis as $kpi) {
			$series = ['name' => (string) $kpi->name, 'kpi_id' => (int) $kpi->id, 'data' => []];
			foreach ($periods as $period) {
				$series['data'][] = (float) ($seriesByKpi[$kpi->id][$period] ?? 0.0);
			}
			$data[] = $series;
		}

		return ['labels' => $periods, 'data' => array_values($data)];
	}

	public function region_countries($region_id){

	  return Country::where('region_id',$region_id)->get()->pluck('id');
	}

    /**
     * Latest KPI value per indicator and member state for a calendar year.
     *
     * @param  list<int>  $kpiIds
     * @param  list<int>  $countryIds
     * @return array<int, array<int, float>>
     */
    private function latestKpiValuesForYear(array $kpiIds, array $countryIds, int $periodYear, bool $publishedOnly = false): array
    {
        if ($kpiIds === [] || $countryIds === []) {
            return [];
        }

        $rows = DB::table('kpi_data_view as kdv1')
            ->join('kpi', 'kpi.id', '=', 'kdv1.kpi_id')
            ->when($publishedOnly, fn ($q) => $q->where('kpi.status', 'published'))
            ->whereIn('kdv1.kpi_id', $kpiIds)
            ->whereIn('kdv1.country_id', $countryIds)
            ->whereRaw('YEAR(kdv1.period) = ?', [$periodYear])
            ->whereRaw('kdv1.period = (
                SELECT MAX(kdv2.period)
                FROM kpi_data_view kdv2
                WHERE kdv2.kpi_id = kdv1.kpi_id
                AND kdv2.country_id = kdv1.country_id
                AND YEAR(kdv2.period) = ?
            )', [$periodYear])
            ->select(['kdv1.kpi_id', 'kdv1.country_id', 'kdv1.kpi_value'])
            ->get();

        $valueMap = [];
        foreach ($rows as $row) {
            $valueMap[(int) $row->kpi_id][(int) $row->country_id] = (float) $row->kpi_value;
        }

        return $valueMap;
    }

	//get country kpi performance
	public function get_country_kpis($filter = [], $get_row = false, $publishedOnly = true)
	{

		$kpi_ids = $this->get_kpis($filter, true, $publishedOnly);

		if(count($kpi_ids) == 0)
			return [];

        $query = DB::table('kpi_data_view as kdv1')
         ->join('kpi', 'kpi.id', '=', 'kdv1.kpi_id')
         ->when($publishedOnly, fn ($q) => $q->where('kpi.status', 'published'))
         ->when(count($kpi_ids) > 0, function ($query) use($kpi_ids){
             return $query->whereIn('kdv1.kpi_id',$kpi_ids->toArray());
         });

		if(isset($filter['region_id'])){

			$country_ids = $this->region_countries($filter['region_id']);

			if(count($country_ids) == 0)
			return [];

			$query->when(count($country_ids) > 0, function ($query) use($country_ids) {
				return $query->whereIn('kdv1.country_id', $country_ids->toArray());
			});
		}


		if (!empty($filter)) {

			foreach ($filter as $key => $value) {

				if (!empty($value)) {

					$is_intended = ($key == "kpi_id" || $key == "country_id")?true:false;

                    $query->when($is_intended, function ($query) use($key,$value) {
                        return $query->where("kdv1.$key", $value);
                    });

				}
			}
		}

		// Use a more efficient approach with window function to get latest period data
		$query->select([
			'kdv1.kpi_name',
			'kdv1.period',
			'kdv1.kpi_value',
			'kdv1.kpi_id',
			'kdv1.country_id',
			'kdv1.subject_area_id',
			'kdv1.unit_label',
			'kdv1.owid_url',
			'kdv1.owid_chart_slug',
		])
		->whereRaw('kdv1.period = (
			SELECT MAX(kdv2.period) 
			FROM kpi_data_view kdv2 
			WHERE kdv2.kpi_id = kdv1.kpi_id 
			AND kdv2.country_id = kdv1.country_id
		)');

        $results = $query->get();
       
		return ($get_row) ? $results->toArray()[0] : $results->toArray();
	}

    /**
     * Historical KPI values per indicator for one member state (for sparklines / drill-down charts).
     *
     * @return array<int, array{labels: list<string>, values: list<float>}>
     */
    public function get_country_kpi_time_series(int $countryId, array $kpiIds, bool $publishedOnly = true): array
    {
        if ($countryId <= 0 || $kpiIds === []) {
            return [];
        }

        $query = DB::table('kpi_data_view as kdv')
            ->select(['kdv.kpi_id', 'kdv.period', 'kdv.kpi_value'])
            ->where('kdv.country_id', $countryId)
            ->whereIn('kdv.kpi_id', array_values(array_unique(array_map('intval', $kpiIds))))
            ->orderBy('kdv.period');

        if ($publishedOnly) {
            $query->join('kpi', 'kpi.id', '=', 'kdv.kpi_id')
                ->where('kpi.status', 'published');
        }

        $series = [];
        foreach ($query->get() as $row) {
            $kpiId = (int) $row->kpi_id;
            if (! isset($series[$kpiId])) {
                $series[$kpiId] = ['labels' => [], 'values' => []];
            }
            $series[$kpiId]['labels'][] = substr((string) $row->period, 0, 4);
            $series[$kpiId]['values'][] = (float) $row->kpi_value;
        }

        return $series;
    }

    public function group_country_kpis_by_subject(array $rows): array
    {
        $areaNames = SubjectArea::query()->pluck('name', 'id');
        $groups = [];

        foreach ($rows as $row) {
            $item = is_array($row) ? (object) $row : $row;
            $subjectId = (int) ($item->subject_area_id ?? 0);
            if (! isset($groups[$subjectId])) {
                $groups[$subjectId] = [
                    'subject_area_id' => $subjectId,
                    'subject_area_name' => $areaNames[$subjectId] ?? 'Other indicators',
                    'items' => [],
                ];
            }
            $groups[$subjectId]['items'][] = $item;
        }

        uasort($groups, fn ($a, $b) => strcmp($a['subject_area_name'], $b['subject_area_name']));

        return array_values($groups);
    }

	// Optimized method to get country KPIs with previous year data in a single query
	public function get_country_kpis_with_previous_year($filter = [], $current_year = null, $publishedOnly = false)
	{
		if (!$current_year) {
			$current_year = date('Y');
		}
		$previous_year = $current_year - 1;

		$kpi_ids = $this->get_kpis($filter, true, $publishedOnly);

		if(count($kpi_ids) == 0)
			return [];

		// Get current year data
		$current_filter = $filter;
		$current_filter['period_year'] = $current_year;
		
		$current_query = DB::table('kpi_data_view as kdv1')
			->join('kpi', 'kpi.id', '=', 'kdv1.kpi_id')
			->when($publishedOnly, fn ($q) => $q->where('kpi.status', 'published'))
			->when(count($kpi_ids) > 0, function ($query) use($kpi_ids){
				return $query->whereIn('kdv1.kpi_id',$kpi_ids->toArray());
			});

		if(isset($filter['region_id'])){
			$country_ids = $this->region_countries($filter['region_id']);
			if(count($country_ids) == 0)
				return [];
			$current_query->when(count($country_ids) > 0, function ($query) use($country_ids) {
				return $query->whereIn('kdv1.country_id', $country_ids->toArray());
			});
		}

		if (!empty($filter)) {
			foreach ($filter as $key => $value) {
				if (!empty($value)) {
					$is_intended = ($key == "kpi_id" || $key == "country_id")?true:false;
					$current_query->when($is_intended, function ($query) use($key,$value) {
						return $query->where("kdv1.$key", $value);
					});
				}
			}
		}

		$current_query->select([
			'kdv1.kpi_name',
			'kdv1.period',
			'kdv1.kpi_value',
			'kdv1.kpi_id',
			'kdv1.country_id',
			'kpi.owid_url',
			'kpi.owid_chart_slug',
		])
		->whereRaw('kdv1.period = (
			SELECT MAX(kdv2.period) 
			FROM kpi_data_view kdv2 
			WHERE kdv2.kpi_id = kdv1.kpi_id 
			AND kdv2.country_id = kdv1.country_id
			AND YEAR(kdv2.period) = ?
		)', [$current_year]);

		$current_results = $current_query->get();

		// Get previous year data for the same KPIs and countries
		$kpi_country_pairs = $current_results->map(function($item) {
			return ['kpi_id' => $item->kpi_id, 'country_id' => $item->country_id];
		});

		$previous_data = collect();
		if ($kpi_country_pairs->count() > 0) {
			$previous_query = DB::table('kpi_data_view as kdv1')
				->select([
					'kdv1.kpi_id',
					'kdv1.country_id',
					'kdv1.kpi_value'
				])
				->whereRaw('kdv1.period = (
					SELECT MAX(kdv2.period) 
					FROM kpi_data_view kdv2 
					WHERE kdv2.kpi_id = kdv1.kpi_id 
					AND kdv2.country_id = kdv1.country_id
					AND YEAR(kdv2.period) = ?
				)', [$previous_year]);

			// Add the kpi_id and country_id conditions
			$previous_query->where(function($query) use($kpi_country_pairs) {
				foreach($kpi_country_pairs as $pair) {
					$query->orWhere(function($q) use($pair) {
						$q->where('kdv1.kpi_id', $pair['kpi_id'])
						  ->where('kdv1.country_id', $pair['country_id']);
					});
				}
			});

			$previous_data = $previous_query->get()->keyBy(function($item) {
				return $item->kpi_id . '_' . $item->country_id;
			});
		}

		// Merge current and previous year data
		$results = $current_results->map(function($item) use($previous_data) {
			$key = $item->kpi_id . '_' . $item->country_id;
			$item->previous_year = $previous_data->get($key)?->kpi_value ?? 0;
			return $item;
		});

		return $results->toArray();
	}

    private function exec_query($query){

        return DB::select(
            DB::raw($query)
        );
    }

	//for dashbaord chart
	public function country_year_kpis($filter = [])
	{
		$data    = [];
		$count   = 0;

		foreach ($this->get_kpis($filter) as $kpi) :

            // Use a more efficient query to get the latest period data for this KPI
            $row = DB::table('kpi_data_view as kdv1')
                ->select(['kdv1.kpi_name', 'kdv1.kpi_value', 'kdv1.kpi_id'])
                ->where('kdv1.kpi_id', $kpi->id)
                ->whereRaw('kdv1.period = (
                    SELECT MAX(kdv2.period) 
                    FROM kpi_data_view kdv2 
                    WHERE kdv2.kpi_id = kdv1.kpi_id
                )')
                ->first();
            
			$data[$count]['name']   = $kpi->name;
			$data[$count]['data'][] = intval($row ? $row->kpi_value : 0);

			$count++;

		endforeach;

		return $data;
	}
    

	public function get_subjectareas(){

		return SubjectArea::all();
	}


    /**
     * Published indicators that have country-level data (for member-states map).
     */
    public function get_published_map_indicators()
    {
        $kpiIds = $this->get_data_kpis([], true);

        if (count($kpiIds) === 0) {
            return collect();
        }

        return Kpi::query()
            ->with('subjectArea')
            ->where('status', 'published')
            ->whereIn('id', $kpiIds->toArray())
            ->orderBy('name')
            ->get();
    }

    /**
     * Member-state choropleth: publications (kpi_id 0) or a published OWID indicator.
     */
    public function get_member_state_map_values(int $kpiId, ?int $regionId = null, ?string $mapContext = 'frontend_countries'): array
    {
        if ($kpiId === 0) {
            return app(AreasRepository::class)->get_publications_map_values($regionId, $mapContext);
        }

        return $this->get_indicator_map_values($kpiId, $regionId);
    }

    /**
     * Choropleth data for one indicator, optionally scoped to a region.
     */
    public function get_indicator_map_values(int $kpiId, ?int $regionId = null): array
    {
        if ($kpiId <= 0) {
            return [
                'kpi_id' => 0,
                'kpi_name' => '',
                'unit_label' => '',
                'aggregation' => 'average',
                'country_count' => 0,
                'aggregate' => null,
                'points' => [],
                'min' => null,
                'max' => null,
            ];
        }

        $filter = ['kpi_id' => $kpiId];
        if ($regionId) {
            $filter['region_id'] = $regionId;
        }

        $rows = $this->get_country_kpis($filter, false, true);
        $kpi = Kpi::query()->find($kpiId);
        $kpiName = $kpi->name ?? '';
        $unitLabel = $kpi->unit_label ?? '';

        $countryQuery = Country::query()
            ->where('region_id', '>', 0)
            ->whereNotNull('iso_code')
            ->where('iso_code', '!=', '');

        if ($regionId) {
            $countryQuery->where('region_id', $regionId);
        }

        $countriesById = $countryQuery->get(['id', 'name', 'iso_code', 'iso3_code', 'slug', 'region_id'])->keyBy('id');
        $rowsByCountry = collect($rows)->map(fn ($row) => (object) $row)->keyBy('country_id');

        $points = [];
        $numericValues = [];

        foreach ($countriesById as $countryId => $country) {
            $row = $rowsByCountry->get($countryId);
            if (! $row) {
                continue;
            }

            $value = (float) $row->kpi_value;
            $numericValues[] = $value;
            $display = kpi_indicator_display($value, $row->unit_label ?? $unitLabel, $row->kpi_name ?? $kpiName);

            $points[] = map_point_from_country($country, [
                'country_id' => (int) $countryId,
                'name' => $country->name,
                'value' => $value,
                'display_value' => $display['value_with_unit'],
                'unit_plain' => $display['unit_plain'],
                'period' => substr((string) ($row->period ?? ''), 0, 4),
                'detail_url' => country_detail_url($country),
            ], 'frontend_countries');
        }

        $aggregation = kpi_aggregate_method($kpiName, $unitLabel);
        $aggregateValue = $numericValues === []
            ? null
            : ($aggregation === 'sum'
                ? array_sum($numericValues)
                : array_sum($numericValues) / count($numericValues));

        return [
            'kpi_id' => $kpiId,
            'kpi_name' => $kpiName,
            'unit_label' => $unitLabel,
            'aggregation' => $aggregation,
            'aggregation_label' => kpi_aggregate_label($aggregation),
            'country_count' => count($numericValues),
            'aggregate' => $aggregateValue !== null
                ? kpi_indicator_display($aggregateValue, $unitLabel, $kpiName)
                : null,
            'points' => map_expand_choropleth_points($points),
            'min' => $numericValues === [] ? null : min($numericValues),
            'max' => $numericValues === [] ? null : max($numericValues),
        ];
    }

    /**
     * Indicator aggregate cards for map sidebar (continental or regional scope).
     */
    public function get_indicator_summaries_for_scope(?int $regionId = null): array
    {
        $indicators = $this->get_published_map_indicators()->keyBy('id');
        if ($indicators->isEmpty()) {
            return [];
        }

        $filter = $regionId ? ['region_id' => $regionId] : [];
        $rows = collect($this->get_country_kpis($filter, false, true));
        $summaries = [];

        foreach ($rows->groupBy('kpi_id') as $kpiId => $kpiRows) {
            $kpi = $indicators->get((int) $kpiId);
            if (! $kpi) {
                continue;
            }

            $numericValues = $kpiRows
                ->map(fn ($row) => (float) (is_array($row) ? $row['kpi_value'] : $row->kpi_value))
                ->all();

            if ($numericValues === []) {
                continue;
            }

            $aggregation = kpi_aggregate_method($kpi->name, $kpi->unit_label ?? '');
            $aggregateValue = $aggregation === 'sum'
                ? array_sum($numericValues)
                : array_sum($numericValues) / count($numericValues);

            $summaries[] = [
                'kpi_id' => (int) $kpi->id,
                'name' => $kpi->name,
                'subject_area' => $kpi->subjectArea->name ?? 'Other indicators',
                'aggregation' => $aggregation,
                'aggregation_label' => kpi_aggregate_label($aggregation),
                'country_count' => count($numericValues),
                'display' => kpi_indicator_display($aggregateValue, $kpi->unit_label ?? '', $kpi->name),
            ];
        }

        usort($summaries, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $summaries;
    }

    /**
     * Continental summaries for sidebar (all AU member states).
     */
    public function get_continental_indicator_summaries(): array
    {
        return $this->get_indicator_summaries_for_scope(null);
    }

    /**
     * Regional summaries for one indicator across all regions.
     */
    public function get_regional_indicator_summaries(int $kpiId): array
    {
        $summaries = [];

        foreach (Region::query()->orderBy('region_name')->get() as $region) {
            $data = $this->get_indicator_map_values($kpiId, (int) $region->id);
            if ($data['country_count'] === 0 || empty($data['aggregate'])) {
                continue;
            }

            $summaries[] = [
                'region_id' => (int) $region->id,
                'region_name' => $region->region_name,
                'country_count' => $data['country_count'],
                'aggregation' => $data['aggregation'],
                'aggregation_label' => $data['aggregation_label'],
                'display' => $data['aggregate'],
            ];
        }

        return $summaries;
    }

    /**
     * RCC admin dashboard: map, charts, KPI cards, and table (published indicators only).
     */
    public function get_rcc_dashboard_payload(array $filter): array
    {
        $regionId = ! empty($filter['region_id']) ? (int) $filter['region_id'] : null;
        $countryId = ! empty($filter['country_id']) ? (int) $filter['country_id'] : null;
        $subjectAreaId = ! empty($filter['subject_area']) ? (int) $filter['subject_area'] : null;
        $kpiId = ! empty($filter['kpi_id']) ? (int) $filter['kpi_id'] : null;
        $periodYear = ! empty($filter['period_year'])
            ? (int) $filter['period_year']
            : $this->get_latest_period_year(true);

        $kpiFilter = array_filter([
            'region_id' => $regionId,
            'country_id' => $countryId,
            'subject_area' => $subjectAreaId,
            'kpi_id' => $kpiId,
        ], fn ($v) => $v !== null && $v !== '');

        $mapKpiId = $kpiId ?? 0;
        $map = $this->get_member_state_map_values($mapKpiId, $regionId, 'admin_rcc');

        $chartFilter = $kpiFilter;
        if ($countryId) {
            $chart = $this->countries_data($chartFilter, true);
            $chart['mode'] = 'timeline';
            $chart['title'] = 'Indicator trends over time';
            $chart['y_axis_title'] = 'Average value';
        } else {
            $chartFilter['period_year'] = $periodYear;
            $chart = $this->kpi_data($chartFilter, true);
            $chart['mode'] = 'countries';
            $selectedKpi = $kpiId ? Kpi::query()->find($kpiId) : null;
            $chart['title'] = $selectedKpi
                ? $selectedKpi->name.' by member state ('.$periodYear.')'
                : 'Published indicators by member state ('.$periodYear.')';
            $chart['y_axis_title'] = ($selectedKpi && $selectedKpi->unit_label) ? $selectedKpi->unit_label : 'Indicator value';
        }
        $chart['data'] = array_values(array_map(function ($series) {
            $series['name'] = (string) ($series['name'] ?? 'Indicator');

            return $series;
        }, $chart['data'] ?? []));

        $rawTableRows = collect($this->get_country_kpis($kpiFilter, false, true));
        $countryIds = $rawTableRows
            ->map(fn ($row) => (int) ((object) $row)->country_id)
            ->unique()
            ->filter()
            ->values()
            ->all();
        $countryNames = $countryIds === []
            ? collect()
            : Country::query()->whereIn('id', $countryIds)->pluck('name', 'id');

        $tableRows = $rawTableRows
            ->map(function ($row) use ($countryNames) {
                $row = (object) $row;
                $display = kpi_indicator_display((float) $row->kpi_value, $row->unit_label ?? null, $row->kpi_name ?? null);

                return [
                    'country_id' => (int) $row->country_id,
                    'country_name' => $countryNames[(int) $row->country_id] ?? '—',
                    'kpi_id' => (int) $row->kpi_id,
                    'kpi_name' => $row->kpi_name,
                    'period' => substr((string) $row->period, 0, 4),
                    'value' => (float) $row->kpi_value,
                    'display_value' => $display['value_with_unit'],
                    'unit_plain' => $display['unit_plain'],
                ];
            })
            ->sortBy(['country_name', 'kpi_name'])
            ->values()
            ->all();

        $rawSnapshots = $this->get_country_kpis_with_previous_year($kpiFilter, $periodYear, true);
        $subjectGroups = $this->serialize_rcc_subject_groups(
            $this->group_country_kpis_by_subject($rawSnapshots),
            $periodYear
        );
        $subjectCharts = $this->build_rcc_subject_charts($subjectGroups, $periodYear);

        $regionName = null;
        if ($regionId) {
            $region = Region::query()->find($regionId);
            $regionName = $region ? $region->region_name : null;
        }
        $countryName = null;
        if ($countryId) {
            $country = Country::query()->find($countryId);
            $countryName = $country ? $country->name : null;
        }

        return [
            'map' => $map,
            'indicator_summaries' => $this->get_indicator_summaries_for_scope($regionId),
            'chart' => $chart,
            'subject_charts' => $subjectCharts,
            'table' => $tableRows,
            'subject_groups' => $subjectGroups,
            'meta' => [
                'period_year' => $periodYear,
                'region_id' => $regionId,
                'region_name' => $regionName,
                'country_id' => $countryId,
                'country_name' => $countryName,
                'subject_area_id' => $subjectAreaId,
                'kpi_id' => $kpiId,
                'map_kpi_id' => $mapKpiId,
                'row_count' => count($tableRows),
            ],
        ];
    }

    private function serialize_rcc_subject_groups(array $groups, int $periodYear): array
    {
        return array_map(function (array $group) use ($periodYear) {
            $items = [];
            foreach ($group['items'] ?? [] as $item) {
                $item = is_array($item) ? (object) $item : $item;
                $display = kpi_indicator_display((float) $item->kpi_value, $item->unit_label ?? null, $item->kpi_name ?? null);
                $prevDisplay = kpi_indicator_display((float) ($item->previous_year ?? 0), $item->unit_label ?? null, $item->kpi_name ?? null);
                $items[] = [
                    'kpi_id' => (int) ($item->kpi_id ?? 0),
                    'kpi_name' => (string) ($item->kpi_name ?? ''),
                    'kpi_value' => (float) ($item->kpi_value ?? 0),
                    'previous_year' => (float) ($item->previous_year ?? 0),
                    'period' => substr((string) ($item->period ?? ''), 0, 4),
                    'display' => $display,
                    'prev_display' => $prevDisplay,
                ];
            }

            return [
                'subject_area_id' => (int) ($group['subject_area_id'] ?? 0),
                'subject_area_name' => (string) ($group['subject_area_name'] ?? 'Other indicators'),
                'items' => $items,
            ];
        }, $groups);
    }

    private function build_rcc_subject_charts(array $subjectGroups, int $periodYear): array
    {
        $charts = [];
        foreach ($subjectGroups as $group) {
            if (empty($group['items'])) {
                continue;
            }
            $labels = [];
            $values = [];
            foreach ($group['items'] as $item) {
                $labels[] = $item['kpi_name'];
                $values[] = (float) $item['kpi_value'];
            }
            $charts[] = [
                'subject_area_name' => $group['subject_area_name'],
                'title' => $group['subject_area_name'].' ('.$periodYear.')',
                'labels' => $labels,
                'series_name' => 'Latest value',
                'data' => $values,
            ];
        }

        return $charts;
    }

	// Call stored Prodcedure
	 // Call stored Prodcedure
     public  function callProcedure($name, $paramsArray=[]) {
    	// call stored procedure by argument $name
        $statement = "call $name('";  
        // values are merged as string
        $arguments = implode("','", $paramsArray);
        $statement .= $arguments. "')";
    
        $results = DB::select(
            DB::raw($statement)
        );
        return $results;
    }


}