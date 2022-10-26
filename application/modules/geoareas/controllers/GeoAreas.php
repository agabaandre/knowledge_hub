<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GeoAreas extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "geoareas";
		$this->title  = "Geographical Areas";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Geographical Areas List";
		$data['geoareas'] = $this->geoareasmodel->get();

		render('list', $data);
	}
	public function get()
	{

		$data['geoareas'] = $this->geoareasmodel->get();

		return  $data;
	}
	public function getrcc()
	{

		$data = $this->geoareasmodel->getrcc();

		return  $data;
	}

	public function save()
	{

		$is_error = false;

		if ($this->form_validation->run('geoareas') == FALSE) {
			flash_form();
			$msg = validation_errors();
			$is_error = true;
		} else {

			$theme = [
				'id' => @$this->input->post("id"), 'name' => $this->input->post("name")
			];

			$resp = $this->geoareasmodel->save($theme);

			$msg = "Operation Successful";
		}
		set_flash($msg, $is_error);
		redirect(base_url("geoareas"));
	}

	public function delete($id)
	{

		$resp = $this->geoareasmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}
	public function getCountries()
	{

		if (!empty($_GET['region_data'])) {

			$region = urldecode($_GET["region_data"]);
			$this->db->where('region_id', $region);
			$countries = $this->db->get('country')->result();

			$opt = "<option value=''>Select Region</option>";

			if (!empty($countries)) {

				foreach ($countries as $country) {
					$opt .= "<option value='" . $country->id . "'>" . ucwords($country->name) . "</option>";
				}
			}

			echo $opt;
		}
	}
	public function countries()
	{
		return $this->geoareasmodel->get_countries();
	}
}
