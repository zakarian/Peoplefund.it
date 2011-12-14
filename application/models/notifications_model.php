<?php

// Include abstract model
require_once(APPPATH.'core/base_model.php');

class Notifications_model extends Base_Model {
	
	public $email_templates = array(); //Cache email templates
	
	// Add event handler for member
	public function configure_event_for_member($member_id, $object_id, $object_role, $object_type, $event_type, $notification_type){
		// Use REPLACE INTO because we already have UNIQUE index on those fields: $member_id, $object_id, $object_type, $event_type
		$member_id = (int)$member_id;
		$object_id = (int)$object_id;
		
		$sql = "
			REPLACE INTO `notifications_settings` 
			SET `member_id` = $member_id, 
				`object_id` = $object_id,
				`object_role` = '$object_role', 
				`object_type` = '$object_type', 
				`event_type` = '$event_type', 
				`notification_type` = '$notification_type', 
				`event_configured_at` = NOW()
		";
		$result = $this->db->query($sql);
	}
	
	
	// Update events handler for member
	public function update_all_events_for_member($member_id, $object_role, $notification_type){
		
		$sql = "
			UPDATE `notifications_settings`
			SET `notification_type` = '$notification_type'
			WHERE object_role = '$object_role' AND member_id = '$member_id'
		";
		
		$result = $this->db->query($sql);
	}
	
	
	public function get_all_new_events_count_for_member($member_id){
		$sql = "
			SELECT st1.total_rows
			FROM (
					SELECT SUM(cnt) AS total_rows FROM(
						SELECT COUNT(*) AS cnt
						FROM `notifications` AS n
						INNER JOIN projects AS j ON (n.object_type = 'project' AND n.object_id = j.idproject)
						WHERE n.member_id = $member_id AND n.read_on IS NULL
					) AS t1
				--	UNION (
				--		SELECT COUNT(*) AS cnt
				--		FROM `notifications` AS n
				--		INNER JOIN blog AS j ON (n.object_type = 'blog' AND n.object_id = j.idblog)
				--		WHERE n.member_id = $member_id AND n.read_on IS NULL
				--	) AS t2
				--  UNION 
				--  ...
				
			) AS st1
		";
		$result = $this->db->query($sql)->result();
		
		return (int)$result[0]->total_rows;
	}
	
	
	public function get_all_events_settings_for_member($member_id){
		$sql = "
			SELECT st1.*
			FROM (
					SELECT * FROM(
						SELECT ns.*, j.title AS object_title, j.slug AS object_slug, j.outcome AS object_description, j.active AS object_active
						FROM `notifications_settings` AS ns
						INNER JOIN projects AS j ON (ns.object_type = 'project' AND ns.object_id = j.idproject)
						WHERE ns.member_id = $member_id
					) AS t1
				--	UNION (
				--		SELECT ns.*, 'blog_title' AS object_title, 'blog_slug' AS object_slug, 'blog_descr' AS object_description, 1 AS object_active
				--		FROM `notifications_settings` AS ns
				--		INNER JOIN blog AS j ON (ns.object_type = 'blog' AND ns.object_id = j.idblog)
				--		WHERE ns.member_id = $member_id
				--	) AS t2
				--  UNION 
				--  ...
				
			) AS st1
			ORDER BY st1.event_configured_at DESC
		";
		$result = $this->db->query($sql)->result();
		return $result;
	}
	
	
	public function get_all_events_for_member($member_id){
		$sql = "
			SELECT st1.*
			FROM (
					SELECT * FROM(
						SELECT ns.*, j.title AS object_title, j.slug AS object_slug, j.outcome AS object_description, j.active AS object_active,
								
								u.name AS from_member_name, u.slug AS from_member_slug, u.username AS from_member_username
								
						FROM `notifications` AS ns
						INNER JOIN projects AS j ON (ns.object_type = 'project' AND ns.object_id = j.idproject)
						LEFT JOIN users AS u ON (IF(ns.from_member_id != '', ns.from_member_id, '0') = u.iduser)
						WHERE ns.member_id = $member_id
					) AS t1
				--	UNION (
				--		SELECT ns.*, 'blog_title' AS object_title, 'blog_slug' AS object_slug, 'blog_descr' AS object_description, 1 AS object_active
				--		FROM `notifications` AS ns
				--		INNER JOIN blog AS j ON (ns.object_type = 'blog' AND ns.object_id = j.idblog)
				--		WHERE ns.member_id = $member_id
				--	) AS t2
				--  UNION 
				--  ...
				
			) AS st1
			ORDER BY st1.event_time DESC
		";
		$result = $this->db->query($sql)->result();
		return $result;
	}
	
	
	public function get_single_event_by_id($id){
		$sql = "
			SELECT st1.*
			FROM (
					SELECT * FROM(
						SELECT ns.*, j.title AS object_title, j.slug AS object_slug, j.outcome AS object_description, j.active AS object_active
						FROM `notifications` AS ns
						INNER JOIN projects AS j ON (ns.object_type = 'project' AND ns.object_id = j.idproject)
						WHERE ns.id = $id
					) AS t1
				--	UNION (
				--		SELECT ns.*, 'blog_title' AS object_title, 'blog_slug' AS object_slug, 'blog_descr' AS object_description, 1 AS object_active
				--		FROM `notifications` AS ns
				--		INNER JOIN blog AS j ON (ns.object_type = 'blog' AND ns.object_id = j.idblog)
				--		WHERE ns.id = $id
				--	) AS t2
				--  UNION 
				--  ...
				
			) AS st1
			ORDER BY st1.event_time DESC
		";
		$result = $this->db->query($sql)->result();
		return $result;
	}
	
	
	public function remove_by_id($id){
		$sql = "
			DELETE FROM `notifications_settings` WHERE `id` = $id LIMIT 1
		";
		$this->db->query($sql);
	}
	
	
	public function get_status($member_id, $object_id = NULL, $object_role = NULL, $object_type = NULL){
		//Update all memberes registered for notifications for this object and this event 
		$object_type_sql = '';
		if (!empty($object_type)){
			$object_type_sql = " AND object_type = '$object_type' ";
		}
		$object_role_sql = '';
		if (!empty($object_role)){
			$object_role_sql = " AND object_role = '$object_role' ";
		}
		$object_id_sql = '';
		if (!empty($object_id)){
			$object_id_sql = " AND object_id = '$object_id' ";
		}
		
		$sql = "
			SELECT COUNT(id) AS cnt FROM `notifications_settings` WHERE `member_id` = $member_id
			$object_type_sql $object_role_sql $object_id_sql
		";
		$result = $this->db->query($sql)->result();
		
		return $result[0]->cnt;
	}
	
	
	public function remove_all_events_for_member($member_id, $object_id = NULL, $object_role = NULL, $object_type = NULL){
		//Update all memberes registered for notifications for this object and this event 
		$object_type_sql = '';
		if (!empty($object_type)){
			$object_type_sql = " AND object_type = '$object_type' ";
		}
		$object_role_sql = '';
		if (!empty($object_role)){
			$object_role_sql = " AND object_role = '$object_role' ";
		}
		$object_id_sql = '';
		if (!empty($object_id)){
			$object_id_sql = " AND object_id = '$object_id' ";
		}
		
		$sql = "
			DELETE FROM `notifications_settings` WHERE `member_id` = $member_id
			$object_type_sql $object_role_sql $object_id_sql
		";
		$result = $this->db->query($sql);
	}

	
	public function remove_all_events_for_object($object_type, $object_id){
		//Update all memberes registered for notifications for this object and this event 
		$sql = "
			DELETE FROM `notifications_settings` WHERE `object_type` = $object_type AND `object_id` = $object_id
		";
		$result = $this->db->query($sql);
	}
	
	
	public function update_notification_type_by_id($id, $member_id, $notification_type){
		$sql = "
			UPDATE `notifications_settings` SET notification_type = '$notification_type' WHERE id = ".(int) $id." AND member_id = ".(int) $member_id." LIMIT 1
		";
		$result = $this->db->query($sql);
	}
	
