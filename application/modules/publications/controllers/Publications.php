<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publications extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "publications";
		$this->title  = "Publications";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = "Publications List";
		$data['publications'] = $this->publicationsmodel->get();

		render('list', $data);
	}

	public function create($id = FALSE)
	{
		$data['module'] = $this->module;
		if ($id) {
			$data['title']  = 'Create Publication';
		} else {
			$data['title']  = 'Create Publication';
		}
		$data['authors'] = $this->authorsmodel->get();
		$data['geoareas'] = $this->geoareasmodel->get();
		$data['subthemes'] = $this->subthemesmodel->get();
		$data['filetypes'] = $this->filetypesmodel->get();
		$data['publications'] = $this->publicationsmodel->find($id);

		render('form', $data);
	}

	public function save()
	{

		$is_error = false;

		$theme = [
			'id' => @$this->input->post("id"), 'sub_thematic_area_id' => @$this->input->post("sub_thematic_area_id"), 'publication' => $this->input->post("publication"), 'author_id' => $this->input->post("author"),
			'geographical_coverage_id' => $this->input->post("geographical_coverage"), 'title' => $this->input->post("title"), 'description'  => $this->input->post("description"), 'file_type_id' => $this->input->post("file_type"), 'is_active' => $this->input->post("is_active")
		];

		$resp = $this->publicationsmodel->save($theme);

		// set_flash($msg, $is_error);
		echo $resp;
	}

	public function delete($id)
	{   
		$resp = $this->publicationsmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
