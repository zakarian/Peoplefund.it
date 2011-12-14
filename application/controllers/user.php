<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/**
* User controller
* Handles user sing up, login, confirmation, user profile page etc.
*
* @package PeopleFund
* @category Administration
* @author MTR Design
* @link http://peoplefund.it
*/
class User extends Base_Controller {

	/**
	* This function will remap to user profiles when the url's are /user/[slug]
	*
	* @param string $slug_temp The slug or method name called from the url
	* @param array $parts Parameters passed from the url
	* @access public
	*/ 
	public function _remap($slug_temp, $parts)
	{		
		// Get the current slug or method name from the url
		$slug = $this->uri->segment(2);
		
		// Load users model
		$this->load->model('Users_model', 'users');

		// Try to get user data for the current slug
		$data = $this->users->get_users(array("slug" => $slug));
		
		// If the user is found
		if ( ! empty($data))
		{
			// Get the user data
			$data = reset($data);
		
			// Show the user profile if no specific parameters are set
			if (empty($parts))
			{			
				// Show profile
				$this->public_profile($data);			
			}
			// If browsing projects
			else if
			($parts[0] == "projects")
			{				
				// Show user projects
				if (empty($parts[1]))
				{				
					// Show projects
					$this->public_projects($data);					
				} 
				// If browsing ALL started projects	from this user
				else if	($parts[1] == "started")
				{				
					// Show started projects
					$this->public_started_projects($data);				
				} 
				// If browsing ALL backed projects from this user	
				else if	($parts[1] == "backed")
				{				
					// Show backed projects
					$this->public_backed_projects($data);				
				} 
				// If browsing ALL watched projects	from this user	
				else if ($parts[1] == "watched")
				{				
					// Show watched projects
					$this->public_watched_projects($data);
				} 
				// If browsing ALL unfinished projects from this user
				else if ($parts[1] == "unfinished")
				{				
					// Show watched projects
					$this->publicUnfinishedProjects($data);
				}
			}	
		} 
		// If the user is not found	look if method is called
		else 
		{
			// If the class method is found
			if (method_exists($this, $slug))
			{
				// Call the method and pass it the parameters
				call_user_func_array ( array($this, $slug) , $parts );				
			} 
			// If the class method is not found	redirect to 404 page		
			else 
			{
				redirect("/404/");
			}
		}
	}

	
	/**
	* Show sign up form and create new user when it is filled
	*
	* @access public
	*/ 
	public function sign_up()
	{
		// If the user is logged in - redirect to home page
		if (!empty($_SESSION['user']))
		{
			redirect("/");
		}
	
		// Errors array
		$vars['errors'] = array();
	
		// Try to sign up if the form is submitted 
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{
			// Add post data to the template
			$vars['post'] = $this->input->post();
			
			// Load users model
			$this->load->model('Users_model', 'users');
			// Try to get user with the same username to make sure it is free
			$user_data = reset($this->users->get_users(array("username" => $this->input->post("username"))));
			
			// Check username length - if it's too short set an error
			if (strlen($this->input->post("username")) < 3)
			{
				$vars['errors'][] = "Username can't be less than 3 symbols";			
			}
			// If the username is already used set an error		
			else if ($user_data)
			{
				$vars['errors'][] = "The username is already taken";
			}
			
			// Check the password - if it's too short set an error 
			if (strlen($this->input->post("password")) < 3)
			{
				$vars['errors'][] = "Password can't be less than 3 symbols";			
			}
			// If the second password doesn't match set an error 
			else if ($this->input->post("password") != $this->input->post("password_repeat"))
			{
				$vars['errors'][] = "Passwords don't match";
			}
			
			// Try to find user with the same email to make sure it is not already used
			$user_data = reset($this->users->get_users(array("email" => $this->input->post("email"))));
			
			// Check email syntax
			if ( ! check_email($this->input->post('email')))
			{
				$vars['errors'][] = "Invalid email address";
			}
			// If there is already user with the same email set an error
			else if($user_data)
			{
				$vars['errors'][] = "The email is already used in our system";
			}			
			
			// If we don't have any errors create new user
			if(empty($vars['errors']))
			{
				// Add user data
				$data = array(
					"username"			=>	slugify($this->input->post("username")),
					"password"			=>	md5($this->input->post("password")),
					"active"			=>	$this->input->post("active"),
					"email"				=>	$this->input->post("email"),
					"type"				=>	"user",
					"active"			=>	"1",
					"date_register"		=>	date("Y-m-d H:i:s"),
					"date_login"		=>	date("Y-m-d H:i:s")
				);
				
				// Generate slug from username
				$slug = slugify($this->input->post("username"));
				// Check if the generated slug is already used
				$check_arr = $this->users->get_users(array("slug" => $slug));
				$check = count($check_arr);
				
				// If the slug is used generate new one
				if ($check)
				{
					do 
					{
						// Generate new slug
						$slug .= "_1";
					
						// Check if the new generated slug is also used
						$check_arr = $this->users->get_users(array("slug" => $slug));
						$check = count($check_arr);
					}
					// Loop while a slug that is not used is generated
					while ($check != 0);
				}
				
				// Set the slug
				$data['slug'] = $slug;
				
				// Check for Facebook id - if the Facebook profile is used for the sign up
				$fbid = $this->input->post("fbid");
				if ( ! empty($fbid))
				{
					$data['fbid'] = $this->input->post("fbid");
				}
				
				// Add the new user to the database
				$this->users->add_user($data);
				// Get the id of the new user
				$iduser = $this->db->insert_id();
				
				// ==== Set the default profile image to the user ==== //
				
					// Set profile image extension to default one
					$ext = strtolower(end(explode(".", DEFAULT_USER_IMAGE)));
	
					// Form upload path
					$path = SITE_DIR."public/uploads/users/$iduser." . $ext;
					// Copy the default image
					copy(DEFAULT_USER_IMAGE, $path);
					chmod($path, 0777);				
				
				//Get fresh new copy of user data and log him in
				$user_data = $this->users->get_users(array("iduser" => $iduser));
				
				// Load session
				$this->load->helper('session');				
				
				// Start user session (login)
				$_SESSION['user'] = (array) reset($user_data);
				
				// Update last login to the database
				$this->users->save_user(array("date_login" => date("Y-m-d H:i:s"), 'ext' => $ext), $_SESSION['user']['iduser']);
				
				//Retrieve new Alerts/Notifications
				$this->load->model('Notifications_model', 'notifications');
				$_SESSION['info']['new_notifications'] = $this->notifications->get_all_new_events_count_for_member($_SESSION['user']['iduser']);
				
				//Retrieve new Messages for this user
				$_SESSION['info']['new_messages'] = $this->users->get_new_messages_count($_SESSION['user']['iduser']);
				
				// ==== Send welcome email with confirmation link to the new user ==== //
				
					// Get email data for the welcome email
					$this->load->model('Emails_model', 'emails');
					$email_data = (array) reset($this->emails->get_emails(array("idemail" => "1")));
	
					// Get site title
					$this->load->model('Configuration_model', 'configuration');
					$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
					$site_title = reset($site_title);
					
					// Params that will be replaced in email subject
					$title_params = array(
						"[site_name]"	=>	$site_title,
					);					
					
					// Get email config
					$this->load->config('emails');
	
					// Form activation link
					$link = $this->config->item('base_url').'user/confirm/'.encode_string($iduser);
	
					// Params that will be replaced in the email text
					$text_params = array(
						'[site_name]'	=>	$site_title,
						'[link]'		=>	"<a href='".$link."'>".$link."</a>"
					);
	
					// Send wlecome email with confirmation link
					send_mail($this->config->item('FROM_EMAIL'), $this->input->post('email'), $email_data['subject'], $email_data['text'], $title_params, $text_params);

				// Assign success message to the session
				$_SESSION['success'] = "Your registration was successful, please check your email address";
				
				// Redirect to index page
				$vars['logged'] = TRUE;
				$vars['ajax'] = TRUE;
				$vars['message'] = $_SESSION['success'];
				$this->view('user/closeframe', $vars);
			}
		}

		// Show sign up form if not logged in (after new user is created) 
		if ( ! isset($vars['logged']) OR $vars['logged'] == FALSE)
		{
			// If ajax is used
			if (uri_string() && preg_match('/ajax/', uri_string())) 
			{
				$vars['ajax'] = TRUE;
			}
			// Display sing up form
			$this->view('user/signup', $vars);
		}
	}

