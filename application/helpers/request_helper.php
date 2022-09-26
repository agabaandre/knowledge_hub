<?php

//get user by id
if (!function_exists('request_data')) {
    function request_data()
    {
        return (Object) json_decode(file_get_contents('php://input'));
    }
}

if (!function_exists('cast')) {

	function cast($jsondata, $className)
	{
	    $json= json_decode(json_encode($jsondata),true);  //JSON to stdClass
	    $class = $className;

	    $reflection = new \ReflectionClass("App\dtos\\".$class);
	    $instance = $reflection->newInstanceWithoutConstructor();
	    $keys = array_keys($json);

	    foreach ($keys  as $key => $property) {
	        $instance->{$property} =$json[$property];
	    }
	    
	    return $instance;
	}
	
}

if(!function_exists('request_headers')){
	function request_headers(){
		return getallheaders();
	}
}


if(!function_exists('get_header')){
	function get_header($key){

		$headers = (object) getallheaders();
		return @$headers->{$key};
	}
}

if(!function_exists('verify_api_token')){
	function verify_api_token(){
		if(get_header('Authorization')!==null):

			$apiKey = explode(" ",get_header('Authorization'));
			$token = $apiKey[1];
			$user = CI_INSTANCE()->db->where('api_token',$token)
			->where_in('status',[1,3]) //active or reset
			->get('users')->row();
		else:
			$user = null;
	    endif;

		return ($user)?true:false;
	}
}
