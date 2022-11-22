<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Geoareas_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table = "geographical_coverage";
	}

	public function get()
	{

		$geoareas = $this->db->get($this->table)->result();
		return $geoareas;
	}
	public function getrcc()
	{

		$geoareas = $this->db->get('regions')->result();
		return $geoareas;
	}

	public function find($id)
	{
		$geoarea = $this->db->where('id', $id)->get($this->table)->row();
		return $geoarea;
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
	public function get_countries()
	{
		$this->db->order_by('name', 'ASC');
		return $this->db->get('country')->result();
	}
}
