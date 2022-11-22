<?php

use Predis\Client;
//use Carbon\Carbon;


if (!function_exists('attempt')) {
	function attempt($credentials)
	{

		return true;
	}
}


if (!function_exists('make_hash')) {
	function make_hash()
	{

		return null;
	}
}

if (!function_exists('format_response')) {
	function format_response($code, $message = 'Finished', $data = [])
	{

		return array('code' => $code, 'message' => $message, 'data' => $data);
	}
}

if (!function_exists('redis_client')) {
	function redis_client()
	{
		return  new Predis\Client();
	}
}

if (!function_exists('CI_INSTANCE')) {
	function CI_INSTANCE()
	{
		$ci = &get_instance();
		return  $ci;
	}
}

if (!function_exists('config')) {
	function config($key)
	{

		$pointer = explode('.', $key);
		$config = $pointer[0];
		$item = $pointer[1];

		CI_INSTANCE()->load->config($config);
		return CI_INSTANCE()->config->item($config)[$item];
	}
}

if (!function_exists('dd')) {
	function dd($message)
	{
		print_r($message);
		exit();
	}
}

if (!function_exists('current_datetime')) {
	function current_datetime()
	{
		// try{
		//  	return Carbon::now();
		// }catch(Exception $ex){
		return date('Y-m-d h:i:s');
		//}
	}
}

if (!function_exists('base64_to_image')) {
	function base64_to_image($base64_string, $storage_path = "uploads")
	{

		$file_name = time() . mt_rand(11111, 999999) . '.' . 'png';

		$output_file = $storage_path . "/" . $file_name;

		// split the string on commas
		// $data[ 0 ] == "data:image/png;base64"
		// $data[ 1 ] == <actual base64 string>
		$data = explode(',', $base64_string);

		file_put_contents($output_file, base64_decode(trim($data[1])));

		return $file_name;
	}
}


if (!function_exists('make_qr_code')) {

	function make_qr_code($data, $store_path = null, $file_name = null)
	{

		$ci = CI_INSTANCE();

		$ci->load->library('ciqrcode');

		if (!$store_path)
			$store_path = "uploads/qrcodes/";

		if (!$file_name)
			$file_name = time() . mt_rand(111, 9999999);

		$file_path = $store_path . $file_name . '.png';
		//generate Qr if it doesn't already exist

		if (!file_exists($file_path)) {

			$params['data'] = $data;
			$params['level'] = 'H';
			$params['size'] = 10;
			$params['savename'] = $file_path;
			$ci->ciqrcode->generate($params);

			return $file_name . '.png';
		}

		return false;
	}
}

if (!function_exists('file_is_image')) {

	function file_is_image($file_name)
	{
		return (str_contains($file_name, '.png') || str_contains($file_name, '.jpg') || str_contains($file_name, '.jpeg'));
	}
}


if (!function_exists('array2csv')) {
	function array2csv(array &$array)
	{
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row) {
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}
}

if (!function_exists('download_send_headers')) {
	function download_send_headers($filename)
	{
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}
}

if (!function_exists('is_known_client')) {
	function is_known_client()
	{

		$cookie_name = "client_token";
		return (!isset($_COOKIE[$cookie_name])) ? false : true;
	}
}

if (!function_exists('mark_known_client')) {
	function mark_known_client()
	{
		$cookie_name = "client_token";
		setcookie($cookie_name, sha1("yes"), time() + (86400 * 30 * 90), "/");
	}
}

if (!function_exists('get_relationships')) {
	function get_relationships()
	{
		return [
			'Son',
			'Daughter',
			'Brother',
			'Wife',
			'Mother',
			'Father',
			'Grand Child',
			'Grand Parent',
			'In Law',
			'Friend',
			'Other'
		];
	}
}
