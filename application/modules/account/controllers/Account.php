<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends MX_Controller
{


	public  function __construct()
	{
		parent::__construct();

		$this->module = "account";
		$this->title  = "Account";
		$this->load->model('account_model','accountmodel');
		$this->load->model('auth/auth_mdl');
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = "Our Publications";
		$data['publications'] = $this->publicationsmodel->get(['author_id'=>user_session()->user_id]);

		render_site('list', $data,true);
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
		$data['publications'] = $this->publicationsmodel->find($id);

		render_site('form', $data,true);
	}

	public function save()
	{

		$is_error = false;

		$theme = [
			'id'           => @$this->input->post("id"), 
			'sub_thematic_area_id' => @$this->input->post("sub_thematic_area_id"), 
			'publication'  => $this->input->post("publication"),
			'author_id'    => $this->input->post("author"),
			'geographical_coverage_id' => $this->input->post("geographical_coverage"), 
			'title' 	   => $this->input->post("title"), 
			'description'  => $this->input->post("description"), 
			'file_type_id' => $this->input->post("file_type"),
			'is_active'    => $this->input->post("is_active")
		];

		$resp = $this->publicationsmodel->save($theme);
		redirect('account');
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

	public function register()
	{
		//dd($this->input->post());


		if($this->input->post('submit')){
			flash_form();

			
			$organization_name = null;

			if($this->input->post('orgName')):
			   $organization_name  = $this->input->post('orgName');
			endif;

			$firstname  = $this->input->post('firstname');
			$othernames = $this->input->post('othernames');
			$email      = $this->input->post('email');
			$password   = $this->input->post('password');

			$hash = $this->argonhash->make($password);


		  $user = array(
			"name"=>$firstname." ".$othernames,
			"email"=>$email,
			"username"=>$email,
			"organization_name"=>$organization_name,
			"password"=>$hash
		  );

		$saved = $this->accountmodel->insert_user($user);

	    if($saved):
			clear_form();
			$msg   = "Request submitted successfully, standby for a an email notification upon approval";
			$error = false;
		else:
			$msg   = "Registration failed, try again";
			$error = true;
		endif;

		set_flash($msg,$error);
		redirect('account/register');
	  
		}else{

		$data['module'] = $this->module;
		$data['title']  = "Register";
		render_site('register', $data,true);

		}
	}

	
	public function login()
	{
	  $postdata = $this->input->post();
	  $password = $this->input->post('password');
	  $data = $this->auth_mdl->login($postdata);
	  $adata = (array) $data;
	 
	  $auth = ($this->argonhash->check($password, $adata['password']));
	  unset($adata['password']);
	
	  if ($auth) {
		
		$adata['author']   = $this->authorsmodel->find($adata['author_id']);
		$adata['country']  = $this->auth_mdl->access_level2($adata['user_id']);
		$adata['is_admin'] = false;
		
		$_SESSION['user'] = (object)$adata;

		$this->session->set_userdata($adata);
		set_flash("Welcome back!");
		redirect(base_url());
  	
	  } else {
		set_flash("Login failed, wrong username or password",true);
		redirect(base_url());
	  }
	}
  
  
	public function logout()
	{
	  session_unset();
	  session_destroy();
	  redirect( base_url());
	}
	
}
