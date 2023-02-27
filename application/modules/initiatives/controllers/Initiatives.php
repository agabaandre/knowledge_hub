<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Initiatives extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "initiatives";
		$this->title  = "Initiatives";
		$this->load->model('initiatives_model', 'initiativesmodel');
	}

	public function index()
	{

		$count   = $this->initiativesmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;

		$data['initiatives']    = $this->initiativesmodel->get([], $perPage, $page);
		$data['links']     = paginate('initiatives/index/', $count, $perPage, $segment);
		$data['rows_count'] = $count;

		$data['module'] = $this->module;
		render_site("initiatives", $data, true);
	}

	public function create()
	{
		$data['module'] = $this->module;
		$data['title']  = "Create Initiative";
		render_site("form", $data, true);
	}

	public function detail($initiative_id)
	{

		$data['initiative']    = $this->initiativesmodel->find($initiative_id);
		$data['initiatives']   = $this->initiativesmodel->get(['not_id' => $initiative_id], 10, 0);
		$data['module']   = $this->module;

		render_site("initiative_details", $data, true);
	}


	public function save()
	{

		$is_error = false;

		$quote = [
			'id' => @$this->input->post("id"),
			'initiative_title' => $this->input->post("title"),
			'initiative_description' => $this->input->post("description"),
			'created_by' => $this->session->userdata('user')->id,
		];

		$resp = $this->initiativesmodel->save($quote);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("initiatives"));
	}


	public function delete($id)
	{
		$this->initiativesmodel->delete($id);
		echo "success";
	}

}