	/**
	* Confirm user - called by a confirm link sent by email to the user, containing confirm hash
	*
	* @param string $hash Hash generated from the user id, used to confirm the user.
	* @access public
	*/
	public function confirm($hash = '')
	{
		// If no hash is set redirect to index page
		if (empty($hash))
		{
			redirect('/');
		}
		
		// Decode user id from the hash
		$iduser = decode_string($hash);
		$iduser = (int)$iduser;
		// If cannot decode valid id from the hash redirect to index page 
		if (!$iduser)
		{
			redirect('/');
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Update user as confirmed in the database
		$this->users->save_user(array('confirmed' => '1'), $iduser);
		
		// Get user information and login
		$_SESSION['user'] = (array) reset($this->users->get_users(array('iduser' => $iduser)));
		
		// ==== Send confirmation success email the user ==== //

			// Get emails config
			$this->load->config('emails');
			
			// Get the data for the confirmation success email
			$this->load->model('Emails_model', 'emails');
			$email_data = (array) reset($this->emails->get_emails(array("idemail" => "2")));
	
			// Get site title
			$this->load->model('Configuration_model', 'configuration');
			$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
			$site_title = reset($site_title);
	
			// Params that will be replaced in the email
			$params = array(
				"[site_name]"	=>	$site_title
			);
	
			// If the email is active
			if ($email_data['active'] > 0)
			{			
				// Send success email
				send_mail($this->config->item('FROM_EMAIL'), $_SESSION['user']['email'], $email_data['subject'], $email_data['text'], $params, $params);
			}
				
		// Assign success message to the session
		$_SESSION['success'] = "You have successfully activated your account";
		
		// Redirect to index page
		redirect("/");
	}
	
	/**
	* Change user password
	*
	* @param string $id_encoded Encoded user id
	* @param string $hash User hash that is stored in the database. Used to confirm that the method is called by the correct user.
	* @access public
	*/
	public function new_password($id_encoded = '', $hash = '')
	{
		// Check hash and id_encoded - if they are not valid redirect to index page
		if (empty($id_encoded) OR empty($hash) OR (strlen($hash) != 32))
		{
			redirect("/");
		}
		
		// Decode the user id
		$iduser = decode_string($id_encoded);
		$iduser = (int)$iduser;
		if ($iduser <= 0){
			redirect("/");
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Try to get user data for the given id and hash
		$user_data = (array) reset($this->users->get_users(array("iduser" => $iduser, "hash" => $hash)));

		//  Change the password if the user is found and the hash is correct
		if ( ! empty($user_data))
		{
			// Define error string
			$error = '';
			
			// If trying to change the password validate the post data
			if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
			{
				// Get passwords from post
				$password = $this->input->post("password");
				$password_repeat = $this->input->post("password_repeat");
			
				// Check if the password is too short
				if (strlen($password) < 3)
				{
					$error = 'Password must be at least 3 symbols long';
				}
				
				// Check if the passwords match
				if ($password != $password_repeat)
				{
					$error = 'Passwords mismatch';
				}

				// If there are no error set the error string to OK
				if ($error == '')
				{
					$error = 'ok';
				}
			}
			
			// Check if there are errors
			if ($error != 'ok')
			{
				$vars = array('id_encoded' => $id_encoded, 'hash' => $hash, 'error' => $error);
				// Refresh the page with error message
				$this->view('user/newpass', $vars);
			}
			// Change the password if there are no validation errors			
			else
			{
				// Encrypt the password
				$data = array(
					"password"	=>	md5($this->input->post("password"))
				);
				// Update the new password to the database
				$this->users->save_user($data, $user_data['iduser']);

				// Load session
				$this->load->helper('session');
				
				// Login
				$_SESSION['user'] = $user_data;

				redirect("/");
			}
		}
		// If the user was not found or the hash wasn't correct redirect to index page
		else
		{
			redirect("/");
		}
	}
	
	/**
	* Confirm account warning - used when the user try to add project, but his account is not confirmed yet
	*
	* @access public
	*/
	public function confirm_account()
	{		
		// If the user is logged in - redirect to index page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Define variables array to be used in the view
		$vars = array();
		
		// If we have success message in the session pass it to the view
		if (!empty($_SESSION['success']))
		{
			$vars['success'] = $_SESSION['success'];
			unset($_SESSION['success']);
		}
		
		// Display confirm account template
		$this->view('user/confirmFirst', $vars);
	}
	
	/**
	* Resend verification email
	*
	* @access public
	*/
	public function resend_verification()
	{	
		// If the user is not logged in - redirect to index page
		if (empty($_SESSION['user']))
		{
			redirect("/");
		}
		
		// Get emails conifg
		$this->load->config('emails');
		
		// Get welcome / confirmation email
		$this->load->model('Emails_model', 'emails');
		$email_data = (array) reset($this->emails->get_emails(array("idemail" => "1")));

		// Get site title
		$this->load->model('Configuration_model', 'configuration');
		$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
		$site_title = reset($site_title);
		
		// Params that will be replaced in the email subject
		$title_params = array(
			"[site_name]"	=>	$site_title,
		);
		
		// Form activation link
		$link = $this->config->item('base_url')."user/confirm/".encode_string($_SESSION['user']['iduser']);

		// Params that will be replaced in the email text
		$text_params = array(
			"[site_name]"	=>	$site_title,
			"[link]"		=>	"<a href='".$link."'>".$link."</a>"
		);

		// Send confirmation email
		send_mail($this->config->item('FROM_EMAIL'), $_SESSION['user']['email'], $email_data['subject'], $email_data['text'], $title_params, $text_params);
		
		// Assign success message
		$_SESSION['success'] = "The verification email was sent";
		
		// Redirect to confirm account
		redirect("/user/confirm_account/");
	}
	
	/**
	* Login - display login form and when filled try to login
	*
	* @param string $refer Define the page to be opened after login
	* @access public
	*/
	public function login($refer = '')
	{
		// Define variables array to be sent to the view
		$vars = array();
		
		// Get parameters from the url
		$uri = $this->uri->segment_array();

		// If refer parameter is set in the url pass it to the session. Used to set the page to be opened after login.
		if (isset($uri[3]) && preg_match('/refer:/i', $uri[3])) 
		{
			$refer = str_replace(array('refer:', '+'), array('', '/'), $refer);
			$_SESSION['_refer'] = $refer;
		}

		// load users model
		$this->load->model('Users_model', 'users');
	
		// If the login form is submitted try to login
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{
			// Array that we will use to login the user
			$data = array(
				"email" 	=>	$this->input->post("username_email"),
				"password"	=>	md5($this->input->post("password")),
				"active"	=>	"1",
				//"confirmed" => 	"1"
			);
		
			// Try to get the user from email/password params
			$user_data = $this->users->get_users($data);

			if (!$user_data) {
				$data = array(
					"username" 	=>	$this->input->post("username_email"),
					"password"	=>	md5($this->input->post("password")),
					"active"	=>	"1",
					//"confirmed" => 	"1"
				);
				// Try to get the user from username/password params
				$user_data = $this->users->get_users($data);
			}

			// Login if we have found the user (the username and password are correct)
			if ($user_data)
			{			
				// Start user session
				$_SESSION['user'] = (array) reset($user_data);
				
				// Update last login
				$this->users->save_user(array("date_login" => date("Y-m-d H:i:s")), $_SESSION['user']['iduser']);
				
				//Retrieve new Alerts/Notifications for this user
				$this->load->model('Notifications_model', 'notifications');
				$_SESSION['info']['new_notifications'] = $this->notifications->get_all_new_events_count_for_member($_SESSION['user']['iduser']);
				
				//Retrieve new Messages for this user
				$_SESSION['info']['new_messages'] = $this->users->get_new_messages_count($_SESSION['user']['iduser']);
				
				//Check if user wants autologin
				$autologin = (int) $this->input->post('autologin');
				if ($autologin)
				{
					// Set autologin data
					$cookie_name = 'autologin_hash';
					$cookie_val = $_SESSION['user']['iduser'] . '|' . $_SESSION['user']['hash'];
					$cookie_domain = $this->config->item('cookie_domain');
					
					$cookie_arr = array(
						'name'   => $cookie_name,
						'value'  => $cookie_val,
						'expire' => 60*60*24*365, // 1 year
						'domain' => $cookie_domain,
						'path'   => '/',
						'prefix' => '',
						'secure' => FALSE
					);
					
					// Set autologin cookie
					$this->input->set_cookie($cookie_arr);					
				}				
				
				// Redirect to index page
				$vars['logged'] = TRUE;
				$vars['ajax'] = TRUE;
				$vars['message'] = 'You have successfully logged in';
				$this->view('user/closeframe', $vars);			
			}
			// If username or password are not correct
			else
			{
				// Assign error message
				$vars['errors'][] = "Wrong username or password";
			}
		}		
		
		// Display the login form (if it is not already submitted)
		if ( ! isset($vars['logged']) OR $vars['logged'] == FALSE)
		{
			if (uri_string() && preg_match('/ajax/', uri_string()))
			{
				$vars['ajax'] = TRUE;
			}
			
			$this->view('user/login', $vars);
		}
	}

	/**
	* Edit profile settings - allows the user to change his profile settings like password, email, bio etc.
	*
	* @access public
	*/
	public function profile()
	{
		// If the user is not logged in
		if (empty($_SESSION['user']))
		{
			redirect("/user/login/");
		}

		// Define variables array to be passed to the view
		$vars = array();
		
		// If we have errors in the sesson get them
		if ( ! empty($_SESSION['error']))
		{
			$vars['errors'][] = $_SESSION['error'];
			unset($_SESSION['error']);
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		// Load categories model
		$this->load->model('Categories_model', 'categories');
		// Get categories
		$vars['categories'] = $this->categories->get_categories(TRUE);
		
		// If trying to edit the profile (the form is submitted)
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{
			// Serialize the list of categories the user is interested in, to store them in the database
			if (isset($_POST['interests']) && $_POST['interests'])
				$_POST['interests'] = serialize($_POST['interests']);
			else
				$_POST['interests'] = serialize(array());
			
			// If updating password
			$new_password = $this->input->post("password");
			if ( ! empty($new_password))
			{
				// Check if the password is long enough
				if (strlen($new_password) < 3)
				{
					$vars['errors'][] = "Password can't be less than 3 symbols";				
				} 
				
				// If the confirm password doesn't match set an error
				$new_password_repeat = $this->input->post("password_repeat");
				if ($new_password != $new_password_repeat)
				{
					$vars['errors'][] = "Passwords don't match";
				}
				
				// Check if the old password is correct
				$old_password = $this->input->post("old_password");
				if ($_SESSION['user']['password'] != md5($old_password))
				{
					$vars['errors'][] = "Old password is wrong";					
				}
			}
			// If the password is not edited
			else
			{
				if (isset($_POST['password'])) unset($_POST['password']);
			}
			
			// Check for empty bio
			//if (!$this->input->post('bio')){
				//$vars['errors'][] = "Empty bio information";
			//}
			
			// Check for empty postcode
			//if (!$this->input->post('postcode')){
				//$vars['errors'][] = "Empty postcode";
			//}
			
			// Check for empty location
			//if (!$this->input->post('location')){
				//$vars['errors'][] = "Empty location";
			//}			
			
			// Check if slug is changing - if so edit it to be usable for links
			if (isset($_POST['slug']) && ! empty($_POST['slug']))
			{
				$_POST['slug'] = slugify($_POST['slug']);
			}
			
			// If the slug is already used set an error
			if (isset($_POST['slug']) && $_POST['slug'] != $_SESSION['user']['slug'])
			{
				// Check if the slug is not taken
				$check = $this->users->get_users(array("slug" => $this->input->post("slug")));
				if ( ! empty($check))
				{
					$check = reset($check);
					if ($check->iduser != $_SESSION['user']['iduser'])
					{
						$vars['errors'][] = "This Vanity URL is used by another user, please enter another one";
					}
				}
			}
			
			// Check if changing username
			if ($_POST['username'] != $_SESSION['user']['username'])
			{
				$_POST['username'] = slugify($_POST['username']);
				// Look if the new username is busy
				$username_check = $this->users->get_users(array("username" => $_POST['username']), "", array("from" => 0, "count" => 1));
				
				// Check username length
				if (strlen($this->input->post("username")) < 3)
				{
					$vars['errors'][] = "Username can't be less than 3 symbols";
				} 
				// If the username is already taken	set an error			
				else if ($username_check)
				{
					$vars['errors'][] = "The username is already taken by someone else";					
				}
				// If the username is free				
				else 
				{
					$data['username'] = $this->input->post("username");
				}
			}
			
			// Check if changing email
			if ($_POST['email'] != $_SESSION['user']['email'])
			{
				// Look if the new email is busy
				$email_check = $this->users->get_users(array("email" => $_POST['email']), "", array("from" => 0, "count" => 1));
				
				// Check if the email is valid
				if( ! check_email($this->input->post('email')))
				{
					$vars['errors'][] = "Invalid email address";					
				}
				// If the email is already taken set an error				
				else if($email_check)
				{
					$vars['errors'][] = "The email is already taken by someone else";					
				}
				// If the email is valid and free set it to be changed			
				else
				{
					$data['email'] = $this->input->post("email");
				}
			}
			
			// If the data is correct (we have no errors)
			if (empty($vars['errors']))
			{
				// Encrypt the password
				$new_password = $this->input->post("password");				
				if (!empty($new_password))
				{
					$data['password'] = md5($new_password);
				}

				// Add personal fields
				$data['name'] = $this->input->post("name");
				$data['slug'] = $this->input->post("slug");
				$data['bio'] = $this->input->post("bio");
				$data['postcode'] = $this->input->post("postcode");
				$data['location'] = $this->input->post("location");
				$data['alerts_own'] = $this->input->post("alerts_own");
				$data['alerts_backing'] = $this->input->post("alerts_backing");
				$data['alerts_watch'] = $this->input->post("alerts_watch");
				$data['newsletter'] = $this->input->post("newsletter");
				$data['interests'] = $_POST['interests'];
				
				// If postcode is set - get location data 
				if (isset($data['postcode']) && ! empty($data['postcode'])) {
					// Get location data
					require_once(APPPATH.'third_party/google/GoogleMapAPI.class.php');
					$gmap = new GoogleMapAPI();
					$coord = $gmap->getGeocode($_POST['postcode'] . ' UK', 1);				
					$location_data = $this->users->get_location_by_postcode($_POST['postcode']);
					
					// If we've detected the postcode set the location data
					$data['town_name'] = $location_data['town_name'];
					$data['county_name'] = $location_data['county_name'];
					$data['location_preview'] = $location_data['location_preview'];
					$data['lat'] = $location_data['lat'];
					$data['lng'] = $location_data['lng'];
				}
				
				// Update user data
				if ( ! empty($data))
				{			
					// Update user data in the database
					$this->users->save_user($data, $_SESSION['user']['iduser']);
					
					//Update notification settings
					$this->load->model('Notifications_model', 'notifications');
					$this->notifications->update_all_events_for_member($_SESSION['user']['iduser'], 'own', $data['alerts_own']);
					$this->notifications->update_all_events_for_member($_SESSION['user']['iduser'], 'support', $data['alerts_backing']);
					$this->notifications->update_all_events_for_member($_SESSION['user']['iduser'], 'watch', $data['alerts_watch']);					
					
					// Assign success message
					$vars['success'] = "Your profile was updated";
				}
			}
		}
		
		// Get user data array
		$vars['data'] = (array) reset($this->users->get_users(array("iduser" => $_SESSION['user']['iduser'])));
		// Set the user data to the session
		$_SESSION['user'] = $vars['data'];
		
		// Get user websites
		$vars['data']['websites_arr'] = $this->users->get_websites_arr($_SESSION['user']['iduser']);
		
		// Pass the POST data to the view (even if there are errors)
		foreach ($vars['data'] as $key => $val)
		{
			$temp = FALSE;
			$temp = $this->input->post($key);
			if ( ! empty($temp))
			{
				$vars['data'][$key] = $this->input->post($key);
			}
		}
		
		// Display profile template
		$this->view('user/profile', $vars);
	}
	
	/**
	* Logout
	* 
	* @access public
	*/
	public function logout()
	{
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Update last login
		$this->users->save_user(array("date_login" => date("Y-m-d H:i:s")), $_SESSION['user']['iduser']);
				
		// Remove user session
		unset($_SESSION['user']);
		
		// Remove the login cookie
		setcookie("PHPSESSID", "", time()-3600, "/");
		// Remove autologin cookie
		setcookie("autologin_hash", "", time()-3600, "/"); 
		
		// Redirect to index page
		redirect('/');
	}
	
	/**
	* Autocomplete username ( for example when writing new messge and choosing the receiver )
	* 
	* @access public
	*/
	public function autocomplete()
	{
		// Get username field value
		$username = $this->input->get("term");
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get usernames containing the username field value
		$users = $this->users->get_users(array("username" => $username."%", "username " => ' != '.$_SESSION['user']['username'])); //Leave the second key with empty space in order to preserve the two conditions of "username" field. 
		
		// Return array
		$return = array();
		
		// Create result array with the usernames
		foreach ($users AS $user)
		{
			$return[] = array(
				"id" => $user->iduser,
				"val" => $user->username,
				"label" => $user->username
			);
		}
		
		// Return JSON encoded results
		echo json_encode($return);		
	}
	
	/**
	* Forgotten password - if the user has forgotten his password he can enter his email address 
	* and receive email allowing him to change his password
	*
	* @access public
	*/
	public function forgotten_pass()
	{
		// Variables array to be used in the view
		$vars = array();
		
		// If the user email is submitted send forgotten password email
		if (strtolower($_SERVER['REQUEST_METHOD']) == "post")
		{		
			// Load users model
			$this->load->model('Users_model', 'users');

			// Try to find the user by email
			$user_data = reset($this->users->get_users(array("email" => $this->input->post("email"))));
			
			// Check if email syntax is valid
			if ( ! check_email($this->input->post('email')))
			{
				$vars['errors'][] = 'Invalid email address';
			} 
			// Check if the user with this email was found
			else if( ! $user_data)
			{
				$vars['errors'][] = 'The email address was not found in our system';
			}
			
			// If the email is valid send email
			if (empty($vars['errors']))
			{
				// Get emails config
				$this->load->config('emails');
				
				// Get the user id
				$user_id = $user_data->iduser;
				// Create link to set the new password for the user
				$confirmation_link = $this->config->item('base_url')."user/new_password/".encode_string($user_id)."/".$user_data->hash."/";				 
				
				// Get email data for forgotten password email
				$this->load->model('Emails_model', 'emails');
				$email_data = (array) reset($this->emails->get_emails(array("idemail" => "4")));

				// Get site title
				$this->load->model('Configuration_model', 'configuration');
				$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
				$site_title = reset($site_title);

				// Params array to be replaced in the email
				$params = array(
					"[site_name]"	=>	$site_title,
					"[username]"	=>	$user_data->username,
					"[link]"		=>  "<a href='".$confirmation_link."'>".$confirmation_link."</a>"
				);

				// Send forgotten password email
				send_mail($this->config->item('FROM_EMAIL'), $user_data->email, $email_data['subject'], $email_data['text'], $params, $params);

				// Assign success message
				$_SESSION['success'] = "Email with instructions was sent, please check your email address";
				
				// Redirect to index page
				$vars['logged'] = TRUE;
				$vars['ajax'] = TRUE;
				$vars['message'] = $_SESSION['success'];
				$this->view('user/closeframe', $vars);					
			}
		}
		
		// Display the forgotten password form (if it is not already submitted)
		if ( ! isset($vars['logged']) OR $vars['logged'] == FALSE)
		{
			if (uri_string() && preg_match('/ajax/', uri_string())) 
			{
				$vars['ajax'] = TRUE;
			}
			$this->view('user/forgotten_pass', $vars);
		} 
	}
	
	/**
	* Get location data by postcode using GoogleMapAPI
	*
	* @access public
	*/
	public function get_location_by_postcode()
	{	
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Load GoogleMapAPI
		require_once(APPPATH.'third_party/google/GoogleMapAPI.class.php');
		$gmap = new GoogleMapAPI();
		
		// Get location data by postcode
		$coord = $gmap->getGeocode($_POST['postcode'] . ' UK', 1);
		$location_data = $this->users->get_location_by_postcode($_POST['postcode']);
		
		// If we have detected postcode location data print it
		if ( ! empty($location_data))
			echo $location_data['location_preview'] . ', ' . $location_data['county_name'];
	}
	
	/**
	* Add profile image for the user 
	*
	* @access public
	*/
	public function add_image()
	{
		// Add some headers to prevent avatar caching
        $cache_time = mktime(0,0,0,15,2,2004);
        header("last-modified: " . gmdate("D, d M Y H:i:s", $cache_time) . " GMT");

        // Check if the user id is set from the form
		if ( ! empty($_POST['iduser']))
		{
			// Use the post data from the form only if administrator is logged in. If normal user is logged in we will use the session user id
			if (empty($_SESSION['admin']))
			{
				$_POST['iduser'] = $_SESSION['user']['iduser'];
			}
		}
		// Else get the user id from the session
		else
		{
			$_POST['iduser'] = $_SESSION['user']['iduser'];
		}
	
		// Get image extension
		$ext = strtolower(end(explode(".", $_FILES['File']['name'])));
		
		// Define error string
		$error = '';
		// Check that file size in not too big  
		if (($_FILES['File']['size'] / 2048) > 2048) 
		{
			$error = "Image size should be less than 2 MB";
		}

		// Check if the file type is supported
		else if ($ext != "jpg" && $ext != "gif" && $ext != "png") 
		{
			$error = "The image should be JPG, GIF or PNG";
		}

		// Form upload path
		$path = SITE_DIR."public/uploads/users/" . (int) $_POST['iduser'] . "." . $ext;

		// If we don't have errors upload the image
		if (empty($error))
		{
			// Upload image
			move_uploaded_file($_FILES['File']['tmp_name'], $path);
			
			// Make thumbs
			make_user_thumb($path);
			
			// Load users model
			$this->load->model('Users_model', 'users');
		
			// Update image extension in the database
			$this->users->save_user(array("ext" => $ext), (int) $_POST['iduser']);
			
			// Set the output
			$output = $_SESSION['user']['iduser'] .'.'. $ext.'?'.time(); //add anticaching
			
			// Prepare JSON output
			$result_arr = array(
				'success' => TRUE,
				'html' => $output
			);
			
			// Print JSON output
			echo json_encode($result_arr);
			exit();			
			
			// Show message
			echo $ext;		
		} 
		// If we have an error		
		else 
		{
			// Prepare JSON output
			$result_arr = array(
				'success' => FALSE,
				'msg' => $error
			);
			
			// Print JSON output
			echo json_encode($result_arr);
			exit();
			
			// Show error
			echo $error;
		}
	}	

	/**
	* Remove user profile image
	*
	* @access public
	*/
	public function remove_image()
	{
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Update user to remove the image
		$this->users->save_user(array('ext' => ''), $_SESSION['user']['iduser']);
	}			

	/**
	* Facebook connect - login with facebook account. It is called by a javascript (fb_implement.js) 
	* and gets data from facebook in the post array. If the user is not registered already there will be sign up.
	*
	* @access public
	*/
	public function fb_connect()
	{
		// If user id and email are not set show an error
		if ( ! isset($_POST['uid']) OR ! isset($_POST['email']) OR empty($_POST['uid']) OR empty($_POST['email']))
		{
			$response = json_encode(array("error" => TRUE));
			exit($response);
		}
		// If user id and email are set get them
		else
		{
			$profile['id'] = $_POST['uid'];
			$profile['email'] = $_POST['email'];
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Load sessions
		$this->load->helper('session');

		// Check if we can find user by Facebook id or email
		$user_data_fbid = $this->users->get_users(array("fbid" => $profile['id']));
		$user_data_email = $this->users->get_users(array("email" => $profile['email']));

		// If we've found the Facebook id - login the user
		if ($user_data_fbid)
		{
			$_SESSION['user'] = (array) reset($user_data_fbid);
			$response = json_encode(array("logged" => TRUE));
			exit($response);			
		}
		// If we've found the email - check Facebook id		
		else if ($user_data_email)
		{
			// Get Facebook id of the user from the database
			$user_data_email = (array) reset($user_data_email);
			$fbid = $user_data_email['fbid'];
			
			// If profile Facebook id is different from the one received from Facebook now return error
			if (($fbid != $profile['id']) && !empty($fbid))
			{
				$response = json_encode(array("error" => TRUE));
				exit($response);				
			} 
			// If the ids are the same - login			
			else 
			{
				// Set user data to the session (login)
				$_SESSION['user'] = $user_data_email;
				
				//Update the Facebook ID if empty
				if (empty($fbid))
				{
					$this->users->save_user(array("fbid" => $profile['id']), $_SESSION['user']['iduser']);
					$_SESSION['user']['fbid'] = $profile['id'];
				}
				
				// Return success
				$response = json_encode(array("logged" => TRUE));
				exit($response);
			}
		}
		// If we can't find the user by Facebook id or email - we will create the account		
		else
		{
			// Create username
			if ( ! isset($_POST['username']) OR empty($_POST['username']))
			{
				// If username is set 'slugify' it
				if (isset($_POST['name']) && !empty($_POST['name']))
				{
					$_POST['username'] = slugify($_POST['name']);
					$_POST['username'] = str_replace('-', '.', $_POST['username']);
				}
				// If username is not set use the email to create one
				else
				{
					$email_parts_arr = explode('@', $profile['email']);
					$_POST['username'] = $email_parts_arr[0];
				}
			}
		
			// Set user data to be inserted in database
			$data = array();
			$data['email'] = $profile['email'];
			$data['fbid'] = $profile['id'];
			$data['username'] = isset($_POST['username']) ? $_POST['username'] : '';
			$data['slug'] = $data['username'];
			$data['name'] = isset($_POST['name']) ? $_POST['name'] : '';
			$data['date_register'] = date('Y-m-d H:i:s');
			$data['date_login'] = date('Y-m-d H:i:s');
			$member_password_clean = rand(10000, 99999);
			$data['password'] = md5($member_password_clean);			
			
			if (empty($data['username']))
			{
				$data['username'] == slugify($data['name']);
			}			
			
			// Insert the new user in the database
			$this->users->add_user($data);
			// Get the id of the new user
			$iduser = $this->db->insert_id();
							
			//Set profile image extension to default one
			$ext = strtolower(end(explode(".", DEFAULT_USER_IMAGE)));

			// Form upload path for profile image
			$path = SITE_DIR."public/uploads/users/$iduser." . $ext;
			// Set the default user profile image to the user
			copy(DEFAULT_USER_IMAGE, $path);
			chmod($path, 0777);
			
			// Get the new user data by email
			$user_data_email = $this->users->get_users(array("email" => $profile['email']));
			$user_data_email = (array) reset($user_data_email);
			// Set the user data to the session (login)
			$_SESSION['user'] = $user_data_email;
			
			//Create Vanity URL / slug
			$slug = $data['username'];
			$check_arr = $this->users->get_users(array("slug" => $slug));
			$check = count($check_arr);
			// If the slug is already used generate new one
			if ($check > 1)
			{
				do 
				{
					// Generate new slug
					$slug .= "_1";
				
					// Check if the new slug is free. If not we will repeat the action
					$check_arr = $this->users->get_users(array("slug" => $slug));
					$check = count($check_arr);
				} while ($check != 0);
			}
			
			// Try getting Facebook Image
			if (isset($_POST['pic_big']) && !empty($_POST['pic_big']))
			{
				$ext = $this->users->get_user_fb_pic($_POST['pic_big'], $iduser);
			}
		
			//Set profile image extension
			$this->users->save_user(array('slug' => $slug, 'ext' => $ext), $_SESSION['user']['iduser']);			
		
			//Send welcome email
				// Get emails model
				$this->load->model('Emails_model', 'emails');
				// Get welcome message (for Facebook sign up)
				$email_data = (array) reset($this->emails->get_emails(array("idemail" => "10"))); //Wellcome from Facebook

				// Get site title
				$this->load->model('Configuration_model', 'configuration');
				$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
				$site_title = reset($site_title);
				
				// Params that will be replaced in email subject
				$title_params = array(
					"[site_name]"	=>	$site_title,
				);				
				
				// Get emails config
				$this->load->config('emails');

				// Form activation link
				$iduser = $_SESSION['user']['iduser'];
				$link = $this->config->item('base_url')."user/confirm/".encode_string($iduser);

				// Params that will be replaced in the email text
				$text_params = array(
					"[site_name]"	=>	$site_title,
					"[link]"		=>	"<a href='".$link."'>".$link."</a>",
					"[password]" 	=>  $member_password_clean,
					"[email]"		=>  $data['email'],
					"[username]"	=>  $data['username']
				);

				// Send welcome email
				send_mail($this->config->item('FROM_EMAIL'), $data['email'], $email_data['subject'], $email_data['text'], $title_params, $text_params);

			// Output success
			$response = json_encode(array("logged" => TRUE));
			exit($response);
		}
	}

	/**
	* If there is an error with the facebook login (using fb_connect method) this method will be called
	*
	* @access public
	*/
	public function fb_error()
	{
	
		// Assign error message
		$vars['message'] = "The requested email is already registered in our system";
		$vars['ajax'] = TRUE;
		
		// Display error template
		$this->view('user/fb_error', $vars);
		
	}

	/**
	* After the Facebook sign up this method is called to display sign up output 
	*
	* @access public
	*/
	public function fb_sign_up()
	{
		// If the user is logged in - redirect to index page
		if (!empty($_SESSION['user']))
		{
			redirect("/");
		}		
		
		// Load Facebook helper
		$this->load->helper('facebook');
		// Get facebook profile data
		$profile = NULL;
		if ($session = $this->facebook->getSession()) 
		{
			try 
			{
				$profile = $this->facebook->api('/me');
			} 
			catch (FacebookApiException $e) {}
		}
		
		// User is not logged in facebook - redirect to sign up
		if ( ! $profile) 
		{
			redirect('/user/sign_up/');
		}
		
		// Set sign up data to be displayed in the view
		$vars['post']['email'] = $profile['email'];
		$vars['post']['username'] = $profile['username'];
		$vars['post']['fbid'] = $profile['id'];

		// Display signup template
		$this->view('user/signupfb', $vars);
	}
	
	/**
	* Connect the current user profile to Facebook account 
	*
	* @access public
	*/
	public function add_facebook()
	{
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get Facebook id
		$fbid = $_POST['uid'];
		
		// Check if we have user with this id
		$user_data = $this->users->get_users(array("fbid" => $fbid));
		
		// If we have such user - redirect back with error
		if ( ! empty($user_data))
		{
			$_SESSION['error'] = "You are not allowed to have more than one accounts connected with your Facebook";
		}
		// Connect the profile with the Facebook account
		else 
		{
			$this->users->save_user(array("fbid" => $fbid), $_SESSION['user']['iduser']);
		}
	}
	
	/**
	* Disconnect user profile from the Facebook account 
	*
	* @access public
	*/
	public function remove_facebook()
	{
		// Load users model
		$this->load->model('Users_model', 'users');

		// Set message
		$_SESSION['error'] = "Your Facebook profile is now disconnected";
		// Disconnect the profile from Facebook
		$this->users->save_user(array("fbid" => "0"), $_SESSION['user']['iduser']);
	}
	
	/**
	* Add user profile website (used when editing profile)
	*
	* @access public
	*/
	public function add_website()
	{
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get user website
		$website = $this->input->post("website");
		// Prepare link - check for invalid symbols and handle the protocol, then add the website to the user profile.
		if (prepare_link($website, TRUE))
		{
			$this->users->add_website(prepare_link($website, TRUE), $_SESSION['user']['iduser']);
		}
		// Get user websites
		$websites_arr = $this->users->get_websites_arr($_SESSION['user']['iduser']);
		$return = '';
		// If there are websites loop through them and generate an output
		if ( ! empty($websites_arr))
		{
			foreach ($websites_arr as $i => $website)
			{
				$return .= '<a class="delete_website" href="javascript:deleteWebsiteRecord(\''.$website.'\');">Delete</a> | <a href="http://'.$website.'" target="_blank">'.$website.'</a><br />'."\n";	
			}
		}
		// Return the output
		echo $return;
	}
	
	/**
	* Delete a website from user profile
	*
	* @access public
	*/
	public function delete_website()
	{
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get the website to be deleted
		$website = $this->input->post("website");

		// Delete the website
		$this->users->delete_website($website, $_SESSION['user']['iduser']);
		
		// Get all websites for the user
		$websites_arr = $this->users->get_websites_arr($_SESSION['user']['iduser']);
		$return = '';
		// Loop through the websites and generate an output
		if ( ! empty($websites_arr))
		{
			foreach($websites_arr as $i => $website) 
			{
				$return .= '<a class="delete_website" href="javascript:deleteWebsiteRecord(\''.$website.'\');">Delete</a> | <a href="http://'.$website.'" target="_blank">'.$website.'</a><br />'."\n";	
			}
		}
		// Return the output
		echo $return;
	}	

	/**
	* Public profile - show the public profile of a user that can be seen by all the visitors
	*
	* @access public
	*/
	public function public_profile($user)
	{
		// Variables array to be passed to the view
		$vars = array();
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get wall events
		$vars['events'] = $this->users->get_wall_events($user->iduser, array("from" => 0, "count" => 5));

		// Set facebook title and description
		$vars['fb_title'] = ''; //$user->name;
		$vars['fb_description']	= $user->bio;
		
		// Set vars
		$vars['user'] = $user;
		$vars['tab'] = "profile";		
		
		// Show template
		$this->view('user/public_profile', $vars);
	}
	
	/**
	* Get user wall events 
	*
	* @access public
	*/
	public function get_events()
	{	
		// Get user id
		$iduser = intval($_POST['iduser']);
		
		// Get pagination params
		$limit['from'] = $_POST['from'];
		$limit['count'] = $_POST['count'];

		// Get wall events from the database
		$vars['events'] = $this->users->get_wall_events($iduser, $limit);
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Try to get user data
		$vars['user'] = reset($this->users->get_users(array("iduser" => $iduser)));
		
		// Show results
		$this->load->view('user/public_profile_events', $vars);
	}	
	
	/**
	* Show the projects (watched, backed and started) of a user. The method is called from the _remap method.
	*
	* @access public
	* @param string $user The user whose projets will be shown.
	*/
	public function public_projects($user)
	{	
		// Variables array to be passed to the view
		$vars = array();
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// If the current user is looking at his own profile 
		if (isset($_SESSION['user']) && $_SESSION['user']['iduser'] == $user->iduser)
		{
			// Get 4 projects the user is watching
			$this->projects->limit = 4;
			$vars['watched'] = $this->projects->get_public_watching_projects($user->iduser);
			
			// Get 4 projects the user is backing
			$this->projects->limit = 4;
			$this->projects->owner = TRUE;
			$vars['backed'] = $this->projects->get_public_backed_projects($user->iduser);
			
			// Get 4 projects the user has started
			$this->projects->limit = 4;
			$this->projects->owner = TRUE;
			$vars['started'] = $this->projects->get_public_started_projects($user->iduser);
		}
		else
		{
			// Get 4 projects the user is backing
			$this->projects->limit = 4;
			$this->projects->owner = FALSE;
			$vars['backed'] = $this->projects->get_public_backed_projects($user->iduser);
			
			// Get 4 projects the user has started
			$this->projects->limit = 4;
			$this->projects->owner = FALSE;
			$vars['started'] = $this->projects->get_public_started_projects($user->iduser);
		}

		// Set variables for the view
		$vars['user'] = $user;
		$vars['tab'] = 'projects';
		// Show the view
		$this->view('user/public_projects', $vars);
	}
	
	/**
	* Show the started projects of a user. The method is called from the _remap method.
	*
	* @access public
	* @param string $user The user whose projets will be shown.
	*/
	public function public_started_projects($user)
	{
		// Get current URL parts
		$uri = $this->uri->segment_array();
		
		// Get page
		$page = ( ! empty($uri[6])) ? intval($uri[6]) : 0;
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Variables array to be used in the view
		$vars = array();
		$vars['searching'] = TRUE;
		$vars['user'] = $user;
		$vars['tab'] = "projects";
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');

		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		$pager['base_url'] = site_url('/user/'.$user->slug.'/projects/started/page');
		$pager['total_rows'] = 0;
		if(strpos($this->uri->uri_string(), "page/all")){
			$pager['per_page'] = 600;
		} else {
			$pager['per_page'] = 20;
		}
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page'] + 1);
		
		// Get projects
		$vars['projects'] = array();
		
		// Display template
		$this->view('user/public_projects_all', $vars);
	}
	
	/**
	* Show the backed projects of a user. The method is called from the _remap method.
	*
	* @access public
	* @param string $user The user whose projets will be shown.
	*/
	public function public_backed_projects($user)
	{	
		// Get current URL parts
		$uri = $this->uri->segment_array();
		
		// Get page
		$page = (!empty($uri[6])) ? intval($uri[6]) : 0;
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Variables array to be used in the view
		$vars = array();
		$vars['searching'] = TRUE;
		$vars['user'] = $user;
		$vars['tab'] = "projects";
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');

		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		$pager['base_url'] = site_url('/user/'.$user->slug.'/projects/backed/page');
		$pager['total_rows'] = 0;
		if(strpos($this->uri->uri_string(), "page/all")){
			$pager['per_page'] = 600;
		} else {
			$pager['per_page'] = 20;
		}
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page'] + 1);
		
		// Get projects
		$vars['projects'] = array();
		
		// Display template
		$this->view('user/public_projects_all', $vars);
	}
	
	/**
	* Show the watched projects of a user. The method is called from the _remap method.
	*
	* @access public
	* @param string $user The user whose projets will be shown.
	*/
	public function public_watched_projects($user)
	{	
		// Get current URL parts
		$uri = $this->uri->segment_array();
		
		// Get page
		$page = (!empty($uri[6])) ? intval($uri[6]) : 0;
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Variables array to be used in the view
		$vars = array();
		$vars['searching'] = TRUE;
		$vars['user'] = $user;
		$vars['tab'] = "projects";
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');

		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		$pager['base_url'] = site_url('/user/'.$user->slug.'/projects/watched/page');
		$pager['total_rows'] = 0;
		if(strpos($this->uri->uri_string(), "page/all")){
			$pager['per_page'] = 600;
		} else {
			$pager['per_page'] = 20;
		}
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page'] + 1);
		
		// Get projects
		$vars['projects'] = array();
		
		// Display template
		$this->view('user/public_projects_all', $vars);
	}
	
	/**
	* Set a project as watched from the current user, so he will get alerts when there are new events of this project
	*
	* @access public
	* @param string $object_id The project to be watched
	*/
	public function watch_project($object_id)
	{
		// Get the current user id
		$member_id = (int)$_SESSION['user']['iduser'];
		// If the user and project are valid
		if ($object_id > 0 && $member_id > 0)
		{
			// Load projects model
			$this->load->model('Projects_model', 'projects');
			
			// Try to get the project to check it
			$project_check = reset($this->projects->get_projects(array("idproject" => $object_id)));

			// If the project is found
			if ( ! empty($project_check))
			{
				// Get the current user id
				$member_id = (int)$_SESSION['user']['iduser'];
				// Config notification settings
				$object_type = 'project';
				$object_role = 'watch';
				$notification_type = @$_SESSION['user']['alerts_watch']; //Type of notifications where user is watcher of the object
			
				// Set notification settings for this user to watch the project
				$this->load->model('Notifications_model', 'notifications');
				$this->notifications->configure_event_for_member($member_id, $object_id, $object_role, $object_type, 'comment', $notification_type);
				$this->notifications->configure_event_for_member($member_id, $object_id, $object_role, $object_type, 'update', $notification_type);
				$this->notifications->configure_event_for_member($member_id, $object_id, $object_role, $object_type, 'status_change', $notification_type);
			
				//redirect to project
				$slug = $project_check->slug;				
				redirect("/$slug/");
			}
		}
		
		// If project or user are not valid redirect to index page
		redirect("/");
	}
	
	/**
	* Set a project as unwatched for the current user, so he won't get alerts when there are new events of this project anymore
	*
	* @access public
	* @param string $object_id The project to be unwatched
	*/
	public function unwatch_project($object_id)
	{
		// Get the current user id
		$member_id = (int)$_SESSION['user']['iduser'];
		// If the user and project are valid
		if ($object_id > 0 && $member_id > 0)
		{
			// Get the current user id
			$this->load->model('Projects_model', 'projects');
			
			// Try to get the project to check it
			$project_check = reset($this->projects->get_projects(array("idproject" => $object_id)));
		
			// If the project is found
			if (!empty($project_check))
			{
				// Config notification settings
				$object_type = 'project';
				$object_role = 'watch';
				
				// Set notification settings for this user to unwatch the project
				$this->load->model('Notifications_model', 'notifications');
				$this->notifications->remove_all_events_for_member($member_id, $object_id, $object_role, $object_type);
				
				//redirect to project
				$slug = $project_check->slug;				
				redirect("/$slug/");
			}
		}
		
		// If project or user are not valid redirect to index page
		redirect("/");
	}
}

/* End of file user.php */ 
/* Location: ./application/controllers/user.php */