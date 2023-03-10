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

	public function admin()
	{
		$count   = $this->expertsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;
		$slug = $this->input->get('slug');

		$filter = $this->input->post();

		$data['experts']     = $this->expertsmodel->get($filter, $perPage, $page);
		$data['countries']  = $this->geoareasmodel->get_countries();
		$data['links']      = paginate('experts/admin/', $count, $perPage, $segment);
		$data['rows_count'] = $count;
		$data['title'] = "Health Experts List";

		$data['module'] = $this->module;
		render("expert_admin", $data);
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

		$sess = (object) user_session()->user;

		$record = [
			'id' => @$this->input->post("id"),
			'first_name' => $this->input->post("first_name"),
			'last_name' => $this->input->post("last_name"),
			'job_title' => $this->input->post("job_title"),
			'phone_number' => $this->input->post("phone_number"),
			'email' => $this->input->post("email"),
			'occupation' => $this->input->post("occupation"),
			'expert_type' => $this->input->post("expert_type"),
			'country_id' => $this->input->post("country_id"),
			'created_by' => $sess->id,
		];

		$resp = $this->expertsmodel->save($record);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("experts/admin"));
	}


	public function delete($id)
	{
		$this->expertsmodel->delete($id);
		echo "success";
	}

}