	public function update_notification($id, $member_id, $update_arr = array()){
		if (!empty($update_arr) && is_array($update_arr)){
			$set_sql = '';
			$set_arr = array();
			
			foreach($update_arr as $key => $val){
				$set_arr[] = "`$key` = '$val'"; 
			}
			if (!empty($set_arr)){
				$set_sql = join(',', $set_arr);
			}
	
			$sql = "
				UPDATE `notifications` SET $set_sql WHERE id = $id AND member_id = $member_id LIMIT 1
			";
			$result = $this->db->query($sql);			
		}
	}
	
	
	//Main hook for adding events
	//If you need to enable additional event codes - just add them into enumeration for `event_type` column
	//If you need to enable additional object types, such as 'forum', 'blog', 'profile' ... - just add them into enumeration for `object_type` column
	public function trigger_event($object_type, $object_id, $event_type = '', $from_member_id = NULL, $object_secondary_id = NULL, $object_text = ''){
		
		$object_text = addslashes($object_text);
		
		if ($from_member_id === NULL){
			$from_member_id_where = 'IS NOT NULL';
		}else{
			$from_member_id_where = '!= '.(int)$from_member_id;
		}
		
		if ($object_secondary_id === NULL){
			$object_secondary_id = 0;
		}else{
			$object_secondary_id = (int)$object_secondary_id;
		}
		
		$event_action = 'NULL';
		
		// Check if we have project status change.
		if(in_array($event_type, array('status_change successful', 'status_change unsuccessful'))) {
			$event_action = "'".str_replace('status_change ', '', $event_type)."'";
			$event_type = 'status_change';
		}

		//Update all memberes registered for notifications for this object and this event 
		$sql = "
			INSERT INTO `notifications` (member_id, from_member_id, object_id, object_role, object_type, event_time, event_type, event_action, notification_type, token, object_secondary_id, object_text)
				SELECT member_id, ".(int)$from_member_id.", object_id, object_role, object_type, NOW(), event_type, $event_action, notification_type, MD5( ROUND( RAND(), 10 )), $object_secondary_id, '$object_text'
				FROM `notifications_settings` WHERE `event_type` = '$event_type' AND `object_type` = '$object_type' AND `object_id` = '$object_id' AND member_id $from_member_id_where
		";

		$result = $this->db->query($sql);

		//Send Instant notification
		// You can either directly call it from the line bellow or set it trough a cron each 5 minute (cron is recommended to avoid slow performance)
		$this->send_email_notification('instant');
	}
	

