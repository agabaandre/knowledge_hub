<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rccdashboards extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "rccdashboards";
		$this->load->model("Rccs_mdl", 'rccs_mdl');
		$this->load->model("Data_mdl", 'data_mdl');
	}

	public function index()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";

		$filter = $this->input->get();
		$current_year   = "2022"; //date('Y');

		$data['countries'] = $this->data_mdl->get_countries($filter);
		$data['regions']   = $this->geoareasmodel->getrcc();
		$data['subjectareas'] = $this->data_mdl->get_subjectareas();
		$data['filter']    = $filter;
		$data['year']      = $current_year;

		$subject_area_id = (isset($filter['subject_area']))?$filter['subject_area']:null;
		
		foreach ($this->data_mdl->get_subject_area($subject_area_id) as $key=>$subject_area):

			$filter['subject_area']  = $subject_area->id;
			$data['years_data'][$key]['subject_area'] = $subject_area->name;
			$data['years_data'][$key]['subject_area_id'] = $subject_area->id;
			$data['years_data'][$key]['data'] = $this->get_year_data($filter, $current_year);

			$graph_filter = $filter;
			$graph_filter['period_year'] = $current_year;

	    endforeach;

		render('home', $data);
	}

	public function get_year_data($filter, $current_year)
	{

		$previous_year = $current_year - 1;

		$filter['period_year'] = $current_year;
		
		$results = $this->data_mdl->get_country_kpis($filter);

		foreach ($results as $row) {

			unset($filter['kpi_ids']);

			$filter['kpi_id']      = $row->kpi_id;
			$filter['period_year'] = $previous_year;

			$prev_year          = $this->data_mdl->get_country_kpis($filter, true);
			$row->previous_year = (isset($prev_year->kpi_value)) ? $prev_year->kpi_value : 0;
		}

		return $results;
	}

	public function kpi_comparison()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";
		$data['kpis']      =  $this->data_mdl->get_kpis();
		$data['years']     =  $this->data_mdl->get_periods_years();

		render('kpi_comparison', $data);
	}


	public function country_comparison()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";
		$data['kpis']    =  $this->data_mdl->get_kpis();
		$data['countries'] = $this->data_mdl->get_countries();

		render('country_comparison', $data);
	}


	public function country_comparison_data()
	{
		$data = $this->data_mdl->countries_data($this->input->get());
		die(json_encode($data));
	}

	public function kpi_comparison_data($subject_area_id)
	{
		$filter = $this->input->get();
		$filter['subject_area'] = $subject_area_id;

		if(intval($filter['country_id'])>0):
			$data = $this->data_mdl->countries_data($filter);
	    else:
	    	$data = $this->data_mdl->kpi_data($filter);
	    endif;

		die(json_encode($data));
	}

	public function dashboardData()
	{

		$data = $this->rcss_mdl->getData();
		echo json_encode($data);
	}
}
