<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Categories model
class Categories_model extends Base_Model {

    // Get all categories
    public function get_categories($includeSubcategories = TRUE, $onlyActive = TRUE) {
	
		// Check if we are getting only active categories
		if($onlyActive){
			$onlyActive = " AND active = 1";
		} else {
			$onlyActive = "";
		}

        // Get categories
        $result = $this->db->query("SELECT * FROM `categories` WHERE idsubcategory = 0 $onlyActive ORDER BY `order` ASC")->result();

		// If adding subcategories
		if($includeSubcategories){
			foreach($result AS &$category){
				$category->subcategories = $this->db->query("SELECT * FROM `categories` WHERE idsubcategory = '".(int) $category->idcategory."' $onlyActive ORDER BY `order` ASC")->result();
			}
		}

		// Return data
		return $result;
    }
	
	// Get single category
    public function get_category($where) {
		
		// Generate where clause
        $where = self::where_string_from_array($where);

        // Get caategories
        $result = $this->db->query("SELECT * FROM `categories` $where LIMIT 1")->result();

		// Return data
		return $result[0];
    }
	
	// Add new category
	public function add_category($data){
		$this->db->insert('categories', $data); 
	}
	
	// Save category
	public function save_category($data, $idcategory){
		$this->db->where('idcategory', $idcategory);
		$this->db->update('categories', $data); 
	}

	// Delete category
	public function delete_category($idcategory){
		$this->db->delete('categories', array('idcategory' => $idcategory)); 
	}
	
	// Get next order id
	public function get_next_order_id($where){
	
		// Generate where clause
        $where = self::where_string_from_array($where);
		
        // Get categories
        $result = $this->db->query("SELECT `order` FROM `categories` $where ORDER BY `order` DESC LIMIT 1");
		
		// Fetch data
		$return = (array) reset($result->result());
		
		// If no categories are found
		if(empty($return['order'])){
			$return['order'] = 0;
		}
		
		// Return data
		return ($return['order'] + 1);
	}
	
	// Move category
	public function move_category($idcategory, $direction){
		
		// Get current category data
		$data = $this->db->query("SELECT * FROM `categories` WHERE idcategory = ".(int) $idcategory." LIMIT 1")->result();
		$data = reset( $data );
		$now = $data->order;
		$idsubcategory = $data->idsubcategory;

		// Move down
		if($direction == 'down'){
			$next = $this->db->query("SELECT `order` FROM categories WHERE idsubcategory = '".(int)$idsubcategory."'
									AND `order` > '$now' ORDER BY `order` ASC LIMIT 1")->result();

			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE categories SET `order` = '$now' WHERE `order` = '$next' AND idsubcategory = $idsubcategory");
				$this->db->query("UPDATE categories SET `order` = '$next' WHERE idcategory = '$idcategory'");
			}
			
		// Move up
		} else {
			$next = $this->db->query("SELECT `order` FROM categories WHERE idsubcategory = '".(int) $idsubcategory."'
						AND `order` < '$now' ORDER BY `order` DESC LIMIT 1")->result();
			
			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE categories SET `order` = '$now' WHERE `order` = '$next' AND idsubcategory = '".(int) $idsubcategory."'");
				$this->db->query("UPDATE categories SET `order` = '$next' WHERE idcategory = '$idcategory'");
			}
		}
	}
}