<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "site";
		$this->title  = "Site";

		$this->load->model('Site_model','sitemodel');
		
	}

	public function landing()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		render_site('landing',$data,true);
	}

	public function index()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['categories'] = array(
			["title"=>"Health Security Themes",
			"icon"=>"icon-activity",
			'link'=>"healththemes",
			"image"=>"theme.png"
			],
			["title"=>"Authors","icon"=>"icon-users",'link'=>"authors","image"=>"author.png"],
			["title"=>"Geographical Coverage","icon"=>"icon-radio",'link'=>"areas","image"=>"location.png"],
			//["title"=>"All","icon"=>"icon-search",'link'=>"search"],
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

	public function areas()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['areas'] = $this->geoareasmodel->get();
		render_site('areas',$data);
	}

	public function subthemes($theme_id)
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['thematic_area_id'=>$theme_id];

		$data['theme']        = $this->healththemesmodel->find($theme_id);
		$data['subthemes'] = $this->subthemesmodel->get($filter);

		render_site('sub_themes',$data);
	}

	public function publications($subtheme_id)
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['sub_thematic_area_id'=>$subtheme_id];

		$data['subtheme']     = $this->subthemesmodel->find($subtheme_id);
		$data['results']['publications'] = $this->publicationsmodel->get($filter );

		render_site('theme_publications',$data);
	}

	public function author_pubs($author_id){

		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['author_id'=>$author_id];

		$data['author']     = $this->authorsmodel->find($author_id);
		$data['results']['publications'] = $this->publicationsmodel->get($filter );

		render_site('author_publications',$data);
	}

	public function areas_pubs($region_id){

		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['geographical_coverage_id'=>$region_id];

		$data['region']     = $this->geoareasmodel->find($region_id);
		$data['results']['publications'] = $this->publicationsmodel->get($filter);

		render_site('area_publications',$data);
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
		$data['module']    = $this->module;
		flash_form();
		
		$data['results'] = $this->sitemodel->search($this->input->post('term'));
	
		render_site('search',$data);
	}


}