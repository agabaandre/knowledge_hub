<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Templates extends MX_Controller
{


	public function main($data)
	{
		if($this->session->has_userdata('user')) {
			$this->load->view('main', $data);
		} else {
			redirect('auth');
		}
	}

	public function plain($data)
	{
		if (@user_session()->is_admin)
			redirect(base_url('dashboard'));

		$this->load->view('plain', $data);
	}

	public function frontend($data)
	{
		//check_logged_in();
		$this->load->view('site', $data);
	}

	public function dashboards($data)
	{
		//check_logged_in();
		$this->load->view('dashboards', $data);
	}
}
