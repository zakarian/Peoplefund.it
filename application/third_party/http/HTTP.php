<?php

class HTTP {

	static protected function fetch($curl) {
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);
		if ($response === false)
			return $response;
		$response = str_replace("\r", "", $response);
		list($rawheaders, $content) = explode("\n\n", $response, 2);
		$rawheaders = explode("\n", $rawheaders);
		$status = array_shift($rawheaders);
		list(, $status) = explode(' ', $status, 2);
		list($code, $status) = explode(' ', $status, 2);
		$headers = array();
		foreach ($rawheaders as $header) {
			list($name, $value) = explode(': ', $header, 2);
			$headers[strtolower($name)] = $value;
		}
		return array('code' => $code, 'status' => $status, 'headers' => $headers, 'content' => $content);
	}

	static protected function download($curl, $file) {
		if (!($fp = fopen($file, 'w')))
			return false;
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_FILE, $fp);
		$response = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if (!$response) {
			unlink($file);
			return false;
		}
		return $code;
	}

	static public function get($url, array $params = array()) {
		if (count($params))
			// add params to query
			$url .= ((strpos($url, '?') !== false)? '&': '?').http_build_query($params);
		$curl = curl_init($url);
		return HTTP::fetch($curl);
	}
	
	static public function get_content($url, array $params = array()) {
		$response = HTTP::get($url, $params);
		return $response['content'];
	}
	
	static public function get_download($file, $url, array $params = array()) {
		if (count($params))
			// add params to query
			$url .= ((strpos($url, '?') !== false)? '&': '?').http_build_query($params);
		$curl = curl_init($url);
		return HTTP::download($curl, $file);
	}

	static public function post($url, array $params = array()) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		if (count($params))
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		return HTTP::fetch($curl);
	}

	static public function post_content($url, array $params = array()) {
		$response = HTTP::post($url, $params);
		return $response['content'];
	}

	static public function post_download($file, $url, array $params = array()) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		if (count($params))
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		return HTTP::download($curl, $file);
	}

}