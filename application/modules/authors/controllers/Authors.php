<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authors extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "authors";
		$this->title  = "Authors";
		
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Authors List";
		$data['authors'] = $this->authorsmodel->get();
	
		render('list',$data);
	}

	public function save() {

		$is_error = false;

		if ($this->form_validation->run('author') == FALSE)
		{
			flash_form();
			$msg = validation_errors();
			$is_error = true;
		}
		else
		{
			
			$theme = [
				'id' => @$this->input->post("id")
				,'name' => $this->input->post("name")
			];

			$resp = $this->authorsmodel->save($theme);

			$msg = "Operation Successful";
		}
		set_flash($msg, $is_error);
		redirect( base_url("authors"));
	}

	public function delete($id) {
		
		$resp = $this->authorsmodel->delete($id);
		$is_error = false;
		
		if($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}



}
