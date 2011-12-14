<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Pages model
class Pages_model extends Base_Model {
		
	public function get_next_order_id($nav){

		foreach($nav as $key=>$value) {
			if($value < 1) return 0;
			$nav = $key;
			if($nav == 'in_foot')
				$order_column = 'order_foot';
			else if($nav == 'in_main')
				$order_column = 'order_main';
		}
		
        $result = $this->db->query("SELECT MAX($order_column) as order_column FROM `pages` WHERE $nav = 1 LIMIT 1");
		$return = (array) reset($result->result());
		
		if(empty($return['order_column'])){
			$return['order_column'] = 1;
		}
		
		return ($return['order_column'] + 1);
	}
	
	// Move pages
	public function move_pages($idpage, $direction, $nav){
		
		$data = reset($this->db->query('SELECT * FROM `pages` WHERE idpage = "'.$idpage.'" LIMIT 1')->result());
		
		// Detect nav position
		if($nav == 'in_foot') {
			$order_id = $data->order_foot;
			$order_column = 'order_foot';
		} else if($nav == 'in_main') {
			$order_id = $data->order_main;
			$order_column = 'order_main';
		}
			
		$section_id = $data->idsection;

		// Move down
		if($direction == 'down'){
			$next = $this->db->query('	
				SELECT *
				FROM pages 
				WHERE '.$order_column.' < '.$order_id.' AND '.$nav.' = 1
				ORDER BY '.$order_column.'
				DESC LIMIT 1
			')->result();
			
			if(!empty($next)){ 
				$next = $next[0];
				$this->db->query('UPDATE pages SET '.$order_column.' = '.$order_id.' WHERE '.$order_column.' = '.$next->$order_column.' AND '.$nav.' = 1');
				$this->db->query('UPDATE pages SET '.$order_column.' = '.$next->$order_column.' WHERE idpage = "'.$idpage.'" AND '.$nav.' = 1');
			}
			
		// Move up
		} else {
			$next = $this->db->query('	
				SELECT *
				FROM pages 
				WHERE '.$order_column.' > '.$order_id.' AND '.$nav.' = 1
				ORDER BY '.$order_column.'
				ASC LIMIT 1
			')->result();
			
			if(!empty($next)){ 
				$next = $next[0];
				$this->db->query('UPDATE pages SET '.$order_column.' = '.$order_id.' WHERE '.$order_column.' = '.$next->$order_column.' AND '.$nav.' = 1');
				$this->db->query('UPDATE pages SET '.$order_column.' = '.$next->$order_column.' WHERE idpage = "'.$idpage.'" AND '.$nav.' = 1');
			}
		}
	}
	
    // Get all pages
    public function get_pages($where = "", $limit = "", $nav = "") {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);
		
		if($nav == 'in_foot') {
			$order_column = 'order_foot';
		} else if($nav == 'in_main') {
			$order_column = 'order_main';
		} else
			$order_column = 'idpage';
		
        // Get pages
        $result = $this->db->query("SELECT * FROM `pages` $where $limit ORDER BY $order_column DESC");
		
		// Return data
		return $result->result();
    }
	
	// Add new pages
	public function add_page($data){
		$this->db->insert('pages', $data); 
	}
	
	// Save pages
	public function save_page($data, $idpage){
		$this->db->where('idpage', $idpage);
		$this->db->update('pages', $data); 
	}
	
	// Delete page
	public function delete_page($idpage){
		$this->db->delete('pages', array('idpage' => $idpage)); 
	}
	
	// Get all sections
    public function get_sections($where = "", $limit = "") {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get sections
        $result = $this->db->query("SELECT * FROM `pages` ".$where);
		
		// Return data
		return $result->result();
    }

	// Add new section
	public function add_section($data){
		$this->db->insert('pages_sections', $data); 
	}
	
	// Save section
	public function save_section($data, $idsection){
		$this->db->where('idsection', $idsection);
		$this->db->update('pages_sections', $data); 
	}
	
	// Delete section
	public function delete_section($idsection){
		$this->db->delete('pages_sections', array('idsection' => $idsection)); 
		$this->db->delete('pages', array('idsection' => $idsection)); 
	}
}