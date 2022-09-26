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

	public function front_end_site($data)
	{
		//check_logged_in();
		$this->load->view('site', $data);
	}

	public function front_end_client($data)
	{
		check_logged_in();

		//alerts user to complete profile if not on profiles page
		if (user_session()->kyc->is_complete == "0" && !str_contains($data['view'], 'profile')) {
			$profile_link = base_url('client/profile');
			$msg = "<h5>Please complete your profile to enjoy full functionality. <a href='$profile_link'>Click Here to Start</a></h5>";
			set_flash($msg, true);
		}

		$this->load->view('client', $data);
	}
}
