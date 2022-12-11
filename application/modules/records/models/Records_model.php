<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Records_model extends CI_Model
{


	public function __construct()
	{

		parent::__construct();
	}


	public function search($term, $type, $limit = null, $start = 0)
	{

		$this->applyFilter($term, $type);

		if ($limit) {
			$this->db->limit($limit, $start);
		}

		$results = $this->db->get('publication')->result();

		foreach ($results as $key => $pub) {
			$results[$key] = $this->publicationsmodel->attach_related($pub);
		}

		return $results;
	}

	public function count($term, $type)
	{
		$this->applyFilter($term, $type);
		return count($this->db->get('publication')->result());
	}

	public function applyFilter($term, $type)
	{

		if ($type)
			$this->db->where('file_type_id', $type);

		if ($term) :

			$search = ['search_key' => $term];

			$authors    = $this->get_authors($search);
			$sub_themes = $this->get_subthemes($search);

			$this->db->like('title', $term);
			$this->db->or_like('description', $term);
			//if tag, include pubs with the tag
			$this->db->or_where_in('id', $this->publication_tags($term));

			if (count($authors) > 0)
				$this->db->or_where_in('author_id ', array_values($authors));

			if (count($sub_themes) > 0)
				$this->db->or_where_in('sub_thematic_area_id', array_values($sub_themes));
		endif;
	}


	public function get_authors($search)
	{

		$authors      = $this->authorsmodel->get($search);
		$author_ids   = [];

		foreach ($authors as $row) {
			array_push($author_ids, $row->id);
		}

		return $author_ids;
	}

	public function get_subthemes($search)
	{

		$themes       = $this->healththemesmodel->get($search);
		$subthemes_ids = [];

		foreach ($themes as $row) :

			$filter = ['thematic_area_id' => $row->id];
			$subthemes       = $this->subthemesmodel->get($filter);

			//subthemes
			foreach ($subthemes as $sub) {
				array_push($subthemes_ids, $sub->id);
			}

		endforeach;

		return $subthemes_ids;
	}

	public function get_questions()
	{

		$result = [];

		$questions = $this->db->get("questions")->result();

		foreach ($questions as $question) {

			$answers    = $this->get_answers($question->id);

			$answer_str = array_column($answers, 'answer_text');

			$result[] = array(
				"question" => $question->question_text,
				"choices" => $answer_str,
				"correctAnswer" => intval($this->get_correct_answer($answers))
			);
		}

		return $result;
	}

	public function get_correct_answer($answers)
	{

		foreach ($answers as $key => $value) {

			if ($value['is_correct'] == 1)
				return $key;
		}
	}

	public function get_answers($id)
	{

		$this->db->where('question_id', $id);
		return $this->db->get("answers")->result_array();
	}

	public function publication_tags($tag_text)
	{
		$qry = $this->db->query("SELECT publication_id from publication_tags where tag_id in (SELECT id from tags WHERE  tag_text like '$tag_text%')");
		$result = $qry->result();
		$arr = [];

		foreach ($result as $row) {
			$arr[] = $row->publication_id;
		}

		return $arr;
	}
}
