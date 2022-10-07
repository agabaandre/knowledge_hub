<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_model extends CI_Model {

	
	public function __Construct(){

		parent::__Construct();
	}


	public function search($term,$limit=null,$start=0){

		$this->applyFilter($term);

		if($limit){
			$this->db->limit($limit,$start);
		}

		$results = $this->db->get('publication')->result();

		foreach ($results as $pub) {
			$pub = $this->publicationsmodel->attach_related($pub);
		}
		 
		return $results;
	}

	public function count($term){	
		$this->applyFilter($term);
		return count($this->db->get('publication')->result());
	}

	public function applyFilter($term){

		$search = ['search_key'=>$term];

		$authors    = $this->get_authors($search);
		$sub_themes = $this->get_subthemes($search);

		$this->db->like('publication',$term);
		$this->db->or_where_in('author_id ',array_values($authors));
		$this->db->or_where_in('sub_thematic_area_id',array_values($sub_themes));
	}


	public function get_authors($search){

	  	$authors      = $this->authorsmodel->get($search);
	  	$author_ids   = [];

		foreach ($authors as $row) {
	  		array_push($author_ids, $row->id);
	  	}

	  	return $author_ids;
	}

	public function get_subthemes($search){

	  	$themes       = $this->healththemesmodel->get($search);
	  	$subthemes_ids = [];

		  foreach ($themes as $row):

		  		$filter = ['thematic_area_id'=>$row->id];
		  	    $subthemes       = $this->subthemesmodel->get($filter);

		  	    //subthemes
		  	    foreach ($subthemes as $sub) {
		  	    	array_push($subthemes_ids, $sub->id);
		  	    }

		  endforeach;

		return $subthemes_ids;
	}

}
