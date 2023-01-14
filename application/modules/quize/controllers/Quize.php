<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quize extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "quize";
		$this->title  = "Quize";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Quize";
		$data['questions'] = $this->quizemodel->get();
		// dd($data['questions']);
		render('list', $data);
	}
	public function get()
	{

		$data['quize'] = $this->quizemodel->get();

		return  $data;
	}

	public function save()
	{

		$is_error = false;
		
		$data = [
			'id' => @$this->input->post("id"),
			'question_text' => $this->input->post("question_text"),
			'is_active' => $this->input->post("is_active") ? 1 : 0,
		];

		$resp = $this->quizemodel->save($data);

		$msg = "Operation Successful";
		// }
		set_flash($msg, $is_error);
		redirect(base_url("quize"));
	}

	public function delete($id)
	{

		$resp = $this->quizemodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
