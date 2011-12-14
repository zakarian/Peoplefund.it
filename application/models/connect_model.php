<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Connect model
class Connect_model extends Base_Model {

	// Describe valid services
	var $services = array('energyshare');
	
	// Class constructor
	public function __construct() {
		parent::__construct();
	}

	// Check service
	public function check_service($uri) {
		// Get URL parts and extract params
		foreach($uri AS $segment){
			if(strstr($segment, "service")){

				$service = addslashes(urldecode(str_replace("service:", "", $segment)));
				
				// check for valid service
				if(in_array($service, $this->services))
					break;
			}
		}
		
		return (isset($service) ? $service : FALSE);
	}
}