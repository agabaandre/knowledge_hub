<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssetTypes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "assettypes";
		$this->title  = "Asset Types";
		$this->load->model('assettypes_model','assettypesmodel');
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Asset Types List";
		$data['assettypes'] = $this->assettypesmodel->get();

		render('list', $data);
	}
	public function get()
	{

		$data['assettypes'] = $this->assettypesmodel->get();

		return  $data;
	}

	public function save()
	{

		$is_error = false;

		$theme = [
			'id' => @$this->input->post("id"), 
			'type_name' => $this->input->post("name")
		];

		$resp = $this->assettypesmodel->save($theme);

		$msg = "Operation Successful";
		// }
		set_flash($msg, $is_error);
		redirect(base_url("assettypes"));
	}

	public function delete($id)
	{

		$resp = $this->assettypesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
