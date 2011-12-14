<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Notification controller 
* Handles the notifications/alerts in the user profile
* When a specific event occurs the site is sending alerts to the users that own or watch a project. 
* The event types are change of the project status, project updates and project comments.
* On a certain period  of time the site is sending emails to the users that receive notifications. This is handled by /crons/notifications .
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Notifications extends Base_Controller {
	
	/**
	* Display all the notifications/alerts for the current user
	*
	* @access public
	*/
	public function index()
	{
		// If user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect('/');
		}
		
		// Load notifications model
		$this->load->model('Notifications_model', 'notifications');
		
		// Get all notifications for the current user
		$vars['notifications'] = $this->notifications->get_all_events_for_member($_SESSION['user']['iduser']);		
		
		// Display notifications template
		$this->view('notifications/browse', $vars);
	}
	
	/**
	* Display the notification settings for the current user determining how often the user will get notifications email.
	* The settings value could be 'never','instant','daily','weekly' and 'monthly' for each event type.
	*
	* @access public
	*/
	public function settings()
	{
		// If user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect('/');
		}
		
		// Load notifications model
		$this->load->model('Notifications_model', 'notifications');
		
		// Get notification settings for the current user
		$vars['notifications'] = $this->notifications->get_all_events_settings_for_member($_SESSION['user']['iduser']);
		
		// Display notification settings template
		$this->view('notifications/settings', $vars);	
	}
	
	/**
	* Delete a notification
	*
	* @param string $id Id of the notification to be deleted
	* @access public
	*/
	public function delete($id)
	{		
		// If user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Load notifications model
		$this->load->model('Notifications_model', 'notifications');
		
		// Remove the selected notification
		$this->notifications->remove_by_id($id);
		
		// Redirect back to notifications
		redirect("/notifications/");
	}	
	
	/**
	* Update the notification settings for the different events.
	* The notification types could be 'never','instant','daily','weekly' and 'monthly'
	*
	* @access public
	*/
	public function update()
	{	
		// Check if the new notification settings are set in the post array
		if ( ! empty($_POST['events']) && is_array($_POST['events']))
		{
			// Load notifications model
			$this->load->model('Notifications_model', 'notifications');
		
			// Loop the events
			foreach($_POST['events'] AS $id => $notification_type)
			{
				// Update notification types
				$this->notifications->update_notification_type_by_id($id, $_SESSION['user']['iduser'], $notification_type);
			}
		}
		
		// Redirect back to notifications
		redirect('/notifications/');
	}
	
	/**
	* View notification. 
	*
	* @param string $id Id of the notification to be viewed
	* @access public
	*/
	public function read($id)
	{		
		// If user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// If no notification is specified redirect to notifications
		if (empty($id))
		{
			redirect("/notifications/");
		}
		
		// Load notifications model
		$this->load->model('Notifications_model', 'notifications');
		
		// Get the selected notification from the database
		$vars['data'] = (array) reset($this->notifications->get_single_event_by_id($id));
		
		// Mark as read
		if($vars['data']['member_id'] == $_SESSION['user']['iduser'])
		{
			if (empty($vars['data']['read_on']))
			{
				$this->notifications->update_notification($id, $_SESSION['user']['iduser'], array("read_on" => date('Y-m-d H:i:s')));
			}
		}		
		
		// If using ajax let it be known by the view
		if (uri_string() && preg_match('/ajax/', uri_string())) 
		{
			$vars['ajax'] = TRUE;
		}
		
		// Get projects model
		$this->load->model('Projects_model', 'projects');	

		// If the notification is because of project status changed check if the project was successful		
		if ($vars['data']['event_type'] == 'status_change') 
		{
			// The the project of the selected notification
			$result = $this->projects->get_projects(array("p.idproject" => $vars['data']['object_id']));
			$vars['project'] = $result[0];
			
			// If the project is finished
			if ($vars['project']->status == 'closed') 
			{
				// If the project amount was reached set the projcet as successful. To be used in the view
				if ($vars['project']->pledged_percent >= 100) 
				{
					$vars['project_successful'] = 'true';
				} 
				// else set the projcet as unsuccessful
				else 
				{
					$vars['project_successful'] = 'false';
				}
			}
		}
			
		// Display view notification template
		$this->view('notifications/view', $vars);
 	}	
}

/* End of file notifications.php */
/* Location: ./application/controllers/notifications.php */