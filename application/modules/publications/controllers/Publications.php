<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Publications extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "publications";
		$this->title  = "Publications";
		
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Publications List";
		$data['publications'] = $this->publicationsmodel->get();
	
		render('list',$data);
	}

	public function save() {

		$is_error = false;

		if ($this->form_validation->run('publications') == FALSE)
		{
			flash_form();
			$msg = validation_errors();
			$is_error = true;
		}
		else
		{
			
			$theme = [
				'id' => @$this->input->post("id")
				,'sub_thematic_area_id' => @$this->input->post("sub_thematic_area_id")
				,'publication' => $this->input->post("publication")
				,'description'  => $this->input->post("description")
				,'file_type_id' => $this->input->post("file_type")
				,'is_active' => $this->input->post("is_active")
			];

			$resp = $this->publicationsmodel->save($bank);

			$msg = "Operation Successful";
		}
		set_flash($msg, $is_error);
		redirect( base_url("publications"));
	}

	public function delete($id) {
		
		$resp = $this->publicationsmodel->delete($id);
		$is_error = false;
		
		if($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}



}