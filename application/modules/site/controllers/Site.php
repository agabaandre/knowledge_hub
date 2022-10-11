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
		$data['slides'] = $this->slidesmodel->get();
		
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

		$data['theme']     = $this->healththemesmodel->find($theme_id);
		$data['subthemes'] = $this->subthemesmodel->get($filter);

		render_site('sub_themes',$data);
	}

	public function publications($subtheme_id)
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['sub_thematic_area_id'=>$subtheme_id];

		$count   = $this->publicationsmodel->count($filter);
		$segment = 4;
        $page    = ($this->uri->segment($segment))?$this->uri->segment($segment):0;
        $perPage = 10;

		$data['subtheme']     = $this->subthemesmodel->find($subtheme_id);
		$data['publications'] = $this->publicationsmodel->get($filter,$perPage,$page);
		$data['links']     = paginate('site/author_pubs/'.$subtheme_id,$count, $perPage,$segment);

		render_site('theme_publications',$data);
	}

	public function author_pubs($author_id){

		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['author_id'=>$author_id];

		$count   = $this->publicationsmodel->count($filter);
		$segment = 4;
        $page    = ($this->uri->segment($segment))?$this->uri->segment($segment):0;
        $perPage = 10;

		$data['author']     = $this->authorsmodel->find($author_id);
		$data['publications'] = $this->publicationsmodel->get($filter,$perPage,$page);
		$data['links']     = paginate('site/author_pubs/'.$author_id,$count, $perPage,$segment);

		render_site('author_publications',$data);
	}

	public function areas_pubs($region_id){

		$data['module'] = $this->module;
		$data['title']  = $this->title;

		$filter = ['geographical_coverage_id'=>$region_id];

		$count   = $this->publicationsmodel->count($filter);
		$segment = 4;
        $page    = ($this->uri->segment($segment))?$this->uri->segment($segment):0;
        $perPage = 10;

		$data['region']     = $this->geoareasmodel->find($region_id);
		$data['publications'] = $this->publicationsmodel->get($filter,$perPage,$page);
		$data['links']     = paginate('site/areas_pubs/'.$region_id,$count, $perPage,$segment);
	
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

		$term    = $this->input->post('term');

		$count   = $this->sitemodel->count($term);
		$segment = 3;
        $page    = ($this->uri->segment($segment))?$this->uri->segment($segment):0;
        $perPage = 10;
		
		$data['publications'] = $this->sitemodel->search($term,$perPage,$page);
		$data['links']     = paginate('site/search',$count, $perPage,$segment);
	
		render_site('search',$data);
	}

    public function autocomplete(){

    	$term = $_GET[ "term" ];
    	$suggestions = [];

    	$results = $this->sitemodel->search($term,10,0);

    	foreach ($results as $result) {
			$suggestions[] =  array( "label" => $result->title, "value" => $result->title);
	    }

		die(json_encode($suggestions));
	}

}