<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class Initiatives_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table                = "initiatives";
		$this->users_table           = "user";
	}

	public function count($filters = [])
	{
		$this->applyFilters($filters);
		return $this->db->count_all($this->table);
	}

	public function get($filters = [], $limit = null, $start = null)
	{
		if ($limit)
			$this->db->limit($limit, $start);

		$this->applyFilters($filters);

		$rows = $this->db->get($this->table)->result();

		foreach ($rows as $row) :
			$row->user     = $this->find_user($row->created_by);
		endforeach;

		return $rows;
	}

	public function applyFilters($filters = [])
	{

		foreach ($filters as $key => $value) {

			if ($key == "not_id") :
				$this->db->where("id !=$value");
			else :
				$this->db->where($key, $value);
			endif;
		}
	}

	public function find($id)
	{
		$row = $this->db->where('id', $id)->get($this->table)->row();

		if ($row) :
			$row->user     = $this->find_user($row->created_by);
		endif;

		return $row;
	}

	public function find_user($id)
	{
		return $this->db->where('user_id', $id)->get($this->users_table)->row();
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
