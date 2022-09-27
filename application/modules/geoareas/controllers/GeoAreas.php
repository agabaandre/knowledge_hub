<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GeoAreas extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "geoareas";
		$this->title  = "Geographical Areas";
		
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Geographical Areas List";
		$data['geoareas'] = $this->geoareasmodel->get();
	
		render('list',$data);
	}

	public function save() {

		$is_error = false;

		if ($this->form_validation->run('geoareas') == FALSE)
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

			$resp = $this->geoareasmodel->save($bank);

			$msg = "Operation Successful";
		}
		set_flash($msg, $is_error);
		redirect( base_url("geoareas"));
	}

	public function delete($id) {
		
		$resp = $this->geoareasmodel->delete($id);
		$is_error = false;
		
		if($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}



}
