<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Constants extends MX_Controller
{


	public function __Construct()
	{

		parent::__Construct();

		$this->load->model('constants_mdl');
		$this->user = $this->session->get_userdata('user');
	}


	public function index()
	{
		$data['title'] = "Constants";
		$data['uptitle'] = "Constants";
		$data['module'] = 'constants';
		$data['view'] = "variables";
		$data['setting'] = $this->getSettings();
		$postdata = $this->input->post();
		if ($this->input->post('site_name')) {
			$data['message'] = $this->constants_mdl->update_variables($postdata);
			redirect("constants/");
		} else {
			echo Modules::run('templates/main', $data);
		}
	}
	public function getSettings()
	{
		return $this->constants_mdl->getSettings();
	}
	public function readLogs()
	{
		$myfile = fopen("log.txt", "r") or die("Unable to open file!");

		return fread($myfile, filesize("log.txt"));
	}
	public function logs()
	{
		$data['title'] = "Logs";
		$data['uptitle'] = "Logs";
		$data['module'] = 'constants';
		$data['view'] = "logs";
		echo Modules::run('templates/main', $data);
	}
}
