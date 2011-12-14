<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Log model
class Logs_model extends Base_Model {

	// Add new log record
	public function add($data){
		$this->db->insert('projects_pledges_log', $data); 
	}

}