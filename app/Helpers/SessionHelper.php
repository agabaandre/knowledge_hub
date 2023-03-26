<?php

use Illuminate\Support\Facades\Auth;

if(!function_exists('get_cookie')){
	function get_cookie($cookie_name){

		return (isset($_COOKIE[$cookie_name]))?$_COOKIE[$cookie_name]:null;
	}
}


if(!function_exists('set_cookie')){
	function set_cookie($cookie_name,$value="yes"){
		setcookie($cookie_name, $value, time() + (86400 * 30 * 90), "/");
	}
}


if(!function_exists('current_user')){
	function current_user(){
		return Auth::user();
	}
}




?>