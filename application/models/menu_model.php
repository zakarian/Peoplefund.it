<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Menu model
class Menu_model extends Base_Model {
 
	// Get all categories and their subcategories
	function get_menus($idsubmenu, $index = 0){
	
		// Increment level index
		$index++;
	
		if(empty($return)){
			$return = array();
		}
		
		$elements = $this->db->query("SELECT * FROM menus WHERE idsubmenu = "(int) $idsubmenu" ORDER BY `order` ASC")->result();
		
		foreach($elements AS $element){
			$element = (array) $element;
			$element['submenus'] = $this->get_menus($element['idmenu'], $index);
			$element['index'] = $index;
			$return[] = $element;
		}
		
	
		return $return;
	}
	
	// Get menu
	public function get_menu($idmenu){
		
		$menu_data = $this->db->query("SELECT * FROM menus WHERE idmenu = ".(int) $idmenu."")->result();
		$menu_data = reset($menu_data);
		return $menu_data;
	}
	
	// Get last submenu id
	public function get_last_order_id($idmenu){
		$data = $this->db->query("SELECT `order` FROM menus WHERE idsubmenu = ".(int) $idmenu." ORDER BY `order` DESC LIMIT 1")->result();
		$data = reset($data);
		$data = $data->order + 1;
		return $data;
	}
	
	// Refill orders - after category movement from one to another upper category
	public function refill_orders($idmenu){
		$data = $this->db->query("SELECT * FROM menus WHERE idsubmenu = $idmenu ORDER BY `order` ASC")->result();
		foreach($data AS $k => $row){
			$this->db->where('idmenu', $row->idmenu);
			$this->db->update('menus', array("order" => ($k + 1)));
		}
		return $data;
	}
	
	// Save menu
	public function save_menu($data, $idmenu){
		$this->db->where('idmenu', $idmenu);
		$this->db->update('menus', $data); 
	}
	
	// Add menu
	public function add_menu($data){
		$this->db->insert('menus', $data); 
	}
	
	// Move menu
	public function move_menu($idmenu, $direction){
		
		// Get current category data
		$data = $this->db->query("SELECT * FROM `menus` WHERE idmenu = $idmenu LIMIT 1")->result();
		$data = reset( $data );
		$now = $data->order;
		$idsubmenu = $data->idsubmenu;

		// Move down
		if($direction == 'down'){
			$next = $this->db->query("SELECT `order` FROM menus WHERE idsubmenu = '$idsubmenu'
									AND `order` > '$now' ORDER BY `order` ASC LIMIT 1")->result();

			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE menus SET `order` = '$now' WHERE `order` = '$next' AND idsubmenu = $idsubmenu");
				$this->db->query("UPDATE menus SET `order` = '$next' WHERE idmenu = '$idmenu'");
			}
			
		// Move up
		} else {
			$next = $this->db->query("SELECT `order` FROM menus WHERE idsubmenu = '$idsubmenu'
						AND `order` < '$now' ORDER BY `order` DESC LIMIT 1")->result();
			
			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE menus SET `order` = '$now' WHERE `order` = '$next' AND idsubmenu = '$idsubmenu'");
				$this->db->query("UPDATE menus SET `order` = '$next' WHERE idmenu = '$idmenu'");
			}
		}
	}
	
	// Delete menu
	public function delete_menu($idmenu){
		
		$elements = $this->db->query("SELECT * FROM menus WHERE idsubmenu = $idmenu")->result();

		foreach($elements AS $element){
			$submenus = $this->get_menus($element->idmenu);
			foreach($submenus AS $submenu){
				$remove[] = $submenu;
			}
			$remove[] = $element;
			
		}

		if(!empty($remove)){
			foreach($remove AS $row){	
				$row = (array) $row;
				$this->db->delete('menus', array('idmenu' => $row['idmenu'])); 
			}
		}
		
		$this->db->delete('menus', array('idmenu' => $idmenu)); 
		
		exit("ok");
	}
}