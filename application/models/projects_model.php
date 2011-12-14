<?php
require_once(APPPATH.'core/base_model.php');

class Projects_model extends Base_Model {

	var $variable;
	var $owner 			= FALSE;
	var $where 			= NULL;
	var $order 			= NULL;
	var $limit 			= NULL;
	var $offset 		= NULL;
	var $join		 	= NULL;
	var $reservedIds	= array();
	var $filter_type	= '';
	var $searchSql		= array();
	
	/* PUBLIC VALIDATION */
	public function _validation(&$data) {
	
		$error = array();
		
		$data['slug'] = slugify($data['slug']);
	
		if(
			!isset($data['title']) OR 
			(isset($data['title']) && !trim($data['title']))
		) $error['title'] = 'Title can\'t be empty';
		
		if(	
			!isset($data['slug']) OR 
			(isset($data['slug']) && !trim($data['slug']))
		) $error['slug'] = 'Please enter project vanity url';
		else {
			$reservedSlug = $this->get_projects(array('p.slug' => $data['slug']));
			if($reservedSlug) $error['slug'] = 'This slug is already taken, please enter another one';
		}
		
		if(
			!isset($data['categories']) OR 
			(isset($data['categories']) && count($data['categories']) == 0)
		) $error['categories'] = 'You should select at least 1 category';
		
		if(	
			!isset($data['websites']) OR 
			(isset($data['websites']) && !$data['websites'])
		) $data['websites'] = array();
		
		if(
			!isset($data['outcome']) OR 
			(isset($data['outcome']) && !trim($data['outcome']))
		) $error['outcome'] = 'Please enter outcome';
		
		if(
			!isset($data['about']) OR 
			(isset($data['about']) && !trim($data['about']))
		) $error['about'] = 'Please enter project information by fillint the about field';	
		
		if(
			!isset($data['period']) OR 
			(isset($data['period']) && !is_numeric($data['period']))
		) $error['period'] = 'Please enter valid period';
		else $data['period'] = (intval($data['period']) * 7);
		
		if(
			!isset($data['amount']) OR 
			(isset($data['amount']) && !is_numeric($data['amount'])) OR 
			(isset($data['amount']) && ((int)$data['amount'] < 1000 OR (int)$data['amount'] > 50000))
		) $error['amount'] = 'Please enter funding target between &pound;1000 and &pound;50,000';
		
		if(
			isset($data['time']) && 
			$data['time']
		) {
			if(strlen($data['time']) > 4)
				$error['time'] = 'Time field you entered has characters that are not allowed – you can only enter numbers';
			else if(!is_numeric($data['time']))
				$error['time'] = 'Time field you entered has characters that are not allowed – you can only enter numbers';
		} else $data['time'] = 0;
			
		if(
			isset($data['skills']) && 
			is_array($data['skills']) && 
			count($data['skills'])
		) {
			foreach($data['skills'] as $k=>$skill)
				if(strlen($skill) > 30)
					$error['skills'] = 'The skills you entered contains over 30 characters';
				else
					$data['skills'][$k] = h(st($data['skills'][$k]));
		} else $data['skills'] = array();
		
		if(
			!isset($data['postcode']) OR 
			(isset($data['postcode']) && !$data['postcode'])
		) $error['postcode'] = 'Please enter postcode';
		else {
			$this->load->model('Users_model', 'users');
			$data['location_data'] = $this->users->get_location_by_postcode($data['postcode']);
			if(empty($data['location_data'])) $error['postcode'] = 'Invalid postcode';
		}
		
		foreach($data['amounts'] AS $k => $amount){
			if(
				!isset($data['amounts_descriptions'][$k]) OR 
				(isset($data['amounts_descriptions'][$k]) && !$data['amounts_descriptions'][$k])
			) $error['empty_description'] = 'One of the rewards has empty description';
				
			if(
				!isset($data['amounts'][$k]) OR 
				(isset($data['amounts'][$k]) && ((int)$data['amounts'][$k] < 0 OR (int)$data['amounts'][$k] > 50000))
			) $error['empty_amount'] = 'One of the reward amounts you entered has characters that are not allowed – you can only enter numbers between &pound;1000 and &pound;50,000';
				
			if(
				isset($data['amounts_limited'][$k]) &&
				$data['amounts_limited'][$k] == 'yes'
			)
				if(
					!isset($data['amounts_numbers'][$k]) OR 
					(isset($data['amounts_numbers'][$k]) && 
					!is_numeric($data['amounts_numbers'][$k]))
				) $error['empty_numbers'] = 'One of the rewards is limited and has empty number field';
				
			$data['amounts_descriptions'][$k] = h(st($data['amounts_descriptions'][$k]));		
		}
		
		if(
			isset($_SESSION['vzaar_idvideo']) &&
			$_SESSION['vzaar_idvideo']
		) $data['vzaar_idvideo'] = $_SESSION['vzaar_idvideo'];
		else $data['vzaar_idvideo'] = 0;
		
		if(
			isset($_SESSION['add_project']['embed']) &&
			$_SESSION['add_project']['embed']
		) $data['add_project']['embed'] = $_SESSION['add_project']['embed'];
		else $data['add_project']['embed'] = '';
		
		if(
			isset($_SESSION['add_project']['ext']) &&
			$_SESSION['add_project']['ext']
		) $data['add_project']['ext'] = $_SESSION['add_project']['ext'];
		else $data['add_project']['ext'] = '';
		
		if(!$data['vzaar_idvideo'] && !$data['add_project']['embed'])
			$error['no_video'] = 'Please link to or upload a short video pitching your project.';
		
		if(
			isset($data['pledge_more']) &&
			$data['pledge_more']
		) $data['pledge_more'] = 1;
		else $data['pledge_more'] = 0;	
		
		if(
			isset($data['helpers']) &&
			$data['helpers']
		) $data['helpers'] = 1;
		else $data['helpers'] = 0;	
			
		$data['title'] = h(st($data['title']));
		$data['postcode'] = h(st($data['postcode']));
		$data['slug'] = h(st($data['slug']));
		$data['amount'] = (float)(h(st($data['amount'])));
		$data['outcome'] = h(st($data['outcome']));
		$data['time'] = (int)$data['time'];
		$data['skills'] = serialize($data['skills']);
		$data['date_created'] = date('Y-m-d H:i:s');
		$data['hostname'] = $_SERVER['REMOTE_ADDR'];
		
		return $error;
	}
	
