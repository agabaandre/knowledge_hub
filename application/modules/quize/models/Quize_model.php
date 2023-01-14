<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quize_model extends CI_Model
{
	// Handle Saving, Retrieving, Updating and Deleting of Quize questions and answers

	public function __construct()
	{
		parent::__construct();
	}

	public function get($id = null)
	{
		$this->db->select('q.*, (SELECT GROUP_CONCAT(CONCAT(a.id,":",a.answer_text,":",a.is_correct)) as answers FROM answers a WHERE a.question_id = q.id) as answers');
		$this->db->from('questions q');
		// $this->db->where('q.id', $id);
		$query = $this->db->get();
		$result = $query->result_array();

		// dd($result);
		return $result;
	}
}
