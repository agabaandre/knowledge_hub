<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publications_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();

		$this->table = "publication";
		$this->author_pubs_table = "author_publication";
		$this->filetypes_table = "file_type";
	}

	public function get($filter = [])
	{

		if(!empty($filter)){

			foreach($filter as $key => $value) {
				
				if($key == "search_key"):
					$this->db->like('description',$value);
					$this->db->or_like('publication',$value);
				else:
					$this->db->like($key,$value);
				endif;
			}
		}

		$publications = $this->db->get($this->table)->result();

		foreach ($publications as $pub) {
			$pub = $this->attach_related($pub);
		}

		return $publications;
	}

	public function get_by_subtheme($sub_theme_id)
	{

		$this->db->where('sub_thematic_area_id', $sub_theme_id);
		$publications = $this->db->get($this->table)->result();

		foreach ($publications as $pub) {
			$pub = $this->attach_related($pub);
		}

		return $publications;
	}

	public function find($id)
	{

		$publication = $this->db->where('id', $id)->get($this->table)->row();

		if ($publication)
			$publication = $this->attach_related($publication);
		return $publication;
	}

	private function attach_related($publication)
	{

		$publication->author    = $this->get_author($publication);
		$publication->geoareas = $this->geoareasmodel->find($publication->geographical_coverage_id);

		$publication->sub_theme = $this->subthemesmodel->find($publication->sub_thematic_area_id);
		$publication->theme = $publication->sub_theme->theme;
		$publication->file_type = $this->get_filetype($publication->file_type_id);
		return $publication;
	}

	public function get_author($publication)
	{

		$this->db->where('publication_id', $publication->id);
		$this->db->join('author', 'author.id=author_publication.author_id');
		return $this->db->get($this->author_pubs_table)->row();
	}

	public function get_filetype($type_id)
	{

		$this->db->where('id', $type_id);
		return $this->db->get($this->filetypes_table)->row();
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