	########################################
	#
	#	Cron methods & mail notifications
	#
	########################################

	public function mark_notification_handled($id, $content_arr = NULL){
		$update_sql = '';
		if (!empty($content_arr['html'])){
			$update_sql .= " , content_html = '".addslashes($content_arr['html'])."' ";
		}
		if (!empty($content_arr['text'])){
			$update_sql .= " , content_text = '".addslashes($content_arr['text'])."' ";
		}
		
		$sql = "
			UPDATE `notifications` 
			SET notification_handled = 'yes' $update_sql
			WHERE id = $id 
			LIMIT 1
		";
		$this->db->query($sql);
	}
	
	
	public function _send_email_notification_sql($notification_type, $start_time = NULL, $end_time = NULL, $limit_start = 0, $limit_count = 100){
		$time_sql = '';
		if (!empty($start_time)){
			$time_sql .= " AND event_time >= '$start_time' ";
		}
		if (!empty($end_time)){
			$time_sql .= " AND event_time <= '$end_time' ";
		}
		$sql = "
			SELECT n.*, 
				m.username AS member_username, m.email AS member_email, m.name AS member_name, m.slug AS member_slug, m.ext AS member_ext, m.hash AS member_hash,
				fm.username AS from_member_username, fm.email AS from_member_email, fm.name AS from_member_name, fm.slug AS from_member_slug, fm.ext AS from_member_ext, fm.hash AS from_member_hash
			FROM `notifications` AS n
			LEFT JOIN users AS m ON (n.member_id = m.iduser)
			LEFT JOIN users AS fm ON (n.from_member_id IS NOT NULL AND n.from_member_id = fm.iduser)
			WHERE notification_handled = 'no' AND notification_type = '$notification_type' $time_sql
			ORDER BY `member_id` ASC, `event_time` DESC
			LIMIT $limit_start, $limit_count
		";
		
		$sql = "
			SELECT st1.*
			FROM (
					SELECT * FROM(
						SELECT n.*, 
							m.username AS member_username, m.email AS member_email, m.name AS member_name, m.slug AS member_slug, m.ext AS member_ext, m.hash AS member_hash,
							fm.username AS from_member_username, fm.email AS from_member_email, fm.name AS from_member_name, fm.slug AS from_member_slug, fm.ext AS from_member_ext, fm.hash AS from_member_hash,
							j.title AS object_title, j.slug AS object_slug, j.outcome AS object_description, j.active AS object_active
						FROM `notifications` AS n
						INNER JOIN projects AS j ON (n.object_type = 'project' AND n.object_id = j.idproject)
						LEFT JOIN users AS m ON (n.member_id = m.iduser)
						LEFT JOIN users AS fm ON (n.from_member_id IS NOT NULL AND n.from_member_id = fm.iduser)
						WHERE n.notification_handled = 'no' AND n.notification_type = '$notification_type' $time_sql
					) AS t1
				--	UNION (
				--		SELECT n.*, 
				--			m.username AS member_username, m.email AS member_email, m.name AS member_name, m.slug AS member_slug, m.ext AS member_ext, m.hash AS member_hash, 
				--			fm.username AS from_member_username, fm.email AS from_member_email, fm.name AS from_member_name, fm.slug AS from_member_slug, fm.ext AS from_member_ext, fm.hash AS from_member_hash,
				--			j.title AS object_title, j.slug AS object_slug, j.outcome AS object_description, j.active AS object_active
				--		FROM `notifications` AS n
				--		INNER JOIN projects AS j ON (n.object_type = 'project' AND n.object_id = j.idproject)
				--		LEFT JOIN users AS m ON (n.member_id = m.iduser)
				--		LEFT JOIN users AS fm ON (n.from_member_id IS NOT NULL AND n.from_member_id = fm.iduser)
				--		WHERE n.notification_handled = 'no' AND n.notification_type = '$notification_type' $time_sql
				--	) AS t2
				--  UNION 
				--  ...
				
			) AS st1
			ORDER BY member_id ASC, st1.event_time DESC
		";
		
		return $sql;
	}
	
	
	public function _get_email_content($data){
		$return = array();

		$CI =& get_instance();
		if (empty($this->email_templates)){
			$CI->load->model('Emails_model', 'emails', TRUE);
			
			//Slug does not exist in the database - they are for internal description only
			$email_notification_slug_arr = array(
				'notification-instant' => 6, 'notification-daily' => 7, 'notification-weekly' => 8, 'notification-monthly' => 9, 'single-event-text' => 11, 'single-event-text-project-end-successful' => 13, 'single-event-text-project-end-unsuccessful' => 14
			);
			
			$this->email_templates = $CI->emails->get_notification_templates($email_notification_slug_arr);
		}
		
		$notification_type = $data->notification_type;

		$return['html'] = $this->email_templates["notification-$notification_type"];
		$return['text'] = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n",$this->email_templates["notification-$notification_type"]));

		// Params that will be replaced in text and subject
		$text_params = get_object_vars($data);
		$text_params['site_name'] =	'PeopleFund.it';
		$text_params['settings_link'] = '<a href="'.$CI->config->item('base_url') . 'user/profile/?autologin_hash='.$data->member_id.'|'.$data->member_hash.'">'.$CI->config->item('base_url') . 'user/profile/?autologin_hash='.$data->member_id.'|'.$data->member_hash.'</a>';
		
		$text_params['from_member_link'] = '<a href="'.$CI->config->item('base_url') . 'user/'.$data->from_member_slug . '/'.'">'.$CI->config->item('base_url') . 'user/'.$data->from_member_slug . '/'.'</a>';
		if (empty($data->from_member_slug)){
			$data->from_member_slug = 'system.event';
			$text_params['from_member_link'] = 'N/A';
		}
		if (empty($data->from_member_name)){
			$data->from_member_name = 'N/A';
		}
		$text_params['member_name'] = $data->member_username;
		$text_params['from_member_name'] = $data->from_member_username;
		
		$text_params['object_link'] = '<a href="'.$CI->config->item('base_url') . $data->object_slug . '/'.'">'.$CI->config->item('base_url') . $data->object_slug . '/'.'</a>';
		$text_params['event_type'] = ucfirst(strtolower(str_replace('_', ' ', $text_params['event_type'])));
				
		if (!empty($text_params)){
			if($data->event_type == 'status_change' && $data->event_action == 'successful')
				$digested_text = $this->email_templates['single-event-text-project-end-successful'];
			elseif($data->event_type == 'status_change' && $data->event_action == 'unsuccessful')
				$digested_text = $this->email_templates['single-event-text-project-end-unsuccessful'];
			else
				$digested_text = $this->email_templates['single-event-text'];

			foreach($text_params as $key => $value){
				$return['html'] = str_ireplace("[$key]", $value, $return['html']);
				$return['text'] = str_ireplace("[$key]", $value, $return['text']);
				$digested_text = str_ireplace("[$key]", $value, $digested_text);
			}
			
			$return['html'] = str_ireplace('[digested]', $digested_text, $return['html']);
			$return['text'] = str_ireplace('[digested]', $digested_text, $return['text']);
			
		}

		return $return;
	}
	
	
	public function send_email($data){
		$email_content_arr = $this->_get_email_content($data); //text, html  - return of the function
		
		$email_to = $data->member_email;
		$email_subject = 'New ' . str_replace('_', ' ', $data->event_type) . " on ".$data->object_title;
		

		$CI =& get_instance();
		$CI->load->config('emails');
		send_mail($CI->config->item('FROM_EMAIL'), $email_to, $email_subject, $email_content_arr['text']);
		
		$this->mark_notification_handled($data->id, $email_content_arr);
	}
	
	
	public function send_digested_email($notifications_arr_temp){
		$notifications_arr = reset($notifications_arr_temp); //extract first single dimension array, which holds the entire data needed
		
		if (!empty($notifications_arr)){
			$data = reset($notifications_arr);
			

			$email_content_arr = $this->_get_email_content($data); //text, html  - return of the function
			
			$email_to = $data->member_email;
			$email_subject = ucfirst($data->event_type) . " notification from PeopleFund.it";
		
			//Get all into one single text
			$digested_text = $this->digest_notifications($notifications_arr, $email_content_arr);
			
			  
			 
			//Add digested text
			$email_content_arr['html'] = str_ireplace("[digested]", $digested_text, $email_content_arr['html']);
			$email_content_arr['text'] = str_ireplace("[digested]", $digested_text, $email_content_arr['text']);
			$email_content_arr['text'] = strip_tags($email_content_arr['text']);
				
			$CI =& get_instance();
			$CI->load->config('emails');
			send_mail($CI->config->item('FROM_EMAIL'), $email_to, $email_subject, $email_content_arr['text']);
			
		}
	}
	
	
	public function digest_notifications($notifications_arr, $email_content_arr){
		$CI =& get_instance();

		$return = '';
		
		foreach($notifications_arr as $i => $notify){

			$digested_text = $this->email_templates['single-event-text'];

			$text_params = get_object_vars($notify);
			$text_params['site_name'] =	'PeopleFund.it';
			$text_params['settings_link'] = $CI->config->item('base_url') . 'notifications/settings/?autologin_hash='.$notify->member_id.'|'.$notify->member_hash;
			
			$text_params['from_member_link'] = $CI->config->item('base_url') . 'user/'.$notify->from_member_slug . '/';
			if (empty($notify->from_member_slug)){
				$notify->from_member_slug = 'system.event';
				$text_params['from_member_link'] = 'N/A';
			}
			if (empty($notify->from_member_name)){
				$notify->from_member_name = 'N/A';
			}
			
			$text_params['object_link'] = $CI->config->item('base_url') . $notify->object_slug . '/';
			$text_params['event_type'] = str_replace('_', '', $text_params['event_type']);
				
			//Apply parameters to the current event	
			if (!empty($text_params)){
				foreach($text_params as $key => $value){
					$digested_text = str_ireplace("[$key]", $value, $digested_text);
				}
			}
	
			$return .= $digested_text;
			$digested_text = ''; //Just in case, clear everything
			
			$this->mark_notification_handled($notify->id, $email_content_arr);
		}
		
		return $return;
	}
	
	
	//Bad-ass-MF sporty-spammer
	public function send_email_notification($notification_type){
		//Collect all instant notifications and send email for each member
	
		$send_notification = TRUE;
		$start_time = NULL;
		$end_time = NULL;
		
		$limit_start = 0;
		$limit_count = 1000; //Number of notifications to pull per database query. This will optimize the memory usage as well as database load
		
		switch($notification_type){
			case "never":
					$send_notification = FALSE;
				break;
			case "instant":
					//Nothing to change here 
				break;
			case "daily":
					$date_day = date("d", strtotime("-1 day"));
					$start_time = date("Y-m-$date_day 00:00:00");
					$end_time = date("Y-m-$date_day 23:59:59");
				break;
			case "weekly":
					$date_day = date("N"); //Check if it is the first day of next week
					if ($date_day == 1){
						$start_time = date("Y-m-d 00:00:00", strtotime("-7 days"));
						$end_time = date("Y-m-d 23:59:59", strtotime("-1 day"));
					}
				break;
			case "monthly":
					$date_day = date("d"); //Check if it is the first day of the next month
					if ($date_day == 1){
						$start_time = date("Y-m-d 00:00:00", strtotime("-1 month"));
						$end_time = date("Y-m-d 23:59:59", strtotime("-1 day"));
					}
				break;
			default: 
					$notification_type = 'instant';
				break;
		}

		
		$events_digest_cache = array();
		//Add events cache for each user and send a digested single email

		//$send_notification = FALSE;
		if ($send_notification){
			
			//Chunk database results / queries to portions of 1000 results per query
			do {
				$sql = $this->_send_email_notification_sql($notification_type, $start_time, $end_time, $limit_start, $limit_count);
				$result = $this->db->query($sql)->result();
				
				
				//Prepare for next loop
				$limit_start += $limit_count;
				
				if (is_array($result)){ //Check if we have any results after all or it is time to stop the loop
					foreach($result as $i => $notify){
						
						//Digest the email if needed
						if ($notification_type == 'instant'){
							$this->send_email($notify);
						}else{
							//Check if still filling the digestive cache or we are at next user
							
							if (empty($events_digest_cache)){
								//Initialize the ferris wheel 
								$member_id = (int)$notify->member_id;
								$events_digest_cache[$member_id] = array();
								$events_digest_cache[$member_id][] = $notify;
							}else{
								//Check if we are still at the same user (notifications are ordered based on their userID)
								if (isset($events_digest_cache[ $member_id ])){
									$events_digest_cache[$member_id][] = $notify;
								}else{
									//Time to digest all events so far and start with new user
									$this->send_digested_email($events_digest_cache);
									
									//start with new user
									$events_digest_cache = array();
									$events_digest_cache[$member_id] = array();
									$events_digest_cache[$member_id][] = $notify;
								}
							}
						}
					}
					
					//Finish last user from the queue
					if (!empty($events_digest_cache)){
						$this->send_digested_email($events_digest_cache);
						unset($events_digest_cache);
					}
				}
			} while(is_array($result) && !empty($result));
		}
		
		
	}
	
}