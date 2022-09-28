<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "site";
		$this->title  = "Site";
		
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['categories'] = array(
			["title"=>"Health Security Themes","icon"=>"icon-activity",'link'=>"healththemes"],
			["title"=>"Authors","icon"=>"icon-users",'link'=>"authors"],
			["title"=>"Geographical Coverage","icon"=>"icon-radio",'link'=>""],
			["title"=>"All","icon"=>"icon-search",'link'=>"search"],
		);
	
		render_site('home',$data);
	}

	public function themes()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['themes'] = $this->healththemesmodel->get();
		render_site('themes',$data);
	}

	public function authors()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['authors'] = $this->authorsmodel->get();
	
		render_site('authors',$data);
	}


	public function coverage()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['authors'] = $this->authorsmodel->get();
	
		render_site('authors',$data);
	}

	public function search()
	{
		$data['module'] = $this->module;
	
		render_site('search',$data);
	}


}