<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Records extends MX_Controller {

	
	public  function __construct(){
		parent:: __construct();

		$this->module = "records";
		$this->title  = "Records";

		$this->load->model('Records_model','recordsmodel');
		$this->load->model('quotes/Quotes_model','quotesmodel');
		
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
		
		$data['categories'] = $this->get_categories();

		$data['recent']  = $this->publicationsmodel->get([],8,0);
		$data['authors'] = $this->authorsmodel->get([],12,0);
		$data['quotes']  = $this->quotesmodel->get();
		$data['tags']	 = $this->publicationsmodel->get_tags();
		$data['types']   = $this->publicationsmodel->get_types();
	
		render_site('index',$data,true);
	}

	public function get_categories(){

		return array(
			[
				"title"=>"Health Security Themes",
				"icon"=>"fa fa-heart",
				'link'=>"browse/healththemes",
				"image"=>"theme.png",
				"stats"=> $this->healththemesmodel->count()
			],
			[
				"title"=>"Resource Contibuting Authors",
				"icon"=>"fa fa-business-time",
				'link'=>"browse/authors",
				"image"=>"author.png",
				"stats"=> $this->authorsmodel->count() 
			],
			[
				"title"=>"Geographical Coverage",
				"icon"=>"fa fa-map-pin",
				'link'=>"browse/areas",
				"image"=>"location.png",
				"stats"=>$this->geoareasmodel->count()
			],
			[
				"title"=>"Public Discussion Forums",
				"icon"=>"fa fa-comments",
				'link'=>"forums",
				"image"=>"location.png",
				"stats"=>$this->forumsmodel->count()
			]
		);
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
		$data['links']     = paginate('records/author_pubs/'.$subtheme_id,$count, $perPage,$segment);
		$data['types']   = $this->publicationsmodel->get_types();
	

		render_site('theme_publications',$data);
	}

	public function show($id){
		
		$data['publication'] = $this->publicationsmodel->find($id);
		$data['module']       = $this->module;
		$data['title']        = $this->title;

		render_site('publication_detail',$data,true);
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
		$data['links']     = paginate('records/author_pubs/'.$author_id,$count, $perPage,$segment);
		$data['types']   = $this->publicationsmodel->get_types();
	

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
		$data['links']     = paginate('records/areas_pubs/'.$region_id,$count, $perPage,$segment);
		$data['types']   = $this->publicationsmodel->get_types();
	
	
		render_site('area_publications',$data);
	}


	public function authors()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		
		$data['authors'] = $this->authorsmodel->get();
	
		render_site('authors',$data,true);
	}


	public function coverage()
	{
		$data['module'] = $this->module;
		$data['title']  = $this->title;
		$data['authors'] = $this->authorsmodel->get();
	
		render_site('authors',$data,true);
	}

	public function search()
	{
		$data['module']    = $this->module;
		flash_form();

		$term    = $this->input->post('term');
		$type    = $this->input->post('type');

		$count   = $this->recordsmodel->count($term,$type);
		$segment = 3;
        $page    = ($this->uri->segment($segment))?$this->uri->segment($segment):0;
        $perPage = 10;
		
		$data['publications'] = $this->recordsmodel->search($term,$type,$perPage,$page);
		$data['links']     = paginate('records/search',$count, $perPage,$segment);
		$data['types']   = $this->publicationsmodel->get_types();
	
		render_site('search',$data);
	}

    public function autocomplete(){

    	$term = $_GET[ "term" ];
    	$suggestions = [];

    	$results = $this->recordsmodel->search($term,10,0);

    	foreach ($results as $result) {
			$suggestions[] =  array( "label" => substr($result->title,0,100), "value" => substr($result->title,0,100));
	    }

		die(json_encode($suggestions));
	}

	public function quiz(){

		$questions = $this->recordsmodel->get_questions();

		die(json_encode($questions));

	}

}