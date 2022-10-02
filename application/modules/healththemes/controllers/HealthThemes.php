<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HealthThemes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "healththemes";
		$this->title  = "Health Security Themes";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Health Security Themes List";
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

		if ($this->form_validation->run('healththemes') == FALSE) {
			flash_form();
			$msg = validation_errors();
			$is_error = true;
		} else {

			$theme = [
				'id' => @$this->input->post("id"), 'description' => $this->input->post("description")
			];

			$resp = $this->healththemesmodel->save($bank);

			$msg = "Operation Successful";
		}
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
