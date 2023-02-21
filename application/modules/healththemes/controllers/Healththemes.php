<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Healththemes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "healththemes";
		$this->title  = "Security Themes";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['uptitle']   = "Security Themes";
		$data['healththemes'] = $this->healththemesmodel->get();

		render('list', $data);
	}

	public function get()
	{

		$data['healththemes'] = $this->healththemesmodel->get();
		$data;
	}

	public function save()
	{
		$is_error = false;
		$data = $this->input->post();
		$theme = (array) $data;
		$resp = $this->healththemesmodel->save($theme);
		$msg = "Operation Successful";
		set_flash($msg, $is_error);
		redirect(base_url("healththemes"));
	}

	public function delete($id)
	{

		$resp = $this->healththemesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
