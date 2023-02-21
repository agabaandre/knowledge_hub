<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Experts extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "experts";
		$this->title  = "Experts";
		$this->load->model('experts_model', 'expertsmodel');
	}

	public function index()
	{

		$count   = $this->expertsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;

		$data['experts']    = $this->expertsmodel->get([], $perPage, $page);
		$data['links']     = paginate('experts/index/', $count, $perPage, $segment);
		$data['rows_count'] = $count;

		$data['module'] = $this->module;
		render_site("experts", $data, true);
	}

	public function create()
	{
		$data['module'] = $this->module;
		$data['title']  = "Create Discussion";
		render_site("form", $data, true);
	}

	public function detail($expert_id)
	{

		$data['expert']    = $this->expertsmodel->find($expert_id);
		$data['experts']   = $this->expertsmodel->get(['not_id' => $expert_id], 10, 0);
		$data['module']   = $this->module;

		render_site("expert_details", $data, true);
	}


	public function save()
	{

		$is_error = false;

		$quote = [
			'id' => @$this->input->post("id"),
			'expert_title' => $this->input->post("title"),
			'expert_description' => $this->input->post("description"),
			'created_by' => $this->session->userdata('user')->id,
		];

		$resp = $this->expertsmodel->save($quote);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("experts"));
	}


	public function delete($id)
	{
		$this->expertsmodel->delete($id);
		echo "success";
	}

}
