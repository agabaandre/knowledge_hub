<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rccs extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "rccs";
		$this->load->model("Rccs_mdl", 'rccs_mdl');
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title'] = "RRC Dashboards";
		$data['uptitle'] = "RCC Dashboards";

		render_dashboard('home', $data);
	}
	public function dashboardData()
	{

		$data = $this->rcss_mdl->getData();
		echo json_encode($data);
	}
}
