<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

if(!function_exists('settings')){
	 function settings()
	 {
		return null;
	 }
}


function get_role($userId){

    $user_role = DB::table("model_has_roles")->where('model_id',$userId)->first();

    $role = ($user_role)?(($user_role)?Role::find($user_role->role_id):null):null;

	return $role;
}


function is_admin(){
	$role = get_role(current_user()->id);
	return ($role)?((strpos(strtolower($role->name),'admin') >-1)?true:false):false;
}




?>