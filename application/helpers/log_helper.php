<?php

if(!function_exists('log_data')){
	function log_data($key,$data){

		$data = (is_array($data) || is_object($data))?json_encode($data):$data;
		
		log_message('debug',$key);
		log_message('debug',$data);
	}
}




