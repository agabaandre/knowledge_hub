<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kpi extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "kpi";
		$this->load->model("Kpi_mdl", 'kpi_mdl');
	}

	public function create($id = FALSE)
	{
		$data['module']  = $this->module;
		$data['title']   = "Manage Indicators";
		$data['uptitle'] = "Manage Indicators";

		$insert = $this->input->post();
		$data['elements'] = $this->kpi_mdl->create_kpi($insert);
		$this->session->set_flashdata('message', 'Added');
		$data['title']   = 'Create KPI';
		$data['module']  = $this->module;

		render('kpi', $data);
	}
	public function data($id = FALSE)
	{
		$data['module']  = $this->module;
		$data['title']   = "Add Data";
		$data['uptitle'] = "Add Data";
		$insert = $this->input->post();
		$data['elements'] = $this->kpi_mdl->kpi_data($insert);
		$this->session->set_flashdata('message', 'Added');
		$data['title']   = 'Add KPI Data';
		$data['module']  = $this->module;

		render('kpi_data', $data);
	}
	public function get_kpi($id = FALSE)
	{
		$data = $this->kpi_mdl->create_kpi($id);
		return $data;
	}
	public function get_subjects()
	{
		return $this->kpi_mdl->get_subjects();
	}
	public function get_countries()
	{
		return $this->geoareasmodel->get_countries();
	}
}
