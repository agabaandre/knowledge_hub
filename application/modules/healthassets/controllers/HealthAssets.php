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

	public function admin()
	{
		$count   = $this->assetsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;
		$slug = $this->input->get('slug');

		$filter = $this->input->post();

		$data['assets']     = $this->assetsmodel->get($filter, $perPage, $page);
		$data['types'] 		= $this->assetsmodel->get_types();
		$data['countries']  = $this->geoareasmodel->get_countries();
		$data['links']      = paginate('healthassets/admin/', $count, $perPage, $segment);
		$data['rows_count'] = $count;
		$data['title'] = "Health Assets";

		$data['module'] = $this->module;
		render("assets_admin", $data);
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

		$sess = (object) user_session()->user;

		$quote = [
			'id' => @$this->input->post("id"),
			'asset_name'    => $this->input->post("asset_name"),
			'asset_desc'    => $this->input->post("asset_desc"),
			'asset_type_id' => $this->input->post("asset_type_id"),
			'url'           => $this->input->post("asset_url"),
			'country_id'    => $this->input->post("country_id"),
			'created_by'    => $sess->user_id,
		];

		$resp = $this->assetsmodel->save($quote);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("healthassets/admin"));
	}


	public function delete($id)
	{
		$this->assetsmodel->delete($id);
		echo "success";
	}

}
