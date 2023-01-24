<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class Subthemes_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table = "sub_thematic_area";
	}

	public function get($filter = [])
	{

		if (!empty($filter)) {

			foreach ($filter as $key => $value) {

				if ($key == "search_key") :
					$this->db->like('description', $value);
				else :
					$this->db->like($key, $value);
				endif;
			}
		}

		$subthemes = $this->db->get($this->table)->result();

		foreach ($subthemes as $sub) {
			$sub = $this->attach_related($sub);
		}
		
		return $subthemes;
	}

	public function find($id)
	{
		$subtheme = $this->db->where('id', $id)->get($this->table)->row();

		if ($subtheme)
			$subtheme = $this->attach_related($subtheme);
		return $subtheme;
	}

	public function find_by_thematic_area($id)
	{
		$subthemes = $this->db->where('thematic_area_id', $id)->get($this->table)->result();

		foreach ($subthemes as $sub) {
			$sub = $this->attach_related($sub);
		}
		return $subthemes;
	}

	private function attach_related($subtheme)
	{

		//attach pubs too
		$subtheme->theme        = $this->healththemesmodel->find($subtheme->thematic_area_id);
		return $subtheme;
	}

	//Save and returned inserted/updated row
	public function save($data, $update = false)
	{

		//if id is supplied, find record to update
		if ($update ||  (int) $data['id'] > 0) {
			$this->db->where('id', $data['id']);
			$this->db->update($this->table, $data);
		} else {
			$this->db->insert($this->table, $data);
		}

		$row_id = ($update) ? $data['id'] : $this->db->insert_id();

		//return inserted row
		return $this->find($row_id);
	}

	public function update($data)
	{

		$this->db->insert($this->table, $data);
		$row_id =  $this->db->insert_id();

		//return inserted row
		return $this->find($row_id);
	}

	public function delete($id)
	{

		//return inserted row
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}
}
