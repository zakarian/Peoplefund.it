<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

// Include oauth lib
include_once(APPPATH.'third_party/oauth-php/library/OAuthStore.php');
include_once(APPPATH.'third_party/oauth-php/library/OAuthRequester.php');

/** 
* Connect controller 
* Handles connection to other sites services like Energyshare allowing the users to login with their Energyshare accounts 
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Connect extends Base_Controller {
	
	/**
	* Redirects to 404 not found page
	*  
	* @access public
	*/	
	public function index()
	{
		redirect('/404/');
	}
	
	/**
	* Allows the users to login on the site with their accounts on other sites like Energyshare via OAuth
	*
	* @access public
	*/
	public function to()
	{
		// Declaring vars array to be used in view
		$vars = array();

		// Get parameters from the uri
		$uri = $this->uri->segment_array();

		// Check if we use popup to open login form
		$ajax = end($uri) == 'ajax' ? TRUE : NULL;

		// Load connect model
		$this->load->model('Connect_model', 'connect');
		// Load users model
		$this->load->model('Users_model', 'users');

		// Try to get service according to the parameters in the uri
		$service = $this->connect->check_service($uri);

		// If the service is not found redirect to login
		if( ! $service)
			redirect('/user/login/'.(isset($ajax)?'ajax/':''));

		// Get the config options related to this service
		$options = $this->config->item('options');
		
		if (isset($options[$service]))
			$options = $options[$service];
		else
			redirect('/user/login/'.(isset($ajax)?'ajax/':''));

		// Detect if we are using an ajax frame and rewrite oAuth server uris
		if (isset($ajax)) 
		{
			$options['request_token_uri'] .= 'ajax/';
			$options['authorize_uri'] .= 'ajax/';
			$options['access_token_uri'] .= 'ajax/';
			$options['callback_uri'] .= 'ajax/';
		}

		// When we use MySQL storage, we need to provide DB config data.
		$options['server'] = $this->db->hostname;
		$options['username'] = $this->db->username;
		$options['password'] = $this->db->password;
		$options['database'] = $this->db->database;

		// Define temp dir for OAuth
		define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath(@$_ENV["TMP"]));
		
		// Get OAuthStore instance and set it to store data in the session
		OAuthStore::instance("Session", $options );

		// Try to login with OAuth
		try
		{
			// STEP 1: If we do not have an OAuth token yet, go get one
			if (empty($_GET["oauth_token"])) 
			{
				// If access denied reditect to login
				if( ! empty($_GET['denied']))
					redirect('/user/login/'.(isset($ajax)?'ajax/':''));

				// Set OAuth parameters
				$getAuthTokenParams = array(
					'scope' => $options['request_token_uri'],
					'xoauth_displayname' => 'Oauth',
					'oauth_callback' => $options['callback_uri']);

				// Get a request token
				$tokenResultParams = OAuthRequester::requestRequestToken($options['consumer_key'], 0, $getAuthTokenParams);

				// Redirect to the other site (e.g. Energyshare) authorization page, they will redirect back
				header("Location: " . $options['authorize_uri'] . "?btmpl=mobile&oauth_token=" . $tokenResultParams['token']);
			} 
			// STEP 2: When we have taken OAuth token from the other site (e.g. Energyshare)
			else 
			{
				// Get an access token
				$oauthToken = $_GET["oauth_token"];

				$tokenResultParams = $_GET;

				// Try to exchange request token for access token from the site we are connecting
				try 
				{
					OAuthRequester::requestAccessToken($options['consumer_key'], $oauthToken, 0, 'POST', $_GET);
				} 
				catch (OAuthException2 $e) 
				{
					redirect('/user/login/'.(isset($ajax)?'ajax/':''));
				}

				// Make the profile request
				$request = new OAuthRequester($options['api_uri'] . "profile", 'GET', $tokenResultParams);
				$result = $request->doRequest(0);
				
				// We have a success result from the OAuth request
				if ($result['code'] == 200) 
				{
					// Get the user data from OAuth
					$member = (array) json_decode($result['body']);
					
					// Just check if member is empty (this is 99.9% not possible :))
					if (empty($member))
						redirect('/user/login/'.(isset($ajax)?'ajax/':''));
					
					// Try to search linked user in the database
					$user_data = $this->users->get_users(array("esid" => $member['id']));
					
					// We found linked account - login
					if ( ! empty($user_data))
					{
						// Load sessions
						$this->load->helper('session');

						// If user is already logged redirect to profile and set error message
						if (isset($_SESSION['user']['iduser'])) 
						{
							if($user_data['iduser'] != $_SESSION['user']['iduser'])
								$_SESSION['error'] = "The energyshare profile you are trying to use is already connected to another energyshare account";
							else
								$_SESSION['error'] = "Your profile is already connected with energyshare";

							redirect('/user/profile/');
						}

						// Start user session
						$_SESSION['user'] = (array) reset($user_data);
						
						// Update last login
						$this->users->save_user(array("date_login" => date("Y-m-d H:i:s")), $_SESSION['user']['iduser']);
						
						// Retrieve new Alerts/Notifications
						$this->load->model('Notifications_model', 'notifications');
						$_SESSION['info']['new_notifications'] = $this->notifications->get_all_new_events_count_for_member($_SESSION['user']['iduser']);
						
						// Retrieve new Messages for this user
						$_SESSION['info']['new_messages'] = $this->users->get_new_messages_count($_SESSION['user']['iduser']);
						
						// Redirect to index page
						if (isset($ajax)) 
						{
							$vars['logged'] = TRUE;
							$vars['ajax'] = TRUE;
							$vars['message'] = 'You have successfully logged in';
							$this->view('user/closeframe', $vars);
						}
						else 
						{
							redirect('/user/'.$data['username'].'/');
						}
					}
					// We don't find local account - register
					else
					{
						// First we need to check for logged user
						if (isset($_SESSION['user']['iduser'])) 
						{
							// If the user profile is connected with Energyshare
							if ( ! empty($_SESSION['user']['esid'])) 
							{
								$_SESSION['error'] = "Your profile is already connected with energyshare";
							}
							// If not update the database to connect it to Energyshare
							else 
							{
								$_SESSION['error'] = "Your profile is now connected with energyshare";
								$this->users->save_user(array("esid" => $member['id']), $_SESSION['user']['iduser']);
							
								$_SESSION['user']['esid'] = $member['id'];
							}
							
							// Redirect to user profile
							redirect('/user/profile/');
						}
						
						// Check if we can find stored email
						$user_data_email = $this->users->get_users(array("email" => $member['email']));

						if ( ! empty($user_data_email))
						{
							// Check ES id
							$user_data_email = (array) reset($user_data_email);
							$esid = $user_data_email['esid'];

							// If profile ES id is different from the one received from ES now
							if (($esid != $member['id']) && ! empty($esid))
							{
								$response = json_encode(array("error" => TRUE));
								exit($response);

							// If ids are the same - login
							} 
							else 
							{
								// Store the user data in the session
								$_SESSION['user'] = $user_data_email;

								//Update the ES id if empty
								if (empty($esid))
								{
									$this->users->save_user(array("esid" => $member['id']), $_SESSION['user']['iduser']);
									$_SESSION['user']['esid'] = $member['id'];
								}

								// Redirect to index page
								if (isset($ajax)) 
								{
									$vars['logged'] = TRUE;
									$vars['ajax'] = TRUE;
									$vars['message'] = 'You have successfully logged in';
									$this->view('user/closeframe', $vars);
								} 
								else 
								{
									redirect('/user/'.$data['username'].'/');
								}
							}
						}
						// If we can't find the user by any criterium - we will create the account
						else 
						{
							// Set username for the new user record
							if ( ! empty($member['username']))
								$username = $member['username'];
							elseif ( ! empty($member['name']))
								$username = str_replace('-', '.', slugify($member['name']));
							else 
							{
								$email_parts_arr = explode('@', $member['email']);
								$username = $email_parts_arr[0];
							}
						
							// Set the data for the new user record
							$data = array();
							$data['email'] = $member['email'];
							$data['esid'] = $member['id'];
							$data['username'] = isset($username) ? $username : '';
							$data['slug'] = $data['username'];
							$data['name'] = isset($member['name']) ? $member['name'] : '';
							$data['date_register'] = date('Y-m-d H:i:s');
							$data['date_login'] = date('Y-m-d H:i:s');
							// Generate random password
							$member_password_clean = rand(10000, 99999);
							$data['password'] = md5($member_password_clean);
							
							// Create new user
							$this->users->add_user($data);
							// Get the id of the new user
							$iduser = $this->db->insert_id();

							//Set profile image to default one
							$ext = strtolower(end(explode(".", DEFAULT_USER_IMAGE)));

							// Form upload path for user image
							$path = SITE_DIR."public/uploads/users/$iduser.".$ext;
							// Copy the default user image to the users uploads folder
							copy(DEFAULT_USER_IMAGE, $path);
							chmod($path, 0777);
							
							// Store user data in session to login
							$user_data_email = $this->users->get_users(array("email" => $member['email']));
							$user_data_email = (array) reset($user_data_email);
							$_SESSION['user'] = $user_data_email;
							
							// Create Vanity URL / slug
							$slug = $data['username'];
							// Check if the slug is already used by another user
							$check_arr = $this->users->get_users(array("slug" => $slug));
							$check = count($check_arr);
							// If the slug is not free generate a new one
							if ($check > 1)
							{
								do 
								{
									// Generate new slug
									$slug .= "_1";
									
									// If the new slug is also already used generate new one
									$check_arr = $this->users->get_users(array("slug" => $slug));
									$check = count($check_arr);
								} while ($check != 0);
							}
							
							// Try to get image from the site we are connecting
							if (isset($member['picture']) && ! empty($member['picture']))
							{
								// Get the image url
								if(strstr($member['picture'], "/var/members"))
									$member['picture'] = 'http://www.energyshare.com' . $member['picture'];
								// Get the image, copy it to the users upload dir and get the file extension 
								$ext = $this->users->get_user_es_pic($member['picture'], $iduser);
							}
						
							// Set profile image by setting the file extension to the database
							$this->users->save_user(array('slug' => $slug, 'ext' => $ext), $_SESSION['user']['iduser']);
													
							// Send welcome email
								// Get email data
								$this->load->model('Emails_model', 'emails');
								$email_data = (array) reset($this->emails->get_emails(array("idemail" => "12"))); // Welcome from energyshare email

								// Get site title
								$this->load->model('Configuration_model', 'configuration');
								$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
								$site_title = reset($site_title);
								
								// Params that will be replaced in title
								$title_params = array(
									"[site_name]"	=>	$site_title,
								);								
								
								// Get config object
								$this->load->config('emails');

								// Form activation link
								$iduser = $_SESSION['user']['iduser'];
								$link = $this->config->item('base_url')."user/confirm/".encode_string($iduser);

								// Params that will be replaced in text
								$text_params = array(
									"[site_name]"	=>	$site_title,
									"[link]"		=>	"<a href='".$link."'>".$link."</a>",
									"[password]" 	=>  $member_password_clean,
									"[email]"		=>  $data['email'],
									"[username]"	=>  $data['username']
								);

								// Send confirmation email
								send_mail($this->config->item('FROM_EMAIL'), $data['email'], $email_data['subject'], $email_data['text'], $title_params, $text_params);

							// Redirect to index page
							if (isset($ajax)) 
							{
								$vars['logged'] = TRUE;
								$vars['ajax'] = TRUE;
								$vars['message'] = 'You have successfully logged in';
								$this->view('user/closeframe', $vars);
							}
							else 
							{
								redirect('/user/'.$data['username'].'/');
							}
						}
					}
				}
				// OAuth request was not successful so redirect to login
				else
				{
					redirect('/user/login/'.(isset($ajax)?'ajax/':''));
				}
			}
		}
		// If a problem occurs redirect to login
		catch (OAuthException2 $e) 
		{
			redirect('/user/login/'.(isset($ajax)?'ajax/':''));
		}
	}
	
	/**
	* Remove Energyshare account
	*
	* @access public
	*/
	public function remove()
	{
		// If user is not logged in redirect to login
		if ( ! isset($_SESSION['user']['iduser']))
			redirect('/user/login/');

		// Get uri parameters
		$uri = $this->uri->segment_array();

		// Load connect model
		$this->load->model('Connect_model', 'connect');

		// Try to get service
		$service = $this->connect->check_service($uri);

		if( ! $service) 
		{
			redirect('/user/profile/');
		}

		// Get the config options related to this service
		$options = $this->config->item('options');
		
		if(isset($options[$service]))
			$options = $options[$service];
		else 
		{
			$_SESSION['error'] = 'An unexpected error occurred when attempting to delink your account from energyshare.';
			redirect('/user/profile/');
		}

		// Detect if we are using an ajax frame and rewrite oAuth server uris
		if(isset($ajax)) 
		{
			$options['request_token_uri'] .= 'ajax/';
			$options['authorize_uri'] .= 'ajax/';
			$options['access_token_uri'] .= 'ajax/';
			$options['callback_uri'] .= 'ajax/';
		}

		// We need to rewrite callback url
		$options['callback_uri'] = str_replace('/to/', '/remove/', $options['callback_uri']);

		// When we use MySQL storage, we need to provide DB config data.
		$options['server'] = $this->db->hostname;
		$options['username'] = $this->db->username;
		$options['password'] = $this->db->password;
		$options['database'] = $this->db->database;

		// Define temp dir for OAuth
		define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath(@$_ENV["TMP"]));
		
		// Get OAuthStore instance and set it to store data in the session
		OAuthStore::instance("Session", $options );
		
		// Try to login with OAuth, to see if the account is linked with Energyshare and and if so unlink it
		try
		{
			//  STEP 1:  If we do not have an OAuth token yet, go get one
			if (empty($_GET["oauth_token"])) 
			{
				if( ! empty($_GET['denied']))
					redirect('/user/profile/');

				$getAuthTokenParams = array('scope' => $options['request_token_uri'],
						'xoauth_displayname' => 'Oauth',
						'oauth_callback' => $options['callback_uri']);

				// get a request token
				$tokenResultParams = OAuthRequester::requestRequestToken($options['consumer_key'], 0, $getAuthTokenParams);

				//  redirect to the energyshare authorization page, they will redirect back
				header("Location: " . $options['authorize_uri'] . "?btmpl=mobile&oauth_token=" . $tokenResultParams['token']);
				exit;
			}
			// STEP 2: We have taken OAuth token from the other site (e.g. Energyshare)
			else 
			{
				$oauthToken = $_GET["oauth_token"];

				$tokenResultParams = $_GET;

				// Try to exchange request token for access token from the site we are connecting				
				try 
				{
					OAuthRequester::requestAccessToken($options['consumer_key'], $oauthToken, 0, 'POST', $_GET);
				} 
				catch (OAuthException2 $e) 
				{
					$_SESSION['error'] = 'An unexpected error occurred when attempting to delink your account from energyshare.';
					redirect('/user/profile/');
				}

				// Make the profile request
				$request = new OAuthRequester($options['api_uri'] . "disconnect", 'GET', $tokenResultParams);
				$result = $request->doRequest(0);
				
				// We have a success result from the OAuth request
				if ($result['code'] == 200) 
				{
					// Decode the data taken from OAuth
					$data = (array) json_decode($result['body']);

					// If connecton was successful
					if($data['success'] == 'true') 
					{
						if($service == 'energyshare') 
						{
							// Disconnect the profile from Energyshare
							$_SESSION['error'] = "Your energyshare profile is now disconnected";
							$this->users->save_user(array("esid" => "0"), $_SESSION['user']['iduser']);
							
							$_SESSION['user']['esid'] = 0;
						}
					} 
					else 
					{
						$_SESSION['error'] = 'An unexpected error occurred when attempting to delink your account from energyshare.';
					}
				} 
				else 
				{
					$_SESSION['error'] = 'An unexpected error occurred when attempting to delink your account from energyshare.';
				}
			}
		}
		catch(OAuthException2 $e) 
		{
			$_SESSION['error'] = 'An unexpected error occurred when attempting to delink your account from energyshare.';
		}

		// Redirect to user profile
		redirect('/user/profile/');
	}
}

/* End of file connect.php */
/* Location: ./application/controllers/connect.php */