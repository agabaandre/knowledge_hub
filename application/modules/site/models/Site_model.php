<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_model extends CI_Model {

	
	public function __Construct(){

		parent::__Construct();
	}


	public function search($term,$type="all"){

		$search = ['search_key'=>$term];

		if($type == "all"){

		  	$data['publications'] = $this->publicationsmodel->get($search);
		  	$data['authors']      = $this->authorsmodel->get($search);
		  	$data['themes']       = $this->healththemesmodel->get($search);
		}
		else if($type == "themes"){
 		 	$data['themes']       = $this->healththemesmodel->get($search);
		}
		else if($type == "authors"){
 		 	$data['themes']       = $this->authorsmodel->get($search);
		}

		return $data;
	}


}
