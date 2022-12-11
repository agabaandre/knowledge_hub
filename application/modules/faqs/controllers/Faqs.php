<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Faqs extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "faqs";
		$this->title  = "Faqs";
	}

	public  function index()
	{
		$data['faqs'] = $this->faqsmodel->get();
		$data['module']   = $this->module;

		render_site("list", $data, true);
	}

	public function save()
	{

		$is_error = false;

		$quote = [
			'id'       => @$this->input->post("id"),
			'question' => $this->input->post("question"),
			'answer'   => $this->input->post("answer")
		];

		$resp = $this->faqsmodel->save($quote);

		$msg = "Operation Successful";
	
		set_flash($msg, $is_error);
		redirect(base_url("quotes"));
	}


}
