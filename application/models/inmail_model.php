<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Inmail model
class Inmail_model extends Base_Model {

	// Count messages
	public function count_messages($where = array()) {
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Get total messages
		$result = $this->db->query("SELECT m.*, s.username AS sender_username, r.username AS receiver_username FROM `messages` AS m
									LEFT JOIN users AS s ON m.idsender = s.iduser
									LEFT JOIN users AS r ON m.idreceiver = r.iduser
									$where")->num_rows();

		// Return results
		return $result;
	}

    // Get all messages
    public function get_messages($where_arr = array(), $limit = array()) {

		
        // Generate where clause
        $where = self::where_string_from_array($where_arr);
		
		// Generate limit
		$limit = self::generate_limit($limit);

		$order_sql = '';
		
		if (!empty($where_arr['idreceiver'])){
			$order_sql .= ' m.status_receiver ASC, ';
		}

		
        // Get messages
        $result = $this->db->query("SELECT m.*, s.name AS sender_name, s.username AS sender_username, r.name AS receiver_name, r.username AS receiver_username 
									FROM `messages` AS m
									LEFT JOIN users AS s ON m.idsender = s.iduser
									LEFT JOIN users AS r ON m.idreceiver = r.iduser
									$where
									
									ORDER BY $order_sql m.date_sent DESC
									
									$limit
									
									");
		
		// Return data
		return $result->result();
    }
	
	// Get stats
	public function get_messages_stats(){
		
		// Get stats for 1 day
		$date_start_1 = date('Y-m-d H:i:s', strtotime("-1 day"));
		$result = $this->db->query("SELECT * FROM `messages` WHERE date_sent >= '$date_start_1'")->num_rows();
		$return['1'] = $result;
		
		// Get stats for 7 day
		$date_start_7 = date('Y-m-d H:i:s', strtotime("-7 day"));
		$result = $this->db->query("SELECT * FROM `messages` WHERE date_sent >= '$date_start_7'")->num_rows();
		$return['7'] = $result;
		
		// Get stats for 30 day
		$date_start_30 = date('Y-m-d H:i:s', strtotime("-30 day"));
		$result = $this->db->query("SELECT * FROM `messages` WHERE date_sent >= '$date_start_30'")->num_rows();
		$return['30'] = $result;
		
		// Get total stats
		$result = $this->db->query("SELECT * FROM `messages`")->num_rows();
		$return['total'] = $result;
		
		// Return results
		return $return;
	}
	
	// Save message
	public function save_message($data, $idmessage){
		$this->db->where('idmessage', $idmessage);
		$this->db->update('messages', $data); 
	}

	// Add message
	public function add_message($data){
		$this->db->insert('messages', $data); 
	}
}