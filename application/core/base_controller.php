<?php
/**
 * Custom abstract controller that extends the core CodeIgniter controller class
 * and that all controllers will extend. 
 * Handles user authorization and defines view method to display the views with header 
 * and footer and also defines site texts as constants. Retrieves new messages and alerts.
 *
 * @package PeopleFund
 * @category Administration
 * @author MTR Design
 * @link http://peoplefund.it
 */
abstract class Base_Controller extends CI_Controller {

	/**
	* Abstract controller constructor - handles user authorization and defines site texts as constants. 
	* Retrieves new messages and alerts.
	*
	* @access public
	*/
	public function __construct() {
		parent::__construct();

		// Load session and cookies
		$this->load->helper('session');
		$this->load->helper('cookie');
		
		// Define site texts as constants
		if (isset($this->db) && $this->db) 
		{
			// Get all items from texts table in database
			$vars['items'] = $this->db->query('	
				SELECT t.* 
				FROM `texts` AS t
				ORDER BY date DESC
			')->result();
		
			// Define each text as constant
			foreach ($vars['items'] as $item) 
			{
				define($item->key, $item->text);
			}		 
		}
		
		// If the user is not logged in check for autologin cookie - if such exists try to login
		if (empty($_SESSION['user']))
		{
			//Autologin trough URL
			if ( ! empty($_GET['autologin_hash']) )
			{
				$_COOKIE['autologin_hash'] = $_GET['autologin_hash'];
			}

			//Check for Auto Login / Keep me logged option
			$cookie_name = 'autologin_hash';
			$cookie_val = $this->input->cookie($cookie_name);
			
			// If there is autologin cookie
			if ( ! empty($cookie_val))
			{
				$cookie_arr = explode('|', $cookie_val);
				
				// If the autologin cookie is valid
				if ( ! empty($cookie_arr[0]) && ! empty($cookie_arr[1]))
				{
					// Get user id and hash from the cookie
					$data = array();
					$data['iduser'] = $cookie_arr[0];
					$data['hash'] = $cookie_arr[1];
					
					// load users model
					$this->load->model('Users_model', 'users');
					
					// Try to get the user
					$user_data = $this->users->get_users($data);
					
					// Login if user id and hash are correc 
					if ( ! empty($user_data))
					{
						$user_data_arr = (array) reset($user_data);
						if ( ! empty($user_data_arr))
						{
							// Set the user data to the session to login
							$_SESSION['user'] = $user_data_arr;
						}
					}
				}
			}
		}			
		
		// If the user is logged in - retrive new alerts and messages.
		if( ! empty($_COOKIE['PHPSESSID']))
		{
			if ( ! empty($_SESSION['user']['iduser']))
			{			
				//Retrieve new Alerts/Notifications
				$this->load->model('Notifications_model', 'notifications');
				if(isset($this->notifications) && $this->notifications)
					$_SESSION['info']['new_notifications'] = $this->notifications->get_all_new_events_count_for_member($_SESSION['user']['iduser']);
				
				//Retrieve new Messages for this user
				$this->load->model('Users_model', 'users');
				if(isset($this->users) && $this->users)
					$_SESSION['info']['new_messages'] = $this->users->get_new_messages_count($_SESSION['user']['iduser']);			
			}
		}
	}
	
	/**
	* View dispatcher - add header, footer and set parameters to header
	*
	* @param string $view The name of view to be displayed.
	* @param array $vars Variables passed to the view.
	* @access public
	*/
	public function view($view, $vars = array()) 
	{
		// ==== Set parameters to header ==== //
		
			// If loading tinyMCE - include params in the header
			if( ! empty($vars['tinyMCE']))
			{		
				$params = array(
					"tinyMCE" => TRUE
				);
			}		
			// If not loading tinyMCE - don't include anything in the header		
			else 
			{
				$params = array();
			}		
			
			// If the user is logged in - add his data to the params array
			if( ! empty($_SESSION['user']))
			{
				$params['user_data'] = $_SESSION['user'];
			}
			
			// If detecting page
			if( ! empty($vars['current_page']))
			{			
				$params['current_page'] = $vars['current_page'];
				$params['page'] = isset($vars['page']) ? $vars['page'] : array();
			}
			
			// If there is search sql
			if( ! empty($vars['searchSql']['string']))
			{
				$params['searchSql']['string'] = $vars['searchSql']['string'];
			}
			
			// Set the page title
			if( ! empty($vars['page_title']))
			{			
				$params['page_title'] = $vars['page_title'];
			}
			
			// If ajax is used
			if( ! empty($vars['ajax']))
			{			
				$params['ajax'] = $vars['ajax'];
			}
			
			// Searching project by location
			if( ! empty($vars['search_location_data']))
			{			
				$params['search_location_data'] = $vars['search_location_data'];
			}
			
			// If detecting search string
			if( ! empty($vars['string']))
			{			
				$params['string'] = $vars['string'];
			}
			
			// If detecting search keywords
			if( ! empty($vars['keywords']))
			{			
				$params['keywords'] = $vars['keywords'];
			}
			
			// If detecting search category
			if( ! empty($vars['category']))
			{			
				$params['category'] = $vars['category'];
			}
			
			// If loading map
			if( ! empty($vars['show_map']))
			{			
				$params['show_map'] = $vars['show_map'];
			}
			
			// If loading project
			if( ! empty($vars['project']))
			{			
				$params['project'] = $vars['project'];
			}
			// If loading facebook title
			if( ! empty($vars['fb_title']))
			{			
				$params['fb_title'] = $vars['fb_title'];
			}
			// If loading facebook description
			if( ! empty($vars['fb_description']))
			{			
				$params['fb_description'] = $vars['fb_description'];
			}
			
			// Get site settings
			$this->load->model('Configuration_model', 'configuration');
			$configuration = $this->configuration->get_all_configuration();
			$params['configuration'] = $configuration;
	
			// Get Facebook app id
			$this->config->load('facebook');
			$params['fb_app_id'] = $this->config->config['fb_app_id'];
			
		// Load header
		$this->load->view('header', $params);
		
		// Load content
		$this->load->view($view, $vars);
		
		// Load footer
		$this->load->view('footer');
	}
}

/* End of file base_controllers.php */
/* Location: ./application/core/base_controllers.php */