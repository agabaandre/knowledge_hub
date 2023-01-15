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

	public function saveQuestion($data)
	{
		$this->db->insert('questions', $data);
		return $this->db->insert_id();
	}

	public function updateQuestion($data)
	{
		$this->db->where('id', $data['id']);
		$this->db->update('questions', $data);
	}

	public function saveAnswer($data)
	{
		$this->db->insert('answers', $data);
	}

	public function deleteAnswers($question_id)
	{
		$this->db->where('question_id', $question_id);
		$this->db->delete('answers');
	}

	public function getQuestion($id)
	{
		$this->db->select('q.*, (SELECT GROUP_CONCAT(CONCAT(a.id,":",a.answer_text,":",a.is_correct)) as answers FROM answers a WHERE a.question_id = q.id) as answers');
		$this->db->from('questions q');
		$this->db->where('q.id', $id);
		$query = $this->db->get();
		$result = $query->row_array();

		// dd($result);
		return $result;
	}

	public function deleteQuestion($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('questions');
		return $this->db->affected_rows();
	}

	
}
