<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authors extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "authors";
		$this->title  = "Authors";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Authors List";
		$data['authors'] = $this->authorsmodel->get();

		render('list', $data);
	}

	public function get()
	{

		$data['authors'] = $this->authorsmodel->get();
		return $data;
	}


	public function save()
	{

		// Get author data from form
		$author = [
			'id' => @$this->input->post("id"),
			'name' => $this->input->post("name"),
			'address' => $this->input->post("address"),
			'telephone' => $this->input->post("telephone"),
		];

		// dd($author);

		// Validate the input data
		$this->form_validation->set_rules('name', 'Author Name', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			// Validation failed
			// Return to the form with error messages
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url("authors"));
		} else {
			// Validation passed
			// Save the data to the database
			$resp = $this->authorsmodel->save($author);
			if ($resp) {
				// Data saved successfully
				// Return to the form with success message
				$this->session->set_flashdata('success', 'Author saved successfully');
				redirect(base_url("authors"));
			} else {
				// Data not saved
				// Return to the form with error message
				$this->session->set_flashdata('error', 'Author not saved');
				redirect(base_url("authors"));
			}
		}
	}

	public function delete($id)
	{

		$resp = $this->authorsmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
