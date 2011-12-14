<?php

class GoCardless {
	var $subdomain = '';

	// Format of the request
	var $format = 'json';

	// App details
	var $app_id = '';
	var $app_secret = '';
	

	// URLs
	var $oauth_authorize_url;
	var $oauth_access_token_url;
	var $subscription_url;
	var $one_off_bill_url;
	var $pre_authorization_url;
	var $confirmation_url;
	
	var $redirect_uri;
	var $cancel_uri;
	var $state;
	
	var $nonce;
	var $timestamp;
	
	public function GoCardless() {
		$this->go_live();
		// $this->go_test();

		$this->set_nonce();
		$this->set_timestamp();
		$this->set_urls();
	}
	
	function go_live() {
		$this->app_id = 'app_id';
		$this->app_secret = 'app_secret';

		$this->subdomain = '';

		$this->set_urls();
	}
	
	function go_test() {
		$this->app_id = 'app_id';
		$this->app_secret = 'app_secret';

		$this->subdomain = 'sandbox.';
		
		$this->set_urls();
	}
	
	function set_nonce() {
		$this->nonce = md5(microtime());
	}
	
	function set_timestamp() {
		$this->timestamp = date('c');
	}
	
	function set_urls() {
		$this->oauth_authorize_url = 'https://'.$this->subdomain.'gocardless.com/oauth/authorize';
		$this->oauth_access_token_url = 'https://'.$this->subdomain.'gocardless.com/oauth/access_token';
		$this->subscription_url = 'https://'.$this->subdomain.'gocardless.com/connect/subscriptions/new';
		$this->one_off_bill_url = 'https://'.$this->subdomain.'gocardless.com/connect/bills/new';
		$this->pre_authorization_url = 'https://'.$this->subdomain.'gocardless.com/connect/pre_authorizations/new';
		$this->confirmation_url = 'https://'.$this->subdomain.'gocardless.com/api/v1/confirm';
		
		$this->pre_authorizations_merchant_bills_url = 'https://'.$this->subdomain.'gocardless.com/api/v1/merchants/{merchant_id}/pre_authorizations';
		$this->pre_authorizations_bill_url = 'https://'.$this->subdomain.'gocardless.com/api/v1/bills';
	}
	
	public function get_error_message($code) {
		switch($code) {
			case 200:
				$error = 'The request has succeeded'; break;
			case 201:
				$error = 'The request has been fulfilled and resulted in a new resource being created. The newly created resource can be referenced by the URI(s) returned in the entity of the response, with the most specific URI for the resource given by a Location header field.'; break;
			case 400:
				$error = 'The request could not be understood by the server, usually due to malformed syntax.'; break;
			case 401:
				$error = 'Unauthorized. The client has not provided a valid Authentication HTTP header'; break;
			case 403:
				$error = 'Forbidden. The client has provided a valid Authentication header, but it does not have permission to access this resource.'; break;
			case 404:
				$error = 'Not Found. The requested resource was not found.'; break;
			case 412:
				$error = 'Precondition Failed. Certain unmet conditions must be fulfilled before the request to be processed.'; break;
			case 418:
				$error = 'I\'m a teapot. The webserver cannot respond as it is temporarily a teapot.'; break;
			case 422:
				$error = 'Unprocessable Entity. Could not process a POST request because the request is invalid.'; break;
			case 500:
				$error = 'Internal Server Error. The server encountered an error while processing your request and failed. Please report this to the GoCardless support team.s'; break;
			default:
				$error = ''; break;
		}

		return $error;
	}
	
	function convert_to_encoded_query($params = array()) {
		if(empty($params)) return '';
		
		$str = array();

		// Sorting the parameters by key asc
		ksort($params);
		
		foreach($params as $key => $value)
			$str[] = rawurlencode($key) . '=' . rawurlencode($value);

		// Generate the string and return url encoded values
		return join('&', $str);
	}
	
	function generate_signature($string) {
		return hash_hmac('sha256', $string, $this->app_secret);
	}
	
	// Connection Service
	function http_post($url, array $params = array(), array $headers = array()) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);

		if (count($headers)) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		if (count($params))
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->http_build_query_for_curl($params));

		return $this->http_fetch($curl);
	}

	function http_get($url, array $params = array(), array $headers = array()) {
		if (count($params))
			// add params to query
			$url .= ((strpos($url, '?') !== false)? '&': '?').http_build_query($params);

		$curl = curl_init($url);

		if (count($headers)) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		return $this->http_fetch($curl);
	}
	
	function http_fetch($curl) {
		//turning off the server and peer verification(TrustManager Concept)
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);

		if ($response === false)
			return $response;

		$response = preg_replace("/HTTP\/1.1(.*)HTTP\/1.1/is", "HTTP/1.1", $response);
		$response = str_replace("\r", "", $response);
		@list($rawheaders, $content) = explode("\n\n", $response, 2);

		$rawheaders = explode("\n", $rawheaders);
		$status = array_shift($rawheaders);
		list(, $status) = explode(' ', $status, 2);
		list($code, $status) = explode(' ', $status, 2);

		$headers = array();
		foreach ($rawheaders as $header) {
			@list($name, $value) = explode(': ', $header, 2);
			$headers[strtolower($name)] = $value;
		}

		return array('code' => $code, 'status' => $status, 'headers' => $headers, 'content' => $content);
	}
	
	function http_build_query_for_curl( $arrays, &$return = array(), $prefix = null ) {
		$return = array();

		if ( is_object( $arrays ) ) {
			$arrays = get_object_vars( $arrays );
		}

		foreach ( $arrays AS $key => $value ) {
			$k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
			if ( is_array( $value ) OR is_object( $value )  ) {
				$this->http_build_query_for_curl( $value, $return, $k );
			} else {
				$return[$k] = $value;
			}
		}
		
		return $return;
	}
}

?>