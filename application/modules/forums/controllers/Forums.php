<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Forums extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "forums";
		$this->title  = "Forums";
		$this->load->model('forums_model', 'forumsmodel');
	}

	public function index()
	{

		$count   = $this->forumsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;

		$data['forums']    = $this->forumsmodel->get([], $perPage, $page);
		$data['links']     = paginate('forums/index/', $count, $perPage, $segment);
		$data['rows_count'] = $count;

		$data['module'] = $this->module;
		render_site("forums", $data, true);
	}

	public function detail($forum_id)
	{

		$data['forum']    = $this->forumsmodel->find($forum_id);
		$data['forums']   = $this->forumsmodel->get(['not_id' => $forum_id], 10, 0);
		$data['module']   = $this->module;

		render_site("forum_details", $data, true);
	}


	public function save()
	{

		$is_error = false;

		$quote = [
			'id' => @$this->input->post("id"),
			'forum_title' => $this->input->post("title"),
			'forum_descriptionn' => $this->input->post("description"),
		];

		$resp = $this->forumsmodel->save($quote);

		$msg = "Operation Successful";

		set_flash($msg, $is_error);
		redirect(base_url("forums"));
	}

	public function admin()
	{

		$count   = $this->forumsmodel->count([]);
		$segment = 3;
		$page    = ($this->uri->segment($segment)) ? $this->uri->segment($segment) : 0;
		$perPage = 12;
		$data['title'] = "Forums";
		$data['uptitle'] = "Forums";
		$data['forums']    = $this->forumsmodel->get([], $perPage, $page);
		$data['links']     = paginate('forums/admin/', $count, $perPage, $segment);
		$data['rows_count'] = $count;

		$data['module'] = $this->module;
		render("forum_admin", $data);
	}
}
