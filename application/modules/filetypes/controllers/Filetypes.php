<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Filetypes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "filetypes";
		$this->title  = "File Types";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "File Types List";
		$data['filetypes'] = $this->filetypesmodel->get();

		render('list', $data);
	}
	public function get()
	{

		$data['filetypes'] = $this->filetypesmodel->get();

		return  $data;
	}

	public function save()
	{

		$is_error = false;

		$theme = [
			'id' => @$this->input->post("id"), 'name' => $this->input->post("name")
		];

		$resp = $this->filetypesmodel->save($theme);

		$msg = "Operation Successful";
		// }
		set_flash($msg, $is_error);
		redirect(base_url("filetypes"));
	}

	public function delete($id)
	{

		$resp = $this->filetypesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
