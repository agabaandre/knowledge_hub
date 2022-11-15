<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "quotes";
		$this->title  = "Quotes";
	}

	

	public function save()
	{

		$is_error = false;

		$quote = [
			'id' => @$this->input->post("id"), 'name' => $this->input->post("quote")
		];

		$resp = $this->quotesmodel->save($quote);

		$msg = "Operation Successful";
	
		set_flash($msg, $is_error);
		redirect(base_url("quotes"));
	}


}
