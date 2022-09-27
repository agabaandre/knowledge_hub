<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Healththemes_model extends CI_Model {

	
	public function __Construct(){

		parent::__Construct();

		$this->table="thematic_area";

	}

	public function get(){

		$healththemes = $this->db->get($this->table)->result();
		return $healththemes;
	}

	public function find($id){
		$healththeme = $this->db->where('id',$id)->get($this->table)->row();
		 return $healththeme;
	}

   //Save and returned inserted/updated row
   public function save($data,$update=false){
		
		//if id is supplied, find record to update
		if($update ||  (int) $data['id'] > 0){
			$this->db->where('id',$data['id']);
			$this->db->update($this->table,$data);
		}else{
			$this->db->insert($this->table,$data);
		}

		$row_id = ($update)?$data['id']: $this->db->insert_id();

		//return inserted row
		return $this->find($row_id);
	}

	public function update($data){
		
		$this->db->insert($this->table,$data);
		$row_id =  $this->db->insert_id();
		
		//return inserted row
		return $this->find($row_id);
	}

	public function delete($id){
		
		//return inserted row
		$this->db->where('id',$id);
			$this->db->delete($this->table);
	}

}
