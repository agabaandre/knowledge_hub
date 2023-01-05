<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class Data_mdl extends CI_Model
{


	public function __Construct()
	{
		parent::__Construct();

		$this->data_view       = "kpi_data";
		$this->subject_area_tb = "subject_areas";
		$this->countries_tb    = "country";
		$this->kpi_tb    	   = "kpi";
	}

	public function get($filter = [], $group_by = null, $columns = null)
	{

		if (!empty($filter)) {
			foreach ($filter as $key => $value) {

				if($key=='subject_area'):
						$kpi_ids = $this->get_kpis(['subject_area'=>$filter['subject_area']],true);

						if(count($kpi_ids) == 0)
							return [];
						
						if(count($kpi_ids) > 0)
						 $this->db->where_in('kpi_id', $kpi_ids);
				endif;

				if ($key=='kpi_id')
					$this->db->where($key, $value);
			}
		}

		if ($columns) {
			$this->db->select($columns);
		}

		if ($group_by)
			$this->db->group_by($group_by);

		$records = $this->db->get($this->data_view)->result();

		return $records;
	}

	public function subject_sub_themes($id){

		$this->db->where('thematic_area_id',$id);
		$result = $this->db->get($this->subject_area_tb)->result();

		$subtheme_ids = [];

		foreach ($result as $row) {

			array_push($subtheme_ids, $row_id);
		}

		return $subtheme_ids;
	}

	public function get_subject_area($id=null)
	{
		if($id):
			$this->db->where('id', $id);
			return $this->db->get($this->subject_area_tb)->row();
		else:
			return $this->db->get($this->subject_area_tb)->result();
	    endif;
	}

	public function get_country($id)
	{

		$this->db->where('id', $id);
		return $this->db->get($this->countries_tb)->row();
	}

	public function get_countries()
	{
		$this->db->where('national', 'National');
		return $this->db->get($this->countries_tb)->result();
	}

	public function get_periods_years()
	{
		$this->db->select('period_year');
		$this->db->group_by('period_year');

		return array_column($this->db->get($this->data_view)->result_array(), 'period_year');
	}

	public function get_kpis($filter = [], $only_ids=false)
	{

		if (!empty($filter)) {
			foreach ($filter as $key => $value) {

				if (!empty($value)) {
					if ($key == "kpi_id"){
						$this->db->where('id', $value);
					}
				   else if ($key == "subject_area"){
						$this->db->where("subject_area", $value);
				   }
				}
			}
		}

		$results = $this->db->get($this->kpi_tb)->result();

		if($only_ids){ //return an raary of ids

			$ids = [];

			foreach ($results as $row) {
				array_push($ids, $row->id);
			}

			return $ids;
		}

		return $results;
	}

	public function kpi_data($filter = [])
	{

		$countries = [];
		$data      = [];
		$count     = 0;

		foreach ($this->get_kpis($filter) as $kpi) :

			foreach ($this->get_countries() as $country) {

				$filter['kpi_id']     = $kpi->id;
				$filter['country_id'] = $country->id;

				$results    = $this->get($filter, "country_id");
				$data_value = array_column($results, 'kpi_value');

				$data[$count]["name"]  = $kpi->name;
				$data[$count]["data"][]  = (count($data_value) > 0) ? (float) $data_value[0] : 0;
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

	//get country kpi performance
	public function get_country_kpis($filter = [], $get_row = false)
	{

		$kpi_ids = $this->get_kpis(['subject_area'=>$filter['subject_area']],true);

		if(count($kpi_ids) == 0)
			return [];

		
		if(count($kpi_ids) > 0)
		 $this->db->where_in('kpi_id', $kpi_ids);

		if (!empty($filter)) {

			foreach ($filter as $key => $value) {

				if (!empty($value)) {

					if ($key == "kpi_id") {
						$this->db->where('kpi_id', $value);
					} 
				}
			}
		}

		$this->db->group_by('kpi_id');
		$this->db->select("kpi_name,AVG(kpi_value) as kpi_value,kpi_id");
		$qry = $this->db->get('kpi_data');
		return ($get_row) ? $qry->row() : $qry->result();
	}

	//for dashbaord chart
	public function country_year_kpis($filter = [])
	{

		$categories = [];
		$data    = [];
		$count   = 0;


		foreach ($this->get_kpis($filter) as $kpi) :

			$this->db->where('kpi_id', $kpi->id);
			$this->db->select("kpi_name,AVG(kpi_value) as kpi_value,kpi_id");
			$row = $this->db->get('kpi_data')->row();

			$data[$count]['name']   = $kpi->name;
			$data[$count]['data'][] = intval($row->kpi_value);

			$count++;

		endforeach;

		// return array('labels'=>$categories,'data'=>$data);
		return $data;
	}

	// Call stored Prodcedure
	public  function callProcedure($name, $paramsArray = [])
	{

		// call stored procedure by argument $name
		$statement = "call $name('";
		// values are merged as string
		$arguments  = implode("','", $paramsArray);
		$statement .= $arguments . "')";

		$query = CI_INSTANCE()->db->query($statement);

		$data = $query->result();

		mysqli_next_result(CI_INSTANCE()->db->conn_id);
		$query->free_result();

		return $data;
	}
}
