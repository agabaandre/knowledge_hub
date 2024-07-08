<?php

use App\Jobs\AuditTrailJob;
use App\Models\AuditTrail;
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
		$settings = DB::table("setting")->first();
		$settings->logo = asset('storage/uploads/config/'.$settings->logo);
		$settings->favicon = asset('storage/uploads/config/' . $settings->favicon);
		$settings->spotlight_banner = asset('storage/uploads/config/' . $settings->spotlight_banner);

		return $settings;
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

function filter_access($query){
	$user = @current_user();

	if($user && $user->access_level){

		$level = $user->access_level;

		if($level->access_level_name     == "Viewer" || $level->access_level_name == "Country"):
		 // get for own country
		 
		elseif($level->access_level_name == "RCC"):
             // get for countries in rcc
		elseif($level->access_level_name == "Overall"):
			// get normally
	    endif;

	}

}

if(!function_exists('states_enabled')){
	function states_enabled(){
		return config('deployment.states_enabled');
	}
}


if(!function_exists('admin_units_enabled')){
	function admin_units_enabled(){
		return config('deployment.admin_units_enabled');
	}
}

if(!function_exists('log_user_trail')){
	function log_user_trail($action,$description=null,$old_data=null,$new_data=null){
		$user_id = current_user()->id;
		$auditTrail = new AuditTrailJob($action,$user_id,$description,$old_data,$new_data);
		dispatch($auditTrail);
	}
}


?>