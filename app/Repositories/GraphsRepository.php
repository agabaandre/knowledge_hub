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

	public function get_countries($filter=null)
	{
		$use_filters = (isset($filter['region_id']) && $filter['region_id']>0)?true:false;

		// Use a more efficient query to get countries that have KPI data
		$data_countries = DB::table('kpi_data_view')
			->select('country_id')
			->distinct()
			->pluck('country_id');

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

	public function get_periods_years()
	{
		$data = DB::table('kpi_data_view')
			->select('period_year')
			->distinct()
			->pluck('period_year');
		return $data->toArray();
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

	public function kpi_data($filter = [])
	{

		$countries = [];
		$data      = [];
		$count     = 0;

		//get kpis that have data
		foreach ($this->get_kpis($filter) as $kpi) :

			//for each country, get value for the select kpi
			foreach ($this->get_countries($filter) as $country) {

				$filter['kpi_id']     = $kpi->id;
				$filter['country_id'] = $country->id;

				$results    = $this->get($filter, "country_id");

				$data_value = array_column($results, 'kpi_value');

				$data[$count]["name"]    = $kpi->name;
				$col_value               = (count($data_value) > 0) ? array_sum($data_value) / count($data_value):0;
				$data[$count]["data"][]  = (float) $col_value;
				$countries[] = $country->name;
			}

			$count++;

		endforeach;

		return array('labels' => $countries, 'data' => $data);
	}

	//country wise graph
	public function countries_data($filter = [])
	{

		$periods = [];
		$data    = [];

		foreach ($this->get_periods_years() as $period) :
			$count   = 0;

			foreach ($this->get_kpis($filter) as $kpi) {

				$filter['kpi_id']      = $kpi->id;
				$filter['period_year'] = $period;

				$results    = $this->get($filter, "period_year");

				$data_value = array_column($results, 'kpi_value');

				$kpi_avg = (count($data_value) > 0) ? array_sum($data_value) / count($data_value) : 0;

				if (isset($data[$count])) {

					array_push($data[$count]["data"], $kpi_avg);

				} else {

					$data[$count]["name"]   = $kpi->name;
					$data[$count]["data"][] = $kpi_avg;
				}

				$count++;
			}

			$periods[] = $period;

		endforeach;

		return array('labels' => $periods, 'data' => array_values($data));
	}

	public function region_countries($region_id){

	  return Country::where('region_id',$region_id)->get()->pluck('id');
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
	public function get_country_kpis_with_previous_year($filter = [], $current_year = null)
	{
		if (!$current_year) {
			$current_year = date('Y');
		}
		$previous_year = $current_year - 1;

		$kpi_ids = $this->get_kpis($filter, true);

		if(count($kpi_ids) == 0)
			return [];

		// Get current year data
		$current_filter = $filter;
		$current_filter['period_year'] = $current_year;
		
		$current_query = DB::table('kpi_data_view as kdv1')
			->join('kpi', 'kpi.id', '=', 'kdv1.kpi_id')
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