<?php

require_once(APPPATH.'third_party/facebook/sdk.php');

$ci = get_instance();
$ci->load->config('facebook');
$ci->facebook = new Facebook(array(
	'appId' => $ci->config->item('fb_app_id'),
	'secret' => $ci->config->item('fb_secret'),
	'cookie' => TRUE,
));

/* End of file facebook_helper.php */ 
/* Location: ./application/helpers/facebook_helper.php */