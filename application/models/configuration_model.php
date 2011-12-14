<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// COnfiguration model
class Configuration_model extends Base_Model {

    // Get certain configuration
    public function get_configuration($where = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);

        // Get config
        $result = $this->db->query("SELECT * FROM `configuration` $where");
		foreach($result->result() AS $result){
			$return[$result->name] = $result->value;
		}

		return $return;
    }

	
	//Get all config values in assoc array
	public function get_all_configuration(){
		$return = array(); //It is a good manner to init the vars :)
		
		$sql = " SELECT * FROM `configuration` ";
		$result = $this->db->query($sql);
		
		foreach($result->result() AS $result){
			$return[ $result->name ] = $result->value;
		}
		
		return $return;
	}
	
	
	// Save configuration
	public function save_configuration($data){

		// For each config
		foreach($data AS $name => $value){

			// Update array
			$update = array(
				'value' => $value
			);

			// Form where clause
			$this->db->where('name', $name);
			
			// Execute update query
			$this->db->update('configuration', $update);

			// Unset update params for this configuration option
			unset($update);
		}
	}
}