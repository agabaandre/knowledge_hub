<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Templates extends MX_Controller
{


	public function main($data)
	{

		//  check_admin_access();

		$this->load->view('main', $data);
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
		$this->load->view('front_end', $data);
	}

}
