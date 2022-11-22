<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SubThemes extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "subthemes";
		$this->title  = "Sub Security Themes";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Sub Security Themes List";
		$data['subthemes'] = $this->subthemesmodel->get();

		render('list', $data);
	}
	public function get()
	{
		$data['subthemes'] = $this->subthemesmodel->get();

		return $data;
	}

	public function save()
	{

		$is_error = false;

		if ($this->form_validation->run('subthemes') == FALSE) {
			flash_form();
			$msg = validation_errors();
			$is_error = true;
		} else {

			$theme = [
				'id' => @$this->input->post("id"), 'description' => $this->input->post("description")
			];

			$resp = $this->subthemesmodel->save($bank);

			$msg = "Operation Successful";
		}
		set_flash($msg, $is_error);
		redirect(base_url("subthemes"));
	}

	public function delete($id)
	{

		$resp = $this->subthemesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
