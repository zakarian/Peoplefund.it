<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

// Users model
class Users_model extends Base_Model {

	// Count users
	public function count_users($where = array()){
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Get total users
		$result = $this->db->query("SELECT * FROM `users` $where")->num_rows();

		// Return results
		return $result;
	}

    // Get all users
    public function get_users($where = array(), $limit = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);

		// Generate limit
		$limit = self::generate_limit($limit);

        // Get users
        $result = $this->db->query("SELECT * FROM `users` $where ORDER BY iduser DESC $limit");

		// Return data
		return $result->result();
    }
	
	
	// Count project owners
	public function count_project_owners($where = array()){
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Get total users
		$result = $this->db->query("SELECT u.* FROM `users` AS u LEFT JOIN projects AS p ON p.iduser = u.iduser $where GROUP BY p.iduser")->num_rows();

		// Return results
		return $result;
	}

    // Get all project owners
    public function get_project_helpers($where = array(), $limit = array()) {

		// Replace email clause
		if(!empty($where['email'])){
			$where['u.email'] = $where['email'];
			unset($where['email']);
		}
		
        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);
		
		// Add pledge status where clause
		if(empty($where)){
			$where = "WHERE pp.status = 'accepted' AND (pp.helper_hours != 0.00 OR pp.helper_text != '')";
		} else {
			$where .= " AND pp.status = 'accepted' AND (pp.helper_hours != 0.00 OR pp.helper_text != '')";
		}

        // Get users
        $result = $this->db->query("SELECT u.*, pp.idproject, p.title, p.iduser as project_iduser
									FROM `users` AS u 
									INNER JOIN projects_pledges AS pp ON pp.iduser = u.iduser 
									INNER JOIN projects AS p ON pp.idproject = p.idproject
									$where 
									GROUP BY pp.iduser 
									ORDER BY iduser DESC 
									$limit");

		// Return data
		return $result->result();
    }
	
    // Get all project owners
    public function get_project_owners($where = "", $limit = "") {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get users
        $result = $this->db->query("SELECT u.* FROM `users` AS u INNER JOIN projects AS p ON p.iduser = u.iduser $where GROUP BY p.iduser ORDER BY iduser DESC $limit");

		// Return data
		return $result->result();
    }
	
	
	// Count project backers
	public function count_project_backers($where = ""){
	
		// Replace email clause
		if(!empty($where['email'])){
			$where['u.email'] = $where['email'];
			unset($where['email']);
		}
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Add pledge status where clause
		if(empty($where)){
			$where = "WHERE pp.status = 'accepted'";
		} else {
			$where .= " AND pp.status = 'accepted'";
		}
		
		// Get total users
		$result = $this->db->query("SELECT u.* FROM `users` AS u LEFT JOIN projects_pledges AS pp ON pp.iduser = u.iduser $where GROUP BY pp.iduser")->num_rows();

		// Return results
		return $result;
	}

    // Get all backers
    public function get_project_backers($where = "", $limit = "") {

		// Replace email clause
		if(!empty($where['email'])){
			$where['u.email'] = $where['email'];
			unset($where['email']);
		}
		
        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);
		
		// Add pledge status where clause
		if(empty($where)){
			$where = "WHERE pp.status = 'accepted'";
		} else {
			$where .= " AND pp.status = 'accepted'";
		}

        // Get users
        $result = $this->db->query("SELECT u.*, pp.idproject, p.title, pp.amount as pledge_amount
									FROM `users` AS u 
									INNER JOIN projects_pledges AS pp ON pp.iduser = u.iduser 
									INNER JOIN projects AS p ON pp.idproject = p.idproject
									$where 
									GROUP BY pp.iduser 
									ORDER BY iduser DESC 
									$limit");

		// Return data
		return $result->result();
    }
	
	
	// Add new user
	public function add_user($data){
		$data['hash'] = md5(time() . rand(0, 999)); //Hash used on password recovery
		$this->db->insert('users', $data); 
	}
	
	// Save user
	public function save_user($data, $iduser){
		$this->db->where('iduser', $iduser);
		$this->db->update('users', $data); 
	}
	
	// Delete user
	public function delete_user($iduser){
		$this->db->delete('users', array('iduser' => $iduser)); 
	}
	
	// Get stats
	public function get_users_stats(){
		
		// Get stats for 1 day
		$date_start_1 = date('Y-m-d H:i:s', strtotime("-1 day"));
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' AND date_register >= '$date_start_1'")->num_rows();
		$return['1'] = $result;
		
		// Get stats for 7 day
		$date_start_7 = date('Y-m-d H:i:s', strtotime("-7 day"));
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' AND date_register >= '$date_start_7'")->num_rows();
		$return['7'] = $result;
		
		// Get stats for 30 day
		$date_start_30 = date('Y-m-d H:i:s', strtotime("-30 day"));
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' AND date_register >= '$date_start_30'")->num_rows();
		$return['30'] = $result;
		
		// Get total stats
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' ")->num_rows();
		$return['total'] = $result;
		
		// Get facebook total stats
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' AND fbid > 0")->num_rows();
		$return['fb_total'] = $result;
		
		// Get energyshare total stats
		$result = $this->db->query("SELECT * FROM `users` WHERE type='user' AND esid > 0 ")->num_rows();
		$return['es_total'] = $result;
		
		// Return results
		return $return;
	}
	
	
	// Get location data by postcode
	public function get_location_by_postcode($postcode){
	
		$data = $location = array();

		// Get the location from google.
		require_once(APPPATH.'third_party/google/GoogleMapAPI.class.php');

		// Get location data
		$gmap = new GoogleMapAPI();
		$location = $gmap->geoAddressByPostcode(str_replace(' ', '', h(st($postcode))).' UK', 1);
		
		// Check returned data
		if(empty($location)) return array();
		
		$data['town_name'] = trim(str_replace(h(st($postcode)), '', $location['town']));
		$data['county_name'] = trim(str_replace(h(st($postcode)), '', $location['county']));
		$data['location_preview'] = trim(str_replace(h(st($postcode)), '', $location['town']));
		$data['lat'] = $location['lat'];
		$data['lng'] = $location['lng'];

		// Return data
		return $data;
	}
	
	// Get count of the new member messages
	function get_new_messages_count($member_id){
		$sql = "SELECT idmessage FROM `messages` WHERE status_receiver = 'new' AND idreceiver = $member_id ";
		// Get result
		$result = $this->db->query($sql)->num_rows();

		return (int) $result;
	}
	
	// Get member's websites
	function get_websites_arr($iduser){
		$iduser = (int)$iduser;
		$sql = "SELECT websites FROM `users` WHERE iduser = $iduser LIMIT 1";
		// Get result
		$result = $this->db->query($sql)->result();
		
		$return = array();
		if (isset($result[0]->websites) && !empty($result[0]->websites)){
			$return = explode('|', $result[0]->websites);
		}

		return $return;
	}
	
	// Set member's websites
	function save_websites_arr($websites_arr, $iduser){
		$iduser = (int)$iduser;
		if (!empty($websites_arr)){
			$websites_sql = join('|', $websites_arr);
		}else{
			$websites_sql = '';
		}
		$sql = "UPDATE `users` SET websites = '$websites_sql' WHERE iduser = $iduser LIMIT 1";
		$this->db->query($sql);
	}
	
	// Add a member website
	function add_website($website, $iduser){
		$websites_arr = $this->get_websites_arr($iduser);
		
		if (!in_array($website,$websites_arr)){
			$websites_arr[] = $website;
		}
		
		// Save
		$this->save_websites_arr($websites_arr, $iduser);
	}
	
	// Remove website
	function delete_website($website, $iduser){
		$websites_arr = $this->get_websites_arr($iduser);
		
		if (in_array($website,$websites_arr)){
			foreach ($websites_arr as $i => $website_db){
				if ($website == $website_db){
					unset($websites_arr[$i]);
				}
			}
		}
		
		// Save
		$this->save_websites_arr($websites_arr, $iduser);
	}
	
	// Get the member facebook picture
	function get_user_fb_pic($pic_url, $iduser){
		$ext = 'jpg'; //Facebook converts to JPG
		
		// Get the member image
		$pic_contents = file_get_contents($pic_url);
		$path = SITE_DIR."public/uploads/users/$iduser." . $ext;
		if (file_exists($path)){
			unlink($path);
		}
		// Store the member image
		file_put_contents($path, $pic_contents);
		chmod($path, 0777);
		
		// Make the thumb
		make_user_thumb($path);
		
		return $ext;
	}
	
	// Get the member energyshare picture
	function get_user_es_pic($pic_url, $iduser){
		$ext = 'jpg'; //Facebook converts to JPG
		
		// Get the member image
		$pic_contents = file_get_contents($pic_url);
		$path = SITE_DIR."public/uploads/users/$iduser." . $ext;
		if (file_exists($path)){
			unlink($path);
		}
		// Store the member image
		file_put_contents($path, $pic_contents);
		chmod($path, 0777);
		
		make_user_thumb($path);
		
		return $ext;
	}
	
	// Get user wall items
	function get_wall_events($iduser, $limit){

		// Generate limit
		$limit = self::generate_limit($limit);
		$pProjectStatusHaving = "HAVING project_status = 'open'";
		
		// Hide non approved projects from not owner
		if(isset($_SESSION['user']['iduser']) && $_SESSION['user']['iduser'] == $iduser) {
			$pProjectStatusHaving =  "";
		} else {
			$pProjectStatusHaving =  "AND p.approved = 1 " . $pProjectStatusHaving;
		}
			
		// Form the query
		$query = "
			SELECT res.*
			FROM (
					-- ### UPDATES COMMENTS ### --
					SELECT * FROM(
						SELECT uc.text, uc.date_added, p.title, p.idproject, p.slug, p.ext, p.location_preview, p.county_name, p.status AS project_status, 'update_comment' AS type
						-- , ca.title AS category_title, ca.slug AS category_slug
						FROM projects_updates_comments AS uc
						LEFT JOIN projects_updates AS u ON u.idupdate = uc.idupdate
						LEFT JOIN projects AS p ON p.idproject = u.idproject
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						WHERE uc.iduser = $iduser AND p.idproject > 0
						$pProjectStatusHaving
					) AS t1 
					
					UNION
					
					-- ### COMMENTS ### --
					SELECT * FROM(
						SELECT c.text, c.date_added, p.title, p.idproject, p.slug, p.ext, p.location_preview, p.county_name, p.status AS project_status, 'comment' AS type
						-- , ca.title AS category_title, ca.slug AS category_slug
						FROM projects_comments AS c
						LEFT JOIN projects AS p ON p.idproject = c.idproject
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						WHERE c.iduser = $iduser AND p.idproject > 0
						$pProjectStatusHaving
					) AS t2
					
					UNION
					
					-- ### START PROJECT ### --
					SELECT * FROM(
						SELECT p.outcome AS text, p.date_created AS date_added, p.title, p.idproject, p.slug, p.ext, p.location_preview, p.county_name, p.status AS project_status, 'new_project' AS type
						-- , ca.title AS category_title, ca.slug AS category_slug
						FROM projects AS p
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						WHERE p.iduser = $iduser AND p.idproject > 0 
						$pProjectStatusHaving
					) AS t3
					
					UNION
					
					-- ### BACK PROJECT WITH MONEY ## --
					SELECT * FROM(
						SELECT p.outcome AS text, p.date_created AS date_added, p.title, p.idproject, p.slug, p.ext, p.location_preview, p.county_name, p.status AS project_status, 'back_amount' AS type
						-- , ca.title AS category_title, ca.slug AS category_slug
						FROM projects_pledges AS pp
						LEFT JOIN projects AS p ON p.idproject = pp.idproject
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						WHERE pp.iduser = $iduser AND pp.status = 'accepted' AND p.idproject > 0
						$pProjectStatusHaving
					) AS t4
					
					UNION
					
					-- ### ADD PROJECT UPDATE ## --
					SELECT * FROM(
						SELECT pu.text, p.date_created AS date_added, p.title, p.idproject, p.slug, p.ext, p.location_preview, p.county_name, p.status AS project_status, 'new_update' AS type
						-- , ca.title AS category_title, ca.slug AS category_slug
						FROM projects_updates AS pu
						LEFT JOIN projects AS p ON p.idproject = pu.idproject
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						WHERE p.iduser = $iduser AND p.idproject > 0
						$pProjectStatusHaving
					) AS t4
					
			) AS res
			ORDER BY res.date_added DESC
			$limit
		";
		
		// Get result
		$result = $this->db->query($query)->result();
		
		// Get categories
		foreach($result AS $k => &$v){
			$v->categories = $this->db->query("SELECT c.* 
									FROM projects_categories AS pc 
									LEFT JOIN categories AS c ON pc.idcategory = c.idcategory
									WHERE pc.idproject = '".$v->idproject."' LIMIT 50")->result();
		}

		// Get all events
		return $result;
	}
}