<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class HealthAssets_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table                = "health_assets";
		$this->users_table           = "user";
		$this->types_table           = "asset_types";
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
			$row->type     = $this->find_type($row->asset_type_id);
		endforeach;

		return $rows;
	}

	public function applyFilters($filters = [])
	{

		foreach ($filters as $key => $value) {

			if ($key == "not_id"):
				$this->db->where("id !=$value");
			else :
				$this->db->where($key, $value);
			endif;
		}
	}

	public function assets_by_type($slug="",$filters=[],$limit = null, $start = null)
	{
		$type = $this->db->where('slug', $slug)->get($this->types_table)->row();

		if($type)
		 $filters['asset_type_id'] = $type->id;

		return $this->get($filters, $limit, $start);
	}


	public function find($id)
	{
		$row = $this->db->where('id', $id)->get($this->table)->row();

		if ($row) :
			$row->user     = $this->find_user($row->created_by);
			$row->type     = $this->find_type($row->asset_type_id);

			$country  = $this->geoareasmodel->find_country($row->country_id);
			$row->country = $country;
			$row->region = $country->region;
		endif;

		return $row;
	}


	public function get_types()
	{
		return $this->db->get($this->types_table)->result();
	}


	public function find_type($id)
	{
		return $this->db->where('id', $id)->get($this->types_table)->row();
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

	
	//Save and returned inserted/updated row
	public function save_type($data, $update = false)
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

	

	
}
