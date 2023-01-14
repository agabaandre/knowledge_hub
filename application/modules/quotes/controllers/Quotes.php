<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "quotes";
		$this->title  = "Search Quotes";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Search Quotes";
		$data['quotes'] = $this->quotesmodel->get();

		render('list', $data);
	}
	public function get()
	{

		$data['quotes'] = $this->quotesmodel->get();

		return  $data;
	}

	public function save()
	{

		$is_error = false;
		
		$data = [
			'id' => @$this->input->post("id"),
			'quote' => $this->input->post("quote"),
			'is_active' => $this->input->post("is_active") ? 1 : 0,
		];

		$resp = $this->quotesmodel->save($data);

		$msg = "Operation Successful";
		// }
		set_flash($msg, $is_error);
		redirect(base_url("quotes"));
	}

	public function delete($id)
	{

		$resp = $this->quotesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
