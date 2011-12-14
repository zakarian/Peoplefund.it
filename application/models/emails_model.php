<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Emails model
class Emails_model extends Base_Model {

	// Get the notication template
	public function get_notification_templates($slugs_arr){
		$return = array();
		
		foreach($slugs_arr as $slug => $idemail){
			$temp = $this->get_emails(array('idemail' => $idemail));
			if (!empty($temp) && isset($temp[0])){
				$return[ $slug ] = $temp[0]->text;
			}
		}
		
		return $return;
	}

	// Count emails
	public function count_emails($where = array()){
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Get total emails
		$result = $this->db->query("SELECT * FROM `emails` $where")->num_rows();

		// Return results
		return $result;
	}

    // Get all emails
    public function get_emails($where = array(), $limit = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get emails
        $result = $this->db->query("SELECT * FROM `emails` $where $limit");
		
		// Return data
		return $result->result();
    }
	
	// Add new email
	public function add_email($data){
		$this->db->insert('emails', $data); 
	}
	
	// Save email
	public function save_email($data, $idemail){
		$this->db->where('idemail', $idemail);
		$this->db->update('emails', $data); 
	}

}