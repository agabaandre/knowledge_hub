<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rccs extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "rccs";
		$this->load->model("Rccs_mdl", 'rccs_mdl');
		$this->load->model("Data_mdl", 'data_mdl');
	}

	public function index()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";

		$data['kpi_summary']      = $this->data_mdl->get([],'kpi_id','*,avg(kpi_value) as kpi_value');
		$data['years_summary']    = $this->data_mdl->get([],'period_year','*,avg(kpi_value) as kpi_value');
		$data['subjects_summary'] = $this->data_mdl->get([],'subject_area_id','*,avg(kpi_value) as kpi_value');
        $data['countries_summary'] = $this->data_mdl->get([],'country_id,period_year','*,avg(kpi_value) as kpi_value');

		render_dashboard('home', $data);
	}

	public function kpi_comparison()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";
		$data['kpis']      =  $this->data_mdl->get_kpis();
		$data['years']     =  $this->data_mdl->get_periods_years();
		
		render_dashboard('kpi_comparison', $data);
	}


	public function country_comparison()
	{
		$data['module']  = $this->module;
		$data['title']   = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";
		$data['kpis']    =  $this->data_mdl->get_kpis();
		$data['countries'] = $this->data_mdl->get_countries();

		render_dashboard('country_comparison', $data);
	}


	public function country_comparison_data()
	{
       $data = $this->data_mdl->countries_data($this->input->get());
        die( json_encode($data));
	}

	public function kpi_comparison_data()
	{
	
       $data = $this->data_mdl->kpi_data($this->input->get());
        die( json_encode($data));
	}
	
	public function dashboardData()
	{

		$data = $this->rcss_mdl->getData();
		echo json_encode($data);
	}
}
