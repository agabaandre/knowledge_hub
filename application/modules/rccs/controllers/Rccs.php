<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->dashmodule = "dashboard";
		$this->load->model("dashboard_mdl", 'dash_mdl');
	}

	public function index()
	{
		$data['module'] = $this->dashmodule;
		$data['title'] = "Main Dashboard";
		$data['uptitle'] = "Main Dashboard";

		render_dashboards('home', $data);
	}
	public function dashboardData()
	{

		$data = $this->dash_mdl->getData();
		echo json_encode($data);
	}
}
