<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_model extends CI_Model {

	
	public function __Construct(){

		parent::__Construct();
	}


	public function search($term,$type="all"){

		$search = ['search_key'=>$term];


		 $publications = $this->publicationsmodel->get($search);
		 $theme_pubs   = $this->get_theme_pubs($search);
		 $author_pubs  = $this->get_author_pubs($search);


		 $data = array_merge($publications,$theme_pubs,$author_pubs);
		 
		 return $this->lossless_array_merge($data);
	}

	private function lossless_array_merge($input_data) {
	  
	  $data = array();
	  $added = [];

	  foreach ($input_data as $a) {

	      if(!in_array($a->id, $added)){

	        array_push($data, $a);
	        array_push($added, $a->id);

	      }

	  }

	  return $data;
	}


	public function get_author_pubs($search){

		  	$authors      = $this->authorsmodel->get($search);
		  	$author_ids   = [];

			foreach ($authors as $row) {
		  		array_push($author_ids, $row->id);
		  	}

		$this->db->where_in('author_id',$author_ids);
		$pubs = $this->db->get('publication')->result();

		foreach ($pubs as $pub) {
			$pub->sub_theme = $this->subthemesmodel->find($pub->sub_thematic_area_id);
		}

		return $pubs;

	}

	public function get_theme_pubs($search){

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

		$this->db->where_in('sub_thematic_area_id',$subthemes_ids);
		$pubs = $this->db->get('publication')->result();

		foreach ($pubs as $pub) {
			$pub->sub_theme = $this->subthemesmodel->find($pub->sub_thematic_area_id);
		}

		return $pubs;
	}

}