	/* PUBLIC FUNCTIONS */
	public function _query() {
	
		$this->where 		= ($this->where) 		? 'WHERE  	'.$this->where : '';
		$this->order 		= ($this->order) 		? 'ORDER BY '.$this->order : '';
		$this->limit 		= ($this->limit) 		? 'LIMIT  	'.(int)$this->limit 	: '';
		$this->offset 		= ($this->offset) 		? 'OFFSET 	'.(int)$this->offset 	: '';
		$this->join 		= ($this->join) 		? $this->join 				: '';
		
		$result = $this->db->query('
			SELECT p.*, u.`username`, u.`slug` AS user_slug, u.`bio` AS user_bio, u.`ext` AS user_ext,
				(
					SELECT COUNT(`id`) AS cnt 
					FROM `notifications_settings` 
					WHERE 
						`object_type` 		= "project"
						AND `object_role`	= "watch"
						AND `object_id` 	= p.idproject
				) AS watched,
				IF(
					DATEDIFF(DATE_ADD(p.`date_created`, INTERVAL + p.`period` DAY), CURDATE()) < 1, 
					0, DATEDIFF(DATE_ADD(`date_created`, INTERVAL + p.`period` DAY), CURDATE())
				) AS days_left,
				DATE_FORMAT(DATE_ADD(p.`date_created`, INTERVAL + p.`period` DAY), "%D %M %Y") AS deadline,
				CAST((p.`amount_pledged` / p.`amount`) * 100 AS UNSIGNED) AS pledged_percent,
				CAST((
					(p.`period` - (
						IF(
							DATEDIFF(DATE_ADD(p.`date_created`, INTERVAL + p.`period` DAY), CURDATE()) < 1, 
							0, DATEDIFF(DATE_ADD(p.`date_created`, INTERVAL + p.`period` DAY), CURDATE())
						))
					) / p.`period`
				) * 100 AS UNSIGNED) AS pledged_days
				'.( isset($this->lat) && isset($this->lng) ? ', '.$this->get_distance($this->lat, $this->lng, 'p.lat', 'p.lng').' as distance' : '' ).'
			FROM `projects` AS p
			INNER JOIN `users` AS u ON u.`iduser` = p.`iduser`
			'.$this->join.'
			'.$this->where.'
			'.$this->order.'
			'.$this->limit.'
			'.$this->offset.'
		')->result();
		foreach($result as $k => &$v)
			$v->categories = $this->get_project_categories($v->idproject);
		return $result;
		
	}
	
 	function get_distance($lat, $lon, $latField, $lonField){
		// in miles
  		return '3958.56540635 * (acos(sin((0.01745329 * '.$lat.')) * sin(0.01745329 * '.$latField.') + cos(0.01745329 * '.$lat.') * cos(0.01745329 * '.$latField.') * cos(0.01745329 * ('.$lon.' - '.$lonField.'))))';  
	}

    public function get_public_watching_projects($userid) {
		$result = $this->db->query('
			SELECT DISTINCT(n.object_id)
			FROM `notifications_settings` AS n
			WHERE n.member_id = "'.(int)$userid.'" AND n.object_type = "project" AND n.object_role = "watch"
		')->result();
		if(!$result) return FALSE;
		
		$appendWhereList = array();
		foreach($result as $object) {	
			$appendWhereList[] = ' p.idproject = "'.$object->object_id.'" ';
		}
		$appendWhere = implode(' OR ', $appendWhereList);
		$appendWhere = ' ('.implode(' OR ', $appendWhereList).') AND ';
		$this->where = $appendWhere.'p.status = "open" AND p.active = 1 GROUP BY p.idproject HAVING days_left > 0';
		$this->order = 'p.date_created DESC';
		
		// Return query
		return $this->_query();
    }
	
	public function get_public_backed_projects($userid) {
		$this->where = 'pp.iduser = "'.$userid.'" AND pp.status = "accepted" AND p.status = "open" AND p.active = 1 GROUP BY p.idproject';
		$this->where = (!$this->owner) ? $this->where.' AND p.approved = 1 HAVING days_left > 0 ' : $this->where;
		$this->order = 'p.date_created DESC';
		$this->join = 'LEFT JOIN projects_pledges AS pp ON p.idproject = pp.idproject';
		
		// Return query
		return $this->_query();
	}

	public function get_public_started_projects($userid) {
		$appendWhere = (!$this->owner) ? 'p.status = "open" AND ' : '';
		$this->where = $appendWhere.'p.iduser = '.$userid.' AND p.active = 1';
		$this->where = (!$this->owner) ? $this->where.' AND p.approved = 1 GROUP BY p.idproject HAVING days_left > 0 ' : $this->where.' GROUP BY p.idproject ';
		$this->order = 'p.date_created DESC';
		
		// Return query
		return $this->_query();
	}
	 
	public function public_search() {

		$this->where = '';
		
		// Set order clause
		if($this->filter_type == 'latest')
			$this->order = 'p.date_created DESC';
		else if($this->filter_type == 'picks') {
			$this->where .= ' p.editors_pick = "1" AND ';
			$this->order = 'p.order DESC, p.date_created DESC';
		} else if($this->filter_type == 'liked')
			$this->order = 'watched DESC';
		else if($this->filter_type == 'funded')
			$this->order = 'pledged_percent DESC, p.`amount_pledged` DESC';
		else if($this->filter_type == 'ending_soon')
			$this->order = 'days_left ASC';
			
		if(count($this->searchSql))
			foreach($this->searchSql as $key=>$value) {
				if($key == 'keywords' && $value <> 'empty') 
					$this->where .= ' (
						p.title LIKE "%%'.mysql_real_escape_string($value).'%%" OR
						p.slug LIKE "%%'.mysql_real_escape_string($value).'%%" OR
						p.outcome LIKE "%%'.mysql_real_escape_string($value).'%%" OR
						p.about LIKE "%%'.mysql_real_escape_string($value).'%%"
					) AND '; 
				if($key == 'category') {
					$categories = $idcategories = array();
					$categories = explode(',', $value);
					if(count($categories)) 
						foreach($categories as $k => $item) {
							$idcategories[$k] = $this->db->query('
								SELECT idcategory 
								FROM categories 
								WHERE title = "'.$item.'" 
								LIMIT 1
							')->row();
							if($idcategories[$k])
								$idcategories[$k] = 'pc.idcategory = '.(int) $idcategories[$k]->idcategory;
							else
								unset($idcategories[$k]);
						} 
					if(count($idcategories) > 0)	
						$this->where .= ' (SELECT COUNT(*)
							FROM projects_categories AS pc
							WHERE pc.idproject = p.idproject
							AND ( '.implode(' OR ', $idcategories).' )
						) AND ';
				}
				// if($key == 'string') 
					// $this->where .= ' (
						// p.town_name LIKE "%'.$value.'%" OR 
						// p.location_preview LIKE "%'.$value.'%" OR 
						// p.postcode = "%'.$value.'%" 
					// ) AND ';
			}
					
		$this->where .= 'p.status = "open" AND p.active = 1 AND p.approved = 1 HAVING days_left > 0';
		
		// Return query
		return $this->_query();
	}
	
    public function get_picks_projects_home() {
		// Generate where clause
		$this->where = remove_shown_projects($this->reservedIds).'p.status = "open" AND p.active = 1 AND p.approved = 1 AND p.editors_pick = "1" HAVING days_left > 0';
		// Set order clause
		$this->order = 'p.order DESC, p.date_created DESC';
		
		// Return query
		return $this->_query();
    }
	
    public function get_liked_projects_home() {
		// Generate where clause
		$this->where = remove_shown_projects($this->reservedIds).'p.status = "open" AND p.active = 1 AND p.approved = 1 HAVING days_left > 0';
		// Set order clause
		$this->order = 'watched DESC';
		
		// Return query
		return $this->_query();
    }

    public function get_funded_projects_home() {
		// Generate where clause
		$this->where = remove_shown_projects($this->reservedIds).'p.status = "open" AND p.active = 1 AND p.approved = 1 HAVING days_left > 0';
		// Set order clause
		$this->order = 'pledged_percent DESC, p.`amount_pledged` DESC';
		
		// Return query
		return $this->_query();
    }
	
    public function get_recent_projects_home() {
		// Generate where clause
		$this->where = remove_shown_projects($this->reservedIds).'p.status = "open" AND p.active = 1 AND p.approved = 1 HAVING days_left > 0';
		// Set order clause
		$this->order = 'p.date_created DESC';
		
		// Return query
		return $this->_query();
    }
	
    public function get_ending_projects_home() {
		// Generate where clause
		$this->where = remove_shown_projects($this->reservedIds).'p.status = "open" AND p.active = 1 AND p.approved = 1 HAVING days_left > 0';
		// Set order clause
		$this->order = 'days_left ASC';
		
		// Return query
		return $this->_query();
    }
	
    public function get_one_projects($id = NULL, $slug = NULL, $user_id = 0) {
		// Generate where clause
		$where = '';
		if($id) $where .= 'p.idproject = '.(int)$id;
		else if($slug) $where .= 'p.slug = "'.mysql_real_escape_string($slug).'"';
		if(!$user_id) $where .= ' AND (p.status = "open" OR p.status = "closed")';
		else $where .= ' AND p.iduser = '.$user_id.'';
		$this->where = $where.' AND p.active = 1 '.((!$user_id) ? 'AND p.approved = 1 ' : '').''; // HAVING days_left > 0

		// Set order clause
		$this->order = 'p.date_created DESC';
		
		// Return query
		return $this->_query();
    }
	
    public function get_liked_2_liked_projects_home($id) {
		$result = $this->db->query('
			SELECT DISTINCT(n.member_id)
			FROM `notifications_settings` AS n
			WHERE n.object_id = "'.$id.'" AND n.object_type = "project" AND n.object_role = "watch"
		')->result();
		if(!$result) return FALSE;
		
		$appendWhereList = array();
		foreach($result as $member) {	
			$appendWhereList[] = ' n.member_id = "'.$member->member_id.'" ';
		}
		$appendWhere = implode(' OR ', $appendWhereList);
		
		$result = $this->db->query('
			SELECT DISTINCT(n.object_id)
			FROM `notifications_settings` AS n
			WHERE ('.$appendWhere.') AND n.object_type = "project" AND n.object_role = "watch" AND n.object_id != "'.$id.'"
			ORDER BY RAND()
		')->result();
		if(!$result) return FALSE;
		
		$appendWhereList = array();
		foreach($result as $project) {	
			$appendWhereList[] = ' p.idproject = "'.$project->object_id.'" ';
		}
		
		$appendWhere = ' ('.implode(' OR ', $appendWhereList).') AND ';
		$this->where = @$appendWhere.'p.status = "open" AND p.active = 1 HAVING days_left > 0';
		$this->order = 'RAND()';
		return $this->_query();
    }
	
    public function get_project_amount($array) {
		
		// Generate where clause
		$where = self::where_string_from_array($array);
		
		$amounts = $this->db->query('	
			SELECT *
			FROM projects_amounts '.$where.'
			ORDER BY amount ASC
		')->result();

		// Return data
		return $amounts;
    }
	
    public function get_project_amounts($array) {
		if(isset($array['idproject']) && $array['idproject']) {
			$amount = '';
			if(isset($array['idamount']) && $array['idamount']) {
				$amount = ' AND pa.idamount = '.$array['idamount'];
			}
			$id = $array['idproject'];
			$amounts = $this->db->query('	
				SELECT pa.*,
					(
						SELECT COUNT(idpledge) AS cnt 
						FROM projects_pledges
						WHERE 
							idamount = pa.idamount
							AND idproject  = pa.idproject
							AND status = "accepted"
					) AS pledges,
					(pa.number - (
						SELECT COUNT(idpledge) AS cnt 
						FROM projects_pledges
						WHERE 
							idamount = pa.idamount
							AND idproject  = pa.idproject
							AND status = "accepted"
					)) AS remaining
				FROM projects_amounts AS pa
				WHERE pa.idproject = "'.$id.'" '.$amount.'
				ORDER BY pa.amount ASC
			')->result();
		} else {
			// Generate where clause
			$where = self::where_string_from_array($array);

			$amounts = $this->db->query('	
				SELECT *
				FROM projects_amounts '.$where.'
				ORDER BY amount ASC
			')->result();
		}
		return $amounts;
    }
	
	public function public_search_postcode_2_string($string){

		$data = $location = array();

		// Remove empty spaces
		$string = h(st($string));
		$string = str_replace(' ', '', $string);

		// Get the location from google.
		// Get location data
		require_once(APPPATH.'third_party/google/GoogleMapAPI.class.php');
		$gmap = new GoogleMapAPI();
		$location = $gmap->geoAddressByPostcode($string.' UK', 1);
		
		$data['town_name'] = $location['town'];
		$data['county_name'] = $location['county'];
		$data['location_preview'] = $location['town'];
		$data['lat'] = $location['lat'];
		$data['lng'] = $location['lng'];
		
		// Return data
		return $data;
	}
	
	/* PUBLIC FUNCTIONS */
	
	// Get all videos to check
    public function get_projects_to_check_videos($limit = array()) {

		// Generate limit
		$limit = self::generate_limit($limit);
		
        // Get projects
        $result = $this->db->query("SELECT p.*, u.username 
									FROM `projects` AS p
									LEFT JOIN users AS u ON p.iduser = u.iduser
									WHERE p.vzaar_idvideo != '0'
									AND p.vzaar_processed = '0'
									$limit");
		
		// Return data
		return $result->result();
    }
	
	// Add project
	public function add_project($data){
		$this->db->insert('projects', $data); 
		$idobject = $this->db->insert_id();
		
		//Register all event types for the owner of the project
		$member_id = @$_SESSION['user']['iduser'];
		$role = 'own';
		$object_type = 'project';
		$notification_type = @$_SESSION['user']['alerts_own']; //Type of notifications where user is owner of the object
		$CI =& get_instance();
		$CI->load->model('Notifications_model', 'notifications', TRUE);
		$CI->notifications->configure_event_for_member($member_id, $idobject, $role, $object_type, 'comment', $notification_type);
		$CI->notifications->configure_event_for_member($member_id, $idobject, $role, $object_type, 'update', $notification_type);
		$CI->notifications->configure_event_for_member($member_id, $idobject, $role, $object_type, 'status_change', $notification_type); 
		return $idobject;
	}
	
	// Save project
	public function save_project($data, $idproject){
		
		if ((int) $idproject > 0 && isset($data['status']) && !empty($data['status'])){
			//Check what event is going to be occur and trigger it
			$old_data = (array) reset($this->get_projects(array("idproject" => $idproject)));
		
			//Trigger notification for a new event
			if ($old_data['status'] != $data['status']){
				// Notification will be sent only when project is closed, not when created
				/* 
				// We don't need this notificatin anymore
				if ($data['status'] !== 'open') {
					$CI =& get_instance();
					$CI->load->model('Notifications_model', 'notifications', TRUE);
					$object_type = 'project';
					$object_id = $idproject;
					$event_type = 'status_change '.$data['event_type'];
					$from_member_id = NULL;
					$CI->notifications->trigger_event($object_type, $object_id, $event_type, $from_member_id);
				} */
			}

			if(isset($data['event_type'])) unset($data['event_type']);
		}
		
		$this->db->where('idproject', $idproject);
		$this->db->update('projects', $data); 
	}

	// Delete project
	public function delete_project($idproject){
		$this->db->delete('projects', array('idproject' => $idproject)); 
		
		//Remove all notification settings for this project
		$this->load->model('Notifications_model', 'notifications');
		$this->notifications->remove_all_events_for_object('project', $idproject);		
	}
	
	// Get stats
	public function get_projects_stats(){
		
		// Get stats for 1 day
		$date_start_1 = date('Y-m-d H:i:s', strtotime("-1 day"));
		$result = $this->db->query("SELECT * FROM `projects` WHERE date_created >= '$date_start_1'")->num_rows();
		$return['1'] = $result;
		
		// Get stats for 7 day
		$date_start_7 = date('Y-m-d H:i:s', strtotime("-7 day"));
		$result = $this->db->query("SELECT * FROM `projects` WHERE date_created >= '$date_start_7'")->num_rows();
		$return['7'] = $result;
		
		// Get stats for 30 day
		$date_start_30 = date('Y-m-d H:i:s', strtotime("-30 day"));
		$result = $this->db->query("SELECT * FROM `projects` WHERE date_created >= '$date_start_30'")->num_rows();
		$return['30'] = $result;
		
		// Get total stats
		$result = $this->db->query("SELECT * FROM `projects`")->num_rows();
		$return['total'] = $result;
		
		// Return results
		return $return;
	}
	
	// Add project donation amounts
	public function add_project_amount($idproject, $data){
		
		// Insert array
		$data = array(
			"idproject"		=>	$idproject,
			"amount"		=>	$data['amount'],
			"description"	=>	$data['text'],
			"number"		=>	($data['number'] ? (int) $data['number'] : 0),
			"limited"		=>	$data['limited']
		);
		
		// Insert donation option
		$this->db->insert('projects_amounts', $data); 
	}
	
	// Save project amount
	public function save_project_amount($data, $idamount){
		$this->db->where('idamount', $idamount);
		$this->db->update('projects_amounts', $data); 
	}
	
	// Delete project amount
	public function delete_project_amount($idamount){
		$this->db->delete('projects_amounts', array('idamount' => $idamount)); 
	}
	
	// Delete project amounts
	public function delete_project_amounts($idproject){
		$this->db->delete('projects_amounts', array('idproject' => $idproject)); 
	}
	
	
	
	// Add pledge
	public function add_pledge($data){
		$this->db->insert('projects_pledges', $data); 
	}
	
	// Save pledge
	public function save_pledge($data, $where){
		foreach($where AS $k => $v){
			$this->db->where($k, $v);
		}
		$this->db->update('projects_pledges', $data); 
	}
	
	// Update project pledged amount
	public function update_pledged_amount($key){
	
		// Get pledge details
        $result = $this->db->query("SELECT * FROM projects_pledges WHERE `key` = '$key' LIMIT 1")->result();
		$result = reset($result);
		
		// Update project pledged amount
		$this->db->query("UPDATE projects SET amount_pledged = amount_pledged + '".$result->amount."' WHERE `idproject` = '".$result->idproject."' LIMIT 1");
	}
	
	
	// Get projects that are expiring
	public function get_expired_projects(){
		
		// Get projects
		return $this->db->query("SELECT p.*, u.email
								FROM projects AS p
								INNER JOIN users AS u ON p.iduser = u.iduser
								WHERE DATE_ADD(p.date_created, INTERVAL p.period DAY) <= NOW()
								AND p.status = 'open'
								")->result(); // 
	}
	
	// Count pledges
    public function count_project_pledges($where = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);

        // Get projects pledges
        $result = $this->db->query("SELECT COUNT(*) 
									FROM projects_pledges AS pl
									LEFT JOIN projects AS p ON pl.idproject = p.idproject 
									$where");
		
		// Return data
		return $result->result();
    }
	
	// Get pledges
    public function get_project_pledges($where = array(), $limit = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get projects pledges
        $result = $this->db->query("SELECT pl.*, p.title, u.username, u.ext AS user_ext
									FROM projects_pledges AS pl
									INNER JOIN projects AS p ON pl.idproject = p.idproject
									LEFT JOIN users AS u ON pl.iduser = u.iduser
									$where ORDER BY pl.date_added DESC $limit
									");
		
		// Return data
		return $result->result();
    }
	
	

	// Get pledgers
    public function get_project_pledgers($where = array(), $limit = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get projects pledges
        $result = $this->db->query("SELECT pl.*, pa.description as pledge_description, p.title, u.username, u.slug AS user_slug, u.location_preview, u.ext AS user_ext, pl.thanked, pl.reward_sent
									FROM projects_pledges AS pl
									INNER JOIN projects_amounts AS pa ON pa.idamount = pl.idamount
									LEFT JOIN projects AS p ON pl.idproject = p.idproject
									LEFT JOIN users AS u ON pl.iduser = u.iduser
									$where 
									-- GROUP BY pl.iduser
									ORDER BY pl.idpledge DESC
									$limit");
		
		// Return data
		return $result->result();
    }
	
	// Get pledges stats
	public function get_pledges_stats(){
	
		// Get stats for 1 day
		$date_start_1 = date('Y-m-d H:i:s', strtotime("-1 day"));
		$result = $this->db->query("SELECT SUM(amount) AS `projects_amount`, COUNT(*) AS `projects_count`, status FROM `projects_pledges` WHERE date_added >= '$date_start_1' AND status = 'transferred' GROUP BY status")->result();
		$return['1'] = reset($result);
		
		// Get stats for 7 day
		$date_start_7 = date('Y-m-d H:i:s', strtotime("-7 day"));
		$result = $this->db->query("SELECT SUM(amount) AS `projects_amount`, COUNT(*) AS `projects_count`, status FROM `projects_pledges` WHERE date_added >= '$date_start_7' AND status = 'transferred' GROUP BY status")->result();
		$return['7'] = reset($result);
		
		// Get stats for 30 day
		$date_start_30 = date('Y-m-d H:i:s', strtotime("-30 day"));
		$result = $this->db->query("SELECT SUM(amount) AS `projects_amount`, COUNT(*) AS `projects_count`, status FROM `projects_pledges` WHERE date_added >= '$date_start_30' AND status = 'transferred' GROUP BY status")->result();
		$return['30'] = reset($result);
		
		// Get total stats
		$result = $this->db->query("SELECT SUM(amount) AS `projects_amount`, COUNT(*) AS `projects_count`, status FROM `projects_pledges` WHERE status = 'transferred' GROUP BY status")->result();
		$return['total'] = reset($result);

		// Return results
		return $return;
	}
	
	// Get project pledges stats
	public function get_project_pledges_stats($idproject) {
		// Get total stats
		$result = $this->db->query("SELECT SUM(amount) AS `projects_amount`, COUNT(*) AS `projects_count`, status FROM `projects_pledges` WHERE idproject = '".(int)$idproject."' GROUP BY status")->result();
		$return = $result;

		// Return results
		return $return;
	}
	
	// Add project comment
	public function add_comment($data){
		$this->db->insert('projects_comments', $data); 
		$comment_id = $this->db->insert_id();
		
		$CI =& get_instance();
		$CI->load->model('Notifications_model', 'notifications', TRUE);
		$object_type = 'project';
		$object_id = $data['idproject'];
		$event_type = 'comment';
		$from_member_id = $data['iduser'];
		$object_secondary_id = $comment_id;
		$object_text = $data['text'];
		$CI->notifications->trigger_event($object_type, $object_id, $event_type, $from_member_id, $object_secondary_id, $object_text);				
	}
	
	// Add update comment
	public function add_update_comment($data){
		$this->db->insert('projects_updates_comments', $data); 
		$comment_id = $this->db->insert_id();
		
		$CI =& get_instance();
		$CI->load->model('Notifications_model', 'notifications', TRUE);
		$object_type = 'update';
		$object_id = $data['idupdate'];
		$event_type = 'comment';
		$from_member_id = $data['iduser'];
		$object_secondary_id = $comment_id;
		$object_text = $data['text'];
		$CI->notifications->trigger_event($object_type, $object_id, $event_type, $from_member_id, $object_secondary_id, $object_text);		
	}

	// Count comments
    public function count_comments($where = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);

        // Get projects comments
        $result = $this->db->query("SELECT COUNT(*)
									FROM projects_comments AS pc
									INNER JOIN projects AS p ON p.idproject = pc.idproject
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where");
		
		// Return data
		return $result->result();
    }
	
	// Get comments
    public function get_comments($where = array(), $limit = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);
		
		// Generate limit
		$limit = self::generate_limit($limit);

        // Get projects comments
        $result = $this->db->query("SELECT pc.*, p.slug, p.title AS project_title, u.username
									FROM projects_comments AS pc
									INNER JOIN projects AS p ON p.idproject = pc.idproject
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where 
									ORDER BY idcomment DESC
									$limit");
		
		// Return data
		return $result->result();
    }
	
	// Get all comments - project comments and updates comments
    public function get_all_comments($idproject = 0, $limit = array()) {

        // Generate limit
		$limit = self::generate_limit($limit);
		
		// Form the query
		$query = "
			SELECT res.*
			FROM (
					-- ### UPDATES COMMENTS ### --
					SELECT * FROM(
						SELECT u.text, u.title, u.date_added, us.username, us.slug AS user_slug, us.ext AS user_ext, us.iduser, u.idupdate, us.location_preview, 'update' AS type
						FROM projects_updates AS u
						LEFT JOIN projects AS p ON u.idproject = p.idproject
						LEFT JOIN users AS us ON us.iduser = p.iduser
						WHERE u.idproject = $idproject
					) AS t1
					
					UNION
					
					-- ### COMMENTS ### --
					SELECT * FROM(
						SELECT c.text, NULL as title, c.date_added, u.username, u.slug AS user_slug, u.ext AS user_ext, u.iduser, NULL AS idupdate, u.location_preview, 'comment' AS type
						FROM projects_comments AS c
						LEFT JOIN projects AS p ON p.idproject = c.idproject
						-- LEFT JOIN categories AS ca ON p.idcategory = ca.idcategory
						LEFT JOIN users AS u ON c.iduser = u.iduser
						WHERE c.idproject = $idproject
					) AS t2
			) AS res
			ORDER BY res.date_added DESC
			$limit
		";

		// Get all comments and updates
		$results = $this->db->query($query)->result();
		
		// Check for comments for every update
		foreach($results AS &$result){
		
			// We need only updates
			if($result->type != "update") continue;
	
			$result->comments = $this->db->query("SELECT uc.text, u.ext AS user_ext, u.iduser, uc.date_added, u.username, u.slug AS user_slug, uc.idupdate
													FROM projects_updates_comments AS uc
													LEFT JOIN projects_updates AS up ON uc.idupdate = up.idupdate
													LEFT JOIN projects AS p ON up.idproject = p.idproject
													LEFT JOIN users AS u ON uc.iduser = u.iduser
													WHERE uc.idupdate = '".$result->idupdate."'")->result();
		}

		// Return data
		return $results;
    }
	
	// Save comment
	public function save_comment($data, $idcomment){
		$this->db->where('idcomment', $idcomment);
		$this->db->update('projects_comments', $data); 
	}
	
	// Delete comment
	public function delete_comment($idcomment){
		$this->db->delete('projects_comments', array('idcomment' => $idcomment)); 
	}
	
	// Delete update
	public function delete_update($idupdate){
		$this->db->delete('projects_updates', array('idupdate' => $idupdate)); 
	}
	
	// Count updates
    public function count_updates($where = array()) {

        // Generate where clause
        $where = self::where_string_from_array($where);

        // Get projects comments
        $result = $this->db->query("SELECT COUNT(*)
									FROM projects_updates AS pu
									INNER JOIN projects AS p ON p.idproject = pu.idproject
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where");
		
		// Return data
		return $result->result();
    }
	
	// Get all updates
    public function get_updates($where) {

        // Generate where clause
        $where = self::where_string_from_array($where);

        // Get projects amounts
        $result = $this->db->query("SELECT pu.*, p.title AS project_title, u.iduser FROM projects_updates AS pu
									INNER JOIN projects AS p ON pu.idproject = p.idproject
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where");
		
		// Return data
		return $result->result();
    }
	
	// Add project update
	public function add_update($data){

		// Insert donation option
		$this->db->insert('projects_updates', $data); 
		$update_id = $this->db->insert_id();
		
		
		$CI =& get_instance();
		$CI->load->model('Notifications_model', 'notifications', TRUE);
		$object_type = 'project';
		$object_id = $data['idproject'];
		$event_type = 'update';
		$from_member_id = NULL;
		$object_secondary_id = $update_id;
		$object_text = $data['text'];
		if (isset($_SESSION['user']['iduser']) && !empty($_SESSION['user']['iduser'])){
			$from_member_id = $_SESSION['user']['iduser'];
		}
		$CI->notifications->trigger_event($object_type, $object_id, $event_type, $from_member_id, $object_secondary_id, $object_text);				
	}
	
	// Save update
	public function save_update($data, $idupdate){
		$this->db->where('idupdate', $idupdate);
		$this->db->update('projects_updates', $data); 
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
	
	
	// Get location data by postcode or string
	public function get_location_by_postcode_or_string($string){
	
		// Decode string
		$string = urldecode(str_replace("string:", "", $string));
		
		// Get ward code
		$string_wardcode = str_replace(" ", "", $string);

		$wardcode_data = $this->db->query("SELECT ward_code FROM `wards` WHERE pc_full = '$string_wardcode' OR pc_part1 = '$string_wardcode' LIMIT 1")->row();
		if(!empty($wardcode_data)){
			$ward_code = reset($wardcode_data);
		} else {
			$ward_code = "";
		}
		
		// Get location data by ward code
		$location = reset($this->db->query("SELECT * FROM `wards_authorities` WHERE LAD09NM = '$string' OR pc_part1 = '$string' OR WD09CD = '$ward_code'  LIMIT 1")->result());

		// Get location data
		if(!empty($location)){
			$data['ward_code'] = $location->WD09CD;
			$data['ward_name'] = $location->WD09NM;
			$data['town_name'] = $location->LAD09NM;
			$data['county_name'] = $location->CTY09NM;
			$data['location_preview'] = $location->town_name;
			$data['lat'] = $location->lat;
			$data['lng'] = $location->lng;
			
			// Return data
			return $data;
		}

	}
	
	
	// Get projects cities - used for search and etc.
	public function get_projects_cities(){
		return $this->db->query("SELECT COUNT(*) AS cnt, location_preview AS name FROM `projects` WHERE location_preview != '' GROUP BY location_preview ORDER BY cnt DESC LIMIT 10")->result();
	}
	
	
	// Add project categories
	public function add_project_categories($idproject, $categories){
		foreach($categories AS $idcategory => $v){
			$this->db->insert('projects_categories', array("idproject" => $idproject, "idcategory" => $idcategory)); 
		}
	}
	
	// Get project categories
	public function get_project_categories($idproject){
		return $this->db->query("SELECT c.* 
									FROM projects_categories AS pc 
									LEFT JOIN categories AS c ON pc.idcategory = c.idcategory
									WHERE pc.idproject = $idproject LIMIT 50")->result();
	}
	
	// Save project categories
	public function save_project_categories($idproject, $categories){
		
		// Remove project categories
		$this->db->delete('projects_categories', array('idproject' => $idproject)); 
		
		// Add new categories
		$this->add_project_categories($idproject, $categories);
	}
	
	// Move project
	public function move_project($idproject, $direction){
		
		// Get current project data
		$data = $this->db->query("SELECT * FROM `projects` WHERE idproject = $idproject LIMIT 1")->result();
		$data = reset( $data );
		$now = $data->order;		

		// Move up
		if($direction == 'up'){
			$next = $this->db->query("SELECT `order` FROM projects WHERE `order` > '$now' AND `editors_pick` = '1' ORDER BY `order` ASC, `date_created` ASC LIMIT 1")->result();

			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE projects SET `order` = '$now' WHERE `order` = '$next' LIMIT 1");
				$this->db->query("UPDATE projects SET `order` = '$next' WHERE idproject = '$idproject'");
			}
			
		// Move down
		} else {
			$next = $this->db->query("SELECT `order` FROM projects WHERE `order` < '$now' AND `editors_pick` = '1' ORDER BY `order` DESC, `date_created` DESC LIMIT 1")->result();
			
			if(!empty($next)){
				$next = reset($next[0]);
				$this->db->query("UPDATE projects SET `order` = '$now' WHERE `order` = '$next' LIMIT 1");
				$this->db->query("UPDATE projects SET `order` = '$next' WHERE idproject = '$idproject'");
			}
		}
	}
	
	
	// Count projects
	public function count_projects($where = array()){
		
		// Generate where clause
        $where = self::where_string_from_array($where);
		
		// Get total projects
		$result = $this->db->query("SELECT p.*, u.username, u.slug AS user_slug, u.bio AS user_bio, u.ext AS user_ext,
									IF(DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()) < 1, 0, DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()))  AS days_left,
									
									CAST((`amount_pledged` / `amount`) * 100 AS UNSIGNED) AS pledged_percent,
									DATE_FORMAT(DATE_ADD(date_created, INTERVAL +`period` DAY), '%D %M %Y') AS deadline,
									DATE_ADD(date_created, INTERVAL +`period` DAY) AS date_expire,
									CAST(((`period` - (IF(DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()) < 1, 0, DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE())))) / `period`) * 100 AS UNSIGNED) AS pledged_days
									
									-- ,c.title AS category_name, c.slug AS category_slug
									FROM `projects` AS p
									-- LEFT JOIN `categories` AS c ON p.idcategory = c.idcategory
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where")->num_rows();

		// Return results
		return $result;
	}

    // Get all projects
    public function get_projects($where = array(), $limit = array(), $order = "", $categories = TRUE) {

        // Generate where clause
        $where = self::where_string_from_array($where);

		// Generate limit
		$limit = self::generate_limit($limit);
		
		// Generate order
		$order = self::generate_order($order);
		
        // Get projects
        $result = $this->db->query("SELECT p.*, u.username, u.slug AS user_slug, u.bio AS user_bio, u.ext AS user_ext,
									IF(DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()) < 1, 0, DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()))  AS days_left,
									
									CAST((`amount_pledged` / `amount`) * 100 AS UNSIGNED) AS pledged_percent,
									DATE_FORMAT(DATE_ADD(date_created, INTERVAL +`period` DAY), '%D %M %Y') AS deadline,
									DATE_ADD(date_created, INTERVAL +`period` DAY) AS date_expire,
									CAST(((`period` - (IF(DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE()) < 1, 0, DATEDIFF(DATE_ADD(date_created, INTERVAL +`period` DAY), CURDATE())))) / `period`) * 100 AS UNSIGNED) AS pledged_days
									
									-- ,c.title AS category_name, c.slug AS category_slug
									FROM `projects` AS p
									-- LEFT JOIN `categories` AS c ON p.idcategory = c.idcategory
									INNER JOIN users AS u ON p.iduser = u.iduser
									$where $order $limit")->result();
						
		// If we are trying to get projects categories too
		if($categories){
			foreach($result AS $k => &$v){
				$v->categories = $this->get_project_categories($v->idproject);
			}
		}

		// Return data
		return $result;
    }
	
	// Generate new order value for picked object
	public function get_new_order(){
		// Take the new order: the min order value - 1
		$result = $this->db->query("SELECT (MIN(`order`) - 1) AS new_order FROM projects")->result();
		return $result[0]->new_order;
	}	
} 