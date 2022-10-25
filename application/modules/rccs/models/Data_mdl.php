<?php
defined('BASEPATH') or exit('No direct script access allowed');

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

    public function get($filter=[],$group_by=null,$columns=null){

		if(!empty($filter)) {
			foreach ($filter as $key => $value) {

			if(!empty($value))
			   $this->db->where($key, $value);
			}
		}

		if($columns) {
			$this->db->select($columns);
		}

		if($group_by)
			$this->db->group_by($group_by);

		$records = $this->db->get($this->data_view)->result();

		// foreach ($records as $row):
		// 	$row->subject_area = $this->get_subject_area($row->subject_area_id);
		// endforeach;

		return $records;
	}

	public function get_subject_area($id){

		$this->db->where('id',$id);
		return $this->db->get($this->subject_area_tb)->row();
	}

	public function get_country($id){

		$this->db->where('id',$id);
		return $this->db->get($this->countries_tb)->row();
	}

	public function get_countries(){
		$this->db->where('national','National');
		return $this->db->get($this->countries_tb)->result();
	}

	public function get_periods_years(){
		$this->db->select('period_year');
		$this->db->group_by('period_year');
		return array_column($this->db->get($this->data_view)->result_array(),'period_year');
	}

	public function get_kpis($filter=[]){

		if(!empty($filter)) {
			foreach ($filter as $key => $value) {

			if(!empty($value)){
			   if($key=="kpi_id"):
			   		$this->db->where('id', $value);
				endif;
			}

			}
		}

		return $this->db->get($this->kpi_tb)->result();
	}

		public function kpi_data($filter=[]){

		$countries = [];
		$data      = [];
		$count     = 0;

		foreach ($this->get_kpis($filter) as $kpi):

			foreach ($this->get_countries() as $country){

			$filter['kpi_id']     = $kpi->id;
			$filter['country_id']     = $country->id;

			$results    = $this->get($filter,"country_id");
			$data_value = array_column($results,'kpi_value');

			$data[$count]["name"]  = $kpi->name;
			$data[$count]["data"][]  = (count($data_value)>0)?(double) $data_value[0]:0;
			$countries[] = $country->name;
		
			}

			
			$count++;

	   endforeach;

		return array('labels'=>$countries,'data'=>$data);
	}


	public function countries_data($filter=[]){

		$periods = [];
		$data    = [];
		$count   = 0;

		foreach ($this->get_periods_years() as $period):

			foreach ($this->get_kpis($filter) as $kpi){

				$filter['kpi_id']      = $kpi->id;
				$filter['period_year'] = $period;

				$results    = $this->get($filter,"period_year");
				$data_value = array_column($results,'kpi_value');

				//if(count($data_value)>0):
					$data[$count]["name"]   = $kpi->name;
					$data[$count]["data"][] = (count($data_value)>0)?(double) $data_value[0]:0;
				//endif;
		     }
		
			$count++;			
			$periods[] = $period;

       endforeach;

		return array('labels'=>$periods,'data'=>$data);
	}


     // Call stored Prodcedure
    public  function callProcedure($name, $paramsArray=[]) {

    	// call stored procedure by argument $name
        $statement = "call $name('";  
        // values are merged as string
        $arguments  = implode("','", $paramsArray);
        $statement .= $arguments. "')";
    	
    	$query = CI_INSTANCE()->db->query($statement);
    	
        $data = $query->result();
        
         mysqli_next_result(CI_INSTANCE()->db->conn_id);
        $query->free_result();

        return $data;
    }

}
