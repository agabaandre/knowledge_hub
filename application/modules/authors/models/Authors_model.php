<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authors_model extends CI_Model
{


	public function __Construct()
	{

		parent::__Construct();
		$this->table = "author";
		$this->publications_table = "publication";
	}

	public function count()
	{

		return $this->db->count_all($this->table);
	}

	public function get($filter = [], $limit = null, $start = null)
	{

		if (!empty($filter)) {
			foreach ($filter as $key => $value) {

				if ($key == "search_key") :
					$this->db->like('name', $value);
				else :
					$this->db->like($key, $value)->order_by('id', 'DESC');
				endif;
			}
		}

		if ($limit)
			$this->db->limit($limit, $start);

		$authors = $this->db->get($this->table)->result();
		foreach ($authors as $author) :
			$author->publications = $this->get_author_pubs($author->id);
		endforeach;

		return $authors;
	}

	public function find($id)
	{
		$author = $this->db->where('id', $id)->get($this->table)->row();

		if ($author)
			$author->publications = $this->get_author_pubs($author->id);

		return $author;
	}

	public function get_author_pubs($id)
	{
		$author_pubs = $this->db->where('author_id', $id)->get($this->publications_table)->result();
		return $author_pubs;
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
