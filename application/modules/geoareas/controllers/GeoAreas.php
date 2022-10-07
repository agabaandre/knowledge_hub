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

			$distdata = array();
			$distdata = explode("_", $dist);

			$dist_id = $distdata[0];
			$district = $distdata[1];
			$userdata = $this->session->get_userdata();
			$permissions = $userdata['permissions'];
			//view all facilities
			if (in_array('38', $permissions)) {
				$sql = "SELECT DISTINCT facility_id,facility FROM ihrisdata WHERE district_id LIKE '$dist_id' ORDER BY facility ASC";
			} else {
				$facility = $_SESSION['facility'];
				$sql = "SELECT DISTINCT facility_id,facility FROM ihrisdata WHERE facility_id LIKE '$facility'";
			}

			$facilities = $this->db->query($sql)->result();

			$opt = "<option value=''>Select Facility</option>";

			if (!empty($facilities)) {

				foreach ($facilities as $facility) {
					$opt .= "<option value='" . $facility->facility_id . "__" . $facility->facility . "'>" . ucwords($facility->facility) . "</option>";
				}
			}

			echo $opt;
		}
	}
}
