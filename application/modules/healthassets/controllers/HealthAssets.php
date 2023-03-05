<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HealthAssets extends MX_Controller
{

	public  function __construct()
	{
		parent::__construct();

		$this->module = "healthassets";
		$this->title  = "Health Assets";
		$this->load->model('healthassets_model', 'assetsmodel');
	}

	public function index()
	{
		$count   = $this->assetsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;
		$slug = $this->input->get('slug');

		$data['assets']     = $this->assetsmodel->assets_by_type($slug,[], $perPage, $page);
		$data['links']      = paginate('assets/index/', $count, $perPage, $segment);
		$data['rows_count'] = $count;

		$data['module'] = $this->module;
		render_site("assets", $data, true);
	}

	public function create()
	{
		$data['module'] = $this->module;
		$data['title']  = "Create Discussion";
		render_site("form", $data, true);
	}

	public function detail($asset_id)
	{
		$data['asset']    = $this->assetsmodel->find($asset_id);
		$data['assets']   = $this->assetsmodel->get(['not_id' => $asset_id], 10, 0);
		$data['module']   = $this->module;
		render_site("asset_details", $data, true);
	}

	public function save()
	{
		$is_error = false;

		$quote = [
			'id' => @$this->input->post("id"),
			'asset_title' => $this->input->post("title"),
			'asset_description' => $this->input->post("description"),
			'created_by' => $this->session->userdata('user')->id,
		];

		$resp = $this->assetsmodel->save($quote);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("assets"));
	}


	public function delete($id)
	{
		$this->assetsmodel->delete($id);
		echo "success";
	}

}
