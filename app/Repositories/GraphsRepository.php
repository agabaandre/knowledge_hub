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

       $data_countries = array_column($this->exec_query('SELECT country_id from kpi_data GROUP BY country_id '),'country_id');

        $countries = DB::table('country')
                ->where('national','National')
                ->whereIn('id',$data_countries)
                ->when($use_filters, function ($query, $use_filters) use($filter) {

                    return $query->where('region_id',$filter['region_id']);

                });
				
				// ->when(true,function($query){

				// 	return $this->access_filter($query,true);
				// });

				$records = $countries->get();

		return $records;
	}

	public function get_data_kpis($filter=[])
	{
		$kpi_ids_with_data = KpiData::orderBy('kpi_id','desc');


		if(isset($filter['region_id'])){
			$country_ids = $this->region_countries($filter['region_id']);
			$kpi_ids_with_data->whereIn('country_id',$country_ids->toArray());
		}
		
		$this->access_filter($kpi_ids_with_data,true);

		$kpi_ids = $kpi_ids_with_data->get()->pluck('kpi_id');

		$kpis     =  Kpi::whereIn('id',$kpi_ids->toArray())->get()->pluck('id');

		return $kpis;
	}

	public function get_periods_years()
	{
        $data = $this->exec_query("SELECT period_year FROM kpi_data GROUP BY period_year");
		return array_column($data, 'period_year');
	}

	public function get_kpis($filter = [], $only_ids=false)
	{

		$query = Kpi::whereIn('id',$this->get_data_kpis($filter));

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
	public function get_country_kpis($filter = [], $get_row = false)
	{

		$kpi_ids = $this->get_kpis($filter,true);

		if(count($kpi_ids) == 0)
			return [];

        $query = DB::table('kpi_data')
         ->when(count($kpi_ids) > 0, function ($query) use($kpi_ids){
             return $query->whereIn('kpi_id',$kpi_ids->toArray());
         });

		if(isset($filter['region_id'])){

			$country_ids = $this->region_countries($filter['region_id']);

			if(count($country_ids) == 0)
			return [];

			$query->when(count($country_ids) > 0, function ($query) use($country_ids) {
				return $query->whereIn('country_id', $country_ids->toArray());
			});
		}


		if (!empty($filter)) {

			foreach ($filter as $key => $value) {

				if (!empty($value)) {

					$is_intended = ($key == "kpi_id" || $key == "country_id")?true:false;

                    $query->when($is_intended, function ($query, $kpi_ids) use($key,$value) {
                        return $query->where($key, $value);
                    });

				}
			}
		}

		$latest_periods = DB::table('kpi_data')->select(DB::raw('max(period) as period'))
		->groupBy(['kpi_id','country_id'])
		->pluck('period');

		$query->select(DB::raw('kpi_name,max(period) as period,kpi_value,kpi_id'));
		$query->whereIn('period',$latest_periods);
        $results = $query->groupBy(['kpi_id','country_id'])->get();
       
		return ($get_row) ? $results->toArray()[0] : $results->toArray();
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

		$latest_periods = DB::table('kpi_data')->select(DB::raw('max(period) as period'))
		->groupBy(['kpi_id','country_id'])
		->pluck('period');

		foreach ($this->get_kpis($filter) as $kpi) :

            $row = $this->exec_query("SELECT kpi_name,kpi_value,kpi_id FROM kpi_data where kpi_id='$kpi->id' and period in $latest_periods")[0];
            
			$data[$count]['name']   = $kpi->name;
			$data[$count]['data'][] = intval($row->kpi_value);

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