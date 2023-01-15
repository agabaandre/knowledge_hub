<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quize extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "quize";
		$this->title  = "Quize";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['page']   = "Quize";
		$data['questions'] = $this->quizemodel->get();
		// dd($data['questions']);
		render('list', $data);
	}
	public function get()
	{

		$data['quize'] = $this->quizemodel->get();

		return  $data;
	}

	public function save()
	{
		$data = $this->input->post();
		if (isset($data['id']) && !empty($data['id'])) {
			// Update existing question
			$question_data = array(
				'id' => $data['id'],
				'question_text' => $data['question'],
				'enabled' => 1
			);
			$this->quizemodel->updateQuestion($question_data);
			$this->quizemodel->deleteAnswers($data['id']);
			// loop through the answers and insert them
			for ($i = 1; $i <= 3; $i++) {
				$answer_data = array(
					'question_id' => $data['id'],
					'answer_text' => $data['answer' . $i],
					'is_correct' => ($data['correct_answer'] == $i) ? 1 : 0
				);
				$this->quizemodel->saveAnswer($answer_data);
			}
		} else {
			// Save new question
			$question_data = array(
				'question_text' => $data['question'],
				'enabled' => 1
			);
			$question_id = $this->quizemodel->saveQuestion($question_data);
			// loop through the answers and insert them
			for ($i = 1; $i <= 3; $i++) {
				$answer_data = array(
					'question_id' => $question_id,
					'answer_text' => $data['answer' . $i],
					'is_correct' => ($data['correct_answer'] == $i) ? 1 : 0
				);
				$this->quizemodel->saveAnswer($answer_data);
			}
		}
		// Redirect to the list page
		redirect('quize');
	}

	public function delete($id)
	{
		// Delete question and answers
		$this->quizemodel->deleteAnswers($id);
		$this->quizemodel->deleteQuestion($id);

		return true;
		
	}
}
