<?php namespace Kandouwo\Libraries;

class CurlHelp {
	public static function post($url, $data) {
		//$url = "http://localhost/web_services.php";
		//$post_data = array ("username" => "bob","key" => "12345");

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$output = curl_exec($ch);
		if ($ch != false) curl_close($ch);

		return $output;
	}
	
	public static function get($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		if ($ch != false) curl_close($ch);

		return $output;
	}
}