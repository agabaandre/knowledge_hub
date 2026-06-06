<?php

use App\Jobs\AuditTrailJob;
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
		$user = Auth::user();
		if (!$user) {
			return null;
		}
		// Get raw photo value to avoid triggering the accessor
		$rawPhoto = $user->getRawOriginal('photo');
		$isPhotoExternal = $user->is_photo_external ?? 0;
		
		// Only modify photo if needed, avoid loading unnecessary relationships
		if ($isPhotoExternal && !empty($rawPhoto)) {
			// Photo is external, use as-is (already a URL)
			$user->photo = $rawPhoto;
		} elseif (!empty($rawPhoto)) {
			// Use raw photo value to avoid recursive loop in user_profile_photo
			$user->photo = user_profile_photo($rawPhoto);
		} else {
			// Return null instead of default image so we can show icon fallback
			$user->photo = null;
		}
		return $user;
	}
}

if(!function_exists('settings')){
	 function settings()
	 {
		$minutes = 60 * 24; // 24 hours

        $base = cache()->remember('settings', $minutes, function () {
			$settings = DB::table("setting")->where('status', 'active')->first();
			if (!$settings) {
				$settings = DB::table("setting")->first();
			}
			if ($settings) {
				$settings->logo = !empty($settings->logo) ? asset('storage/uploads/config/'.$settings->logo) : '';
				$settings->favicon = !empty($settings->favicon) ? asset('storage/uploads/config/' . $settings->favicon) : '';
				$settings->spotlight_banner = !empty($settings->spotlight_banner) ? asset('storage/uploads/config/' . $settings->spotlight_banner) : '';
			}
			return $settings;
        });

		// Per-theme overlay: load theme_settings for active theme (theme1., et, etc.) so each theme has its own config
		$siteTheme = $base && isset($base->site_theme) ? trim((string) $base->site_theme) : '';
		$themeKey = $siteTheme === 'theme1.' ? 'theme1' : ($siteTheme !== '' ? $siteTheme : null);
		if ($base && $themeKey !== null && \Illuminate\Support\Facades\Schema::hasTable('theme_settings')) {
			$cacheKey = 'theme_settings_' . $themeKey;
			$overlay = cache()->remember($cacheKey, $minutes, function () use ($themeKey) {
				return DB::table('theme_settings')->where('theme', $themeKey)->pluck('value', 'key')->toArray();
			});
			$settings = clone $base;
			foreach ($overlay as $key => $value) {
				$settings->{$key} = $value;
			}
			// Re-apply image URLs (values from theme_settings are filenames)
			if (!empty($settings->logo) && strpos($settings->logo, 'http') !== 0 && strpos($settings->logo, '//') !== 0) {
				$settings->logo = asset('storage/uploads/config/' . $settings->logo);
			}
			if (!empty($settings->favicon) && strpos($settings->favicon, 'http') !== 0 && strpos($settings->favicon, '//') !== 0) {
				$settings->favicon = asset('storage/uploads/config/' . $settings->favicon);
			}
			if (!empty($settings->spotlight_banner) && strpos($settings->spotlight_banner, 'http') !== 0 && strpos($settings->spotlight_banner, '//') !== 0) {
				$settings->spotlight_banner = asset('storage/uploads/config/' . $settings->spotlight_banner);
			}
			return $settings;
		}

		return $base;
	 }
}


function get_role($userId){

    $user_role = DB::table("model_has_roles")->where('model_id',$userId)->first();
    $role = ($user_role)?(($user_role)?Role::find($user_role->role_id):null):null;

	return $role;
}


function is_admin(){
	if(!auth()->user())
		return false;

	$role = get_role(auth()->user()->id);
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
		return function_exists('hub_admin_units_enabled')
			? hub_admin_units_enabled()
			: (bool) config('deployment.admin_units_enabled');
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