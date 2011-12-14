<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Messages controller 
* Handles messaging between users of the site 
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Messages extends Base_Controller {

	/**
	* Index action
	* Redirect to inbox 
	*
	* @access public
	*/
	public function index()
	{
		redirect("/messages/inbox/");
	}
	
	/**
	* Inbox. Browse incoming messages
	*  
	* @access public
	*/
	public function inbox()
	{	
		// If the user is not logged in redirect to home page	
		if(empty($_SESSION['user']))
		{
			redirect("/");
		}		

		// Define array with vars to be used in view
 		$vars = array();
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');
		
		// Where clause array
		$where = array(
			"m.status_receiver"		=>	" != deleted "
		);
		
		// Set the current user 
		$where['idreceiver'] = $_SESSION['user']['iduser'];
		// Set the message folder - inbox or sent
		$vars['direction'] = "inbox";
		
		
		// Get current page
		if( ! empty($uri[3]))
		{
			$page = $uri[3];
		} 
		else 
		{
			$page = 0;
		}
	
		// Load pagination library
		$this->load->library('pagination');
		
		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		
		// Config the pager
		$pager['base_url'] = site_url('/messages/'.$vars['direction'].'/');
		$pager['total_rows'] = $this->inmail->count_messages($where);
		$pager['per_page'] = 20;
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page']) + 1;
		
		// Set current page - could be used by header and footer
		$vars['current_page'] = "messages";	
		
		// Get user messages
		$vars['messages'] = $this->inmail->get_messages($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));

		// Display browse messages template
		$this->view('messages/browse', $vars);
	}
	
	/**
	*  Display sent messages
	*  
	* @access public
	*/
	public function sent()
	{		
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Define array with vars to be used in view
		$vars = array();
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');
		
		// Where clause array
		$where = array(
			"m.status_sender"		=>	" != deleted "
		);

		// Set the current user
		$where['idsender'] = $_SESSION['user']['iduser'];
		// Browse sent messages
		$vars['direction'] = "sent";
	
		// Get current page
		if (!empty($uri[3]))
		{
			$page = $uri[3];
		} 
		else 
		{
			$page = 0;
		}
	
		// Load pagination library
		$this->load->library('pagination');
		
		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		
		// Config the pager
		$pager['base_url'] = site_url('/messages/'.$vars['direction'].'/');
		$pager['total_rows'] = $this->inmail->count_messages($where);
		$pager['per_page'] = 20;
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page']) + 1;
		
		// Set current page - could be used by header and footer		
		$vars['current_page'] = "messages";	
	
		// Get user messages
		$vars['messages'] = $this->inmail->get_messages($where);

		// Display browse messages template
		$this->view('messages/browse', $vars);
	}
	
	/**
	* View message
	*  
	* @access public
	* @param string $idmessage Id of the selected message
	*/
	public function read($idmessage)
	{		
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// If no message is specified redirect to inbox
		if (empty($idmessage))
		{
			redirect("/messages/");
		}
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');
		
		// Get message data
		$vars['data'] = (array) reset($this->inmail->get_messages(array("idmessage" => $idmessage)));
		
		// Mark as read
		if ($vars['data']['idreceiver'] == $_SESSION['user']['iduser'])
		{
			$this->inmail->save_message(array("type" => "old", "status_receiver" => "old"), $idmessage);
		}
		
		// Check the message owner - if the current user is not the owner redirect to inbox
		if ($vars['data']['idsender'] != $_SESSION['user']['iduser'] && $vars['data']['idreceiver'] != $_SESSION['user']['iduser'])
		{
			redirect("/messages/");
		}
		
		// If using ajax let it be known by the view
		if (uri_string() && preg_match('/ajax/', uri_string())) 
		{
			$vars['ajax'] = TRUE;
		}
		
		// Display view message template
		$this->view('messages/view', $vars);
 	}
	
	/**
	* Write message
	*  
	* @access public
	* @param string $iduser Id of the receiver
	* @param boolean $no_template If true do not view template but return vars. Use it when calling this method from another method.
	* @return array $vars Return variables if called from another method. They can contain error messages.
	*/
	public function send($iduser = "", $no_template = FALSE){		
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Define array with vars to be used in view or returned by the function
		$vars = array();
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// If we have user id set the username of the receiver
		if ( ! empty($iduser) && $iduser > 0)
		{
			$receiver_data = $this->users->get_users(array("iduser" => $iduser));
			$receiver_data = (array) reset($receiver_data);
			$vars['post']['receiver'] = $receiver_data['username'];
		}
		
		// Browse the uri segments to see the type of the message to be send
		$uri = $this->uri->segment_array();
		foreach ($uri AS $segment)
		{
			// If the message is reply
			if (strstr($segment, "reply-"))
			{
				// Get the id of the message to which we reply
				$idmessage = addslashes(urldecode(str_replace("reply-", "", $segment)));
				// Get the message to which we reply
				$vars['message'] = (array)reset($this->inmail->get_messages(array("idmessage" => $idmessage)));
				// Set reply var to true to be used in the view
				$vars['reply'] = TRUE;
			}
			// If the message is forward
			if (strstr($segment, "forward-"))
			{
				// Get the id the message which we are forwarding				
				$idmessage = addslashes(urldecode(str_replace("forward-", "", $segment)));
				// Get the message which we are forwarding				
				$vars['message'] = (array)reset($this->inmail->get_messages(array("idmessage" => $idmessage)));
				// Set forward var to true to be used in the view				
				$vars['forward'] = TRUE;
			}
		}
		
		// If the message is submitted try to send it
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{
			// Add post data to the template
			$vars['post'] = $this->input->post();
			
			// Try to get receiver data
			$receiver_data = $this->users->get_users(array("username" => $vars['post']['receiver']));
			if ($receiver_data)
			{
				$receiver_data = (array) reset($receiver_data);
				// If the user try to send mail to himself set an error
				if ($receiver_data['iduser'] == $_SESSION['user']['iduser'])
				{
					$vars['errors'][] = "You can't send messages to yourself";
				}
			}
			// If receiver is not found set an error
			else 
			{
				$vars['errors'][] = "Message receiver was not found";
			}

			// If the title is empty set an error
			if (empty($vars['post']['title']))
			{
				$vars['errors'][] = "Message title can't be empty";
			}
			
			// If the text is empty an error
			if (empty($vars['post']['text']))
			{
				$vars['errors'][] = "Message text can't be empty";
			}
			
			// If we have no errors
			if (empty($vars['errors']))
			{			
				// Message data to be inserted in the database
				$data = array(
					"idsender" 		=>	$_SESSION['user']['iduser'],
					"idreceiver"	=>	$receiver_data['iduser'],
					"type"			=>	"new",
					"title"			=>	$this->input->post("title"),
					"text"			=>	$this->input->post("text"),
					"date_sent"		=>	date("Y-m-d H:i:s")
				);
				
				// Insert message
				$this->inmail->add_message($data);

				// Get email configuration
				$this->load->config('emails');
				
				// Get email template to send an email to the receiver to notfy him he has a new message
				$this->load->model('Emails_model', 'emails');
				$email_data = (array) reset($this->emails->get_emails(array("idemail" => "3")));

				// Get site title
				$this->load->model('Configuration_model', 'configuration');
				$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
				$site_title = reset($site_title);

				// Browse messages link
				$link = $this->config->item('base_url')."messages/";
				
				// Parameters to be used in the email
				$params = array(
					"[site_name]"	=>	$site_title,
					"[sender]"		=>	$_SESSION['user']['username'],
					"[link]"		=>	"<a href='".$link."'>".$link."</a>"
				);

				// If the email is active send it
				if ($email_data['active'] > 0)
				{				
					// Send new message email
					send_mail($this->config->item('FROM_EMAIL'), $receiver_data['email'], $email_data['subject'], $email_data['text'], $params, $params);
				}
		
				// Assign success message
				$vars['success'] = "Your message was successfully sent.";
				// Unset post vars as we have already used them
				unset($vars['post']);
			}
		}
		
		// If using ajax let it be known by the view
		if (uri_string() && preg_match('/ajax/', uri_string())) 
		{
			$vars['ajax'] = TRUE;
		}
		
		// Set current page - could be used by header and footer
		$vars['current_page'] = 'message_send';
		
		
		
		// Return vars if the method is called by another method
		if ($no_template === TRUE)
		{
			return $vars;
		}
		// Else display the view
		else 
		{
			// Display the send message view
			$this->view('messages/send', $vars);
		}
	}	
	
	/**
	* Write message to project backers
	*  
	* @access public
	* @param string $project_slug Slug of the project
	*/
	public function send_backers($project_slug = "")
	{		
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get Project Info
		if ( ! empty($project_slug))
		{
			// Try to get the project from database
			$vars['project'] = $this->projects->get_projects(array("p.slug" => $project_slug));
			if (empty($vars['project']))
			{
				redirect("/");
			}
			else
			{
				$vars['project'] = $vars['project'][0];
			}
		}
		// If no project slug is set redirect to home page
		else
		{
			redirect("/");
		}
		
		// If the message is submitted try to send it
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			// Add post data to the template
			$vars['post'] = $this->input->post();
			// Set the project slug to find the receivers - the project backers			
			$vars['post']['receiver'] = $project_slug;
		
			// Set the where clause
			$where = array(
				"pl.idproject" => $vars['project']->idproject, 
				"pl.public" => 1, 
				"pl.status" => "accepted"
			);
			
			// Get project backers
			$users_arr = $this->projects->get_project_pledgers($where);

			// If the title is empty set an error
			if (empty($_POST['title']))
			{
				$vars['errors'][] = "Message title can't be empty";
			}
			
			// If the text is empty an error
			if (empty($_POST['text']))
			{
				$vars['errors'][] = "Message text can't be empty";
			}
			
			// If we have no errors send the message
			if (empty($vars['errors']))
			{
				// Define array to be used for the results of sending messages
				$result = array();
				// Loop the backers and send email to each of them
				foreach ($users_arr as $user)
				{
					//Do not send message to yourself
					if ($user->iduser != $_SESSION['user']['iduser']){	
						// Set the receiver			
						$_POST['receiver'] = $user->username;
						// Send the message
						$result = $this->send($user->iduser, TRUE);
						// Get the errors from the result
						if ( ! empty($result['errors']))
						{
							$vars['errors'] = array_merge($vars['errors'], $result['errors']);					
						}
					}
				}

				// If there are no errors set success message
				if (empty($vars['errors'])){
					$vars['success'] = 'Message has been sent';
				}
			} 
		}
		
		// If using ajax let it be known by the view
		if(uri_string() && preg_match('/ajax/', uri_string())) {
			$vars['ajax'] = TRUE;
		}
		
		// Set current page - could be used by header and footer
		$vars['current_page'] = 'message_send';
		
		// Display send message template
		$this->view('messages/send', $vars);
	}

	/**
	* Write message to project helpers
	*  
	* @access public
	* @param string $project_slug Slug of the project
	*/
	public function send_helpers($project_slug = ""){		
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get Project Info
		if ( ! empty($project_slug))
		{
			// Try to get the project from database
			$vars['project'] = $this->projects->get_projects(array("p.slug" => $project_slug));
			if (empty($vars['project']))
			{
				redirect("/");
			}
			else
			{
				$vars['project'] = $vars['project'][0];
			}
		}
		// If no project slug is set redirect to home page
		else
		{
			redirect("/");
		}
		
		// If the message is submitted try to send it
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{		
			// Add post data to the template
			$vars['post'] = $this->input->post();
			// Set the project slug to find the receivers - the project backers			
			$vars['post']['receiver'] = $project_slug;
		
			// Set the where clause			
			$where = array(
				"pl.idproject" => $vars['project']->idproject, 
				"pl.public" => 1, 
				"pl.status" => "accepted"
			);
			
			// Get project pledgers
			$users_arr = $this->projects->get_project_pledgers($where);

			// If the title is empty  set an error
			if (empty($_POST['title']))
			{
				$vars['errors'][] = "Message title can't be empty";
			}
			
			// If the text is empty  set an error
			if (empty($_POST['text']))
			{
				$vars['errors'][] = "Message text can't be empty";
			}
			
			// If we have no errors send the message
			if (empty($vars['errors']))
			{
				// Define array to be used for the results of sending messages
				$result = array();
				// Loop the backers and send email to each of them				
				foreach ($users_arr as $user)
				{
					// If the pledger is a helper send him the message
					if ( ! empty($user->helper_text)) 
					{
						//Do not send message to yourself
						if ($user->iduser != $_SESSION['user']['iduser'])
						{
							// Set the receiver
							$_POST['receiver'] = $user->username; 
							// Send the message
							$result = $this->send($user->iduser, TRUE);
							// Get the errors from the result
							if ( ! empty($result['errors']))
							{
								$vars['errors'] = array_merge($vars['errors'], $result['errors']);					
							}
						}
					}
				}
				// If we have no errors set success message
				if (empty($vars['errors']))
				{
					$vars['success'] = 'Message has been sent';
				}
			}
		}
		
		// If using ajax		
		if (uri_string() && preg_match('/ajax/', uri_string())) 
		{
			$vars['ajax'] = TRUE;
		}
		
		// Set current page - could be used by header and footer
		$vars['current_page'] = 'message_send';
		
		// Display send message template
		$this->view('messages/send', $vars);
	}
	
	/**
	 * Delete one or more messages
	 *
	 * @access public
	 */
	public function delete_many()
	{
		// If the user is not logged in redirect to home page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// If no messages are set to delete redirect to home page
		if (empty($_POST['delete_msg']))
		{
			redirect("/");
		}
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');
		
		// Loop through the selected messages and delete them
		foreach ($_POST['delete_msg'] AS $idmessage => $value)
		{
			// Message is checked delete it
			if ($value)
			{		
				// Check message owner - if he is not the sender or the receiver do not delete the message
				$data = (array) reset($this->inmail->get_messages(array("idmessage" => $idmessage)));
				if (($data['idreceiver'] != $_SESSION['user']['iduser']) && ($data['idsender'] != $_SESSION['user']['iduser']))
				{
					continue; //Leave this message
				}
				
				// Mark message as old
				$update_arr = array("type" => "old");
				
				// Check if the current user is sender or reciver and mark message as deleted respectively for the sender or the receiver
				if ($data['idreceiver'] == $_SESSION['user']['iduser'])
				{
					$update_arr['status_receiver'] = 'deleted';
				}
				else
				{
					$update_arr['status_sender'] = 'deleted';
				}
				
				// Remove message - mark it in the database as deleted
				$this->inmail->save_message($update_arr, $idmessage);
			}
		}
		
		// Redirect back to messages
		redirect("/messages/");
	}
	
	/**
	* Delete one message
	* 
	* @access public
	* @param string $idmessage Id of the message to be deleted
	*/
	public function delete($idmessage)
	{
		// If the user is not logged in redirect to home page
		if(empty($_SESSION['user'])){
			redirect("/");
		}
		
		// Load messages model
		$this->load->model('Inmail_model', 'inmail');		
		
		// Check message owner - if he is not the sender or the receiver do not delete the message
		$data = (array) reset($this->inmail->get_messages(array("idmessage" => $idmessage)));
		if (($data['idreceiver'] != $_SESSION['user']['iduser']) && ($data['idsender'] != $_SESSION['user']['iduser']))
		{
			redirect("/messages/");
		}
		
		// Mark message as old
		$update_arr = array("type" => "old");
		
		// Check if the current user is sender or reciver and mark message as deleted respectively for the sender or the receiver
		if ($data['idreceiver'] == $_SESSION['user']['iduser'])
		{
			$update_arr['status_receiver'] = 'deleted';
		}
		else
		{
			$update_arr['status_sender'] = 'deleted';
		}
		
		// Remove message - mark it in the database as deleted
		$this->inmail->save_message($update_arr, $idmessage);
		
		// Redirect back to messages
		redirect("/messages/");
	}
	
	/**
	* Send message to selected users
	* Used when project owner send messages to some of the backers
	* 
	* @access public
	* @param string $users Usernames of the users to which the message will be sent separated with ','
	*/
	public function message_users($users = "")
	{
		// If the user is not logged in redirect to home page
		if(empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Decode the string with the usernames from the url
		$users = urldecode($users);
		// Set the usernames to the view
		$vars['post']['receiver'] = $users;
		
		// If the message is submitted try to send it
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{		
			// Add post data to the view
			$vars['post'] = $this->input->post();			
			
			// Form the where clause - if there are more than one usernames
			if (substr_count($users, ",") > 0)
			{
				// Decode and put the user ids to an array
				$users = explode(",", $users);
				// Loop usernames and prepare the where clause 
				foreach ($users AS $k => $user)
				{
					if($k == "0") continue;
					$usernames[] = "username = '".$user."'";
				}
				// Separate usernames with or to prepare the where clause
				$usernames = implode(" OR ", $usernames);

				// Create where clause to get the users
				$where = array(
					"username"	=>	$users[0]."' OR ".substr($usernames, 0, -1)
				);
			} 
			// If there is only one username
			else 
			{
				// Create where clause to get the user				
				$where = array(
					"username"	=>	$users
				);
			}
			
			// Get the users we are sending the message
			$users_arr = $this->users->get_users($where);

			// If the title is empty set an error
			if (empty($_POST['title']))
			{
				$vars['errors'][] = "Message title can't be empty";
			}
			
			// If the text is empty set an error
			if (empty($_POST['text']))
			{
				$vars['errors'][] = "Message text can't be empty";
			}
			
			// If we have no errors send message
			if (empty($vars['errors']))
			{				
				// Define array to be used for the results of sending messages				
				$result = array();
				// Loop the backers and send email to each of them
				foreach ($users_arr as $user)
				{				
					//Do not send message to yourself
					if ($user->iduser != $_SESSION['user']['iduser'])
					{	
						// Set the receiver
						$_POST['receiver'] = $user->username; 
						// Send the message
						$result = $this->send($user->iduser, TRUE);
						// Get the errors from the result
						if ( ! empty($result['errors'])){
							$vars['errors'] = array_merge($vars['errors'], $result['errors']);					
						}
					}				
				}				
				// If we have no errors set success message
				if (empty($vars['errors'])){
					$vars['success'] = 'Message has been sent';
				}
			}
		}
		
		// If using ajax
		if(uri_string() && preg_match('/ajax/', uri_string())) {
			$vars['ajax'] = TRUE;
		}
		
		// Set current page - could be used by header and footer
		$vars['current_page'] = 'message_send';
		
		// Display send message template
		$this->view('messages/send', $vars);
	}
}

/* End of file messages.php */
/* Location: ./application/controllers/messages.php */