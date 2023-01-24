<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publications extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "publications";
		$this->title  = "Publications";
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = "Publications List";

		$data['publications'] = $this->publicationsmodel->get();

		render('list', $data);
	}

	public function create($id = FALSE)
	{
		$data['module'] = $this->module;

		if ($id) {
			$data['title']  = 'Create Publication';
		} else {
			$data['title']  = 'Create Publication';
		}

		$data['authors'] = $this->authorsmodel->get();
		$data['geoareas'] = $this->geoareasmodel->get();
		$data['subthemes'] = $this->subthemesmodel->get();
		$data['filetypes'] = $this->filetypesmodel->get();
		$data['tags'] = $this->tagsmodel->get();
		$data['publications'] = $this->publicationsmodel->find($id);

		render('form', $data);
	}


	public function save()
	{
		$is_error = false;

		// Load the Upload library
		$this->load->library('upload');

		// For each get the file name and upload it
		$files = $_FILES;

		// Get the cover
		$cover = $files['cover'];

		// Get the publication document
		$publication_file = $files['file'];

		// If the cover is not empty, upload it
		if (!empty($cover['name'])) {
			// Chnage the file name to cover with the extension and timestamp
			$cover['name'] = 'cover' . time() . '.' . pathinfo($cover['name'], PATHINFO_EXTENSION);

			// Set the upload configuration
			$config['upload_path']   = './uploads/publications/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['file_name']     = $cover['name'];

			// Upload the file
			$this->upload->initialize($config);

			// If the upload fails, set the error message
			if (!$this->upload->do_upload('cover')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());
				$is_error = true;
			} else {
				// If the upload is successful, get the file name
				$cover = $this->upload->data('file_name');
			}
		} else {
			$cover = 'cover.png';
		}

		$theme = [
			'id' => @$this->input->post("id"),
			'sub_thematic_area_id' => @$this->input->post("sub_thematic_area_id"),
			'publication' => $this->input->post("link") ?? 'N/A',
			'author_id' => $this->input->post("author"),
			'geographical_coverage_id' => $this->input->post("geographical_coverage"),
			'title' => $this->input->post("title"),
			'description'  => $this->input->post("description", TRUE),
			'file_type_id' => $this->input->post("file_type"),
			'is_active' => $this->input->post("is_active"),
			'cover' => $cover ?? 'cover.png',
		];

		$publication = $this->publicationsmodel->save($theme);


		// If the publication is not empty, upload it
		if (!empty($publication->id) && isset($publication_file['name'])) {
			// Chnage the file name to pub zx  with the extension and timestamp
			$file_name = 'publication' . time() . '.' . pathinfo($publication_file['name'], PATHINFO_EXTENSION);

			// Set the upload configuration
			$config['upload_path']   = './uploads/publications/';
			$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx';
			$config['file_name']     = $file_name;

			// Upload the file
			$this->upload->initialize($config);

			// If the upload fails, set the error message
			if (!$this->upload->do_upload('file')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());

				$is_error = true;
			} else {
				// If the upload is successful, get the file name
				$attachment = $this->upload->data('file_name');
				$this->publicationsmodel->save_attachment($attachment,$publication->id);
			}

		} 

		$response = [];

		// Set Content Type
		header('Content-Type: application/json');


		// If there is no error, save the data
		if (!$is_error) {

			// Get the formdata

			

			if ($publication) {
				$response['status'] = 'success';
				$response['message'] = 'Publication Saved Successfully';

				echo json_encode($response);
			} else {
				$response['status'] = 'error';
				$response['message'] = 'An error occured while saving the publication';

				echo json_encode($response);
			}
		} else {
			// Set response status
			$response['status'] = 'error';
			$response['message'] = $this->session->flashdata('error') ? $this->session->flashdata('error') : 'An error occured while uploading the files';

			echo json_encode($response);
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$title = $this->input->post('title');
		$description = $this->input->post('description');

		$data = [
			'id' => $id,
			'title' => $title,
			'description' => $description
		];

		$resp = $this->publicationsmodel->update($data);

		$response = [];
		$response['status'] = 'success';
		$response['message'] = 'Publication Updated Successfully';

		echo json_encode($response);
	}

	public function delete($id)
	{
		$resp = $this->publicationsmodel->delete($id);
		$is_error = false;

		if ($resp) {
			$msg = "Success";
		} else {
			$msg = "False";
		}

		die($msg);
	}

	public function comment()
	{
		//if(captcha_valid()):
			$this->publicationsmodel->save_comment($this->input->post());
			set_flash("Comment submitted successfully");
		// else:
		// 	flash_form();
		// 	set_flash("Invalid confirmation text",true);
		// endif;
		redirect('records/show/' . $this->input->post('publication_id'));
	}

	public function add_to_favourites($id)
	{
		$this->publicationsmodel->save_favourite($id);
		set_flash("Added to favourites successfully");
		redirect('records/favourites');
	}

	public function remove_favourite($id){
		$this->publicationsmodel->delete_favourite($id);
		set_flash("Removed from favourites successfully");
		redirect('records/favourites');
	}

	// Moderate publications
	public function moderate()
	{
		$data['module'] = $this->module;
		$data['title']  = "Moderate Publications";
		// Get all forums with comments that are not approved
		$data['publications'] = $this->publicationsmodel->get_publications_with_pending_comments();
		render("publications_moderate", $data);
	}

	// Approve comment
	public function approve_comment($id)
	{
		// Accept json data
		header('Content-Type: application/json');
		header('Accept: application/json');

		$this->publicationsmodel->approve_comment($id);
		$response = [
			'status' => 'success',
			'message' => 'Comment approved successfully'
		];

		echo json_encode($response);
	}

	// Reject comment
	public function reject_comment($id)
	{
		// Accept json data
		header('Content-Type: application/json');
		header('Accept: application/json');

		$this->publicationsmodel->reject_comment($id);
		$response = [
			'status' => 'success',
			'message' => 'Comment rejected successfully'
		];

		echo json_encode($response);
	}

	
}
