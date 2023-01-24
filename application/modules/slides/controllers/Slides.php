<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Slides extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "slides";
		$this->title  = "Slides";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Slides List";
		$data['slides'] = $this->slidesmodel->get();

		render('list', $data);
	}

	public function get()
	{

		$data['slides'] = $this->slidesmodel->get();
		return $data;
	}


	public function save()
	{


	$slide = [
		'id' => @$this->input->post("id"), 'caption' => $this->input->post("caption")
	];

    //save attachment
    $config['upload_path']   = './assets/images/slider/';
    $config['allowed_types'] = 'gif|jpg|png|pdf|jpeg';
    $config['max_size']      = 3070;
    $config['file_name']    = "slide" . user_session()->user->user_id.time();
    $this->load->library('upload',$config);

    if ($this->upload->do_upload('image')) {

      $data = $this->upload->data();
      $slide['slide_image'] = $data['file_name'];

     }

	$resp = $this->slidesmodel->save($slide);

	$msg = "Operation Successful";
	
	set_flash($msg, $is_error);
	redirect(base_url("slides"));

	}

	public function delete($id)
	{

		$resp = $this->slidesmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
}
