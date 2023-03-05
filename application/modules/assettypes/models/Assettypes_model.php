<?php
defined('BASEPATH') or exit('No direct script access allowed');
#[AllowDynamicProperties]
class Assettypes_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table = "asset_types";
	}

	public function get()
	{

		$geoareas = $this->db->get($this->table)->result();
		return $geoareas;
	}

	public function find($id)
	{
		$filetype = $this->db->where('id', $id)->get($this->table)->row();
		return $filetype;
	}

	//Save and returned inserted/updated row
	public function save($data, $update = false)
	{

		
		$data['slug'] = $this->get_slug($data['type_name']);
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

	

	public function delete($id)
	{

		//return inserted row
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

	public function get_slug($title){

		$slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($title)));
		$this->db->like('slug',$slug);
		$res=$this->db->get($this->table);

		return (count($res)>0)?($slug."-".(count($res)+1)):$slug;
	}
}
