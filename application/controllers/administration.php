<?php 
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Administration controller 
* Handles the administration module from which you can control projects, users, pages, pledges etc. on the site
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Administration extends Base_Controller {

	/** 
	* Remaps the request to the administration module using the files in /application/modules/administraion and the methods in this file
	* Resends the parameters from the url to the called module
	*
	* @access public 
	* @param string $module The name of the module to be called e.g. projects, users etc.
	* @param string $parameters The parameters to be passed to the module
	*/
	public function _remap($module = 'index', $parameters = array())
	{
		// If the called module is defined in this file call the method e.g. index, login, logout
		if (method_exists($this, $module)) 
            $this->$module();
        else 
		{
			// If the module is not defined in this file pass the parameters to the module method			
            if (is_admin())
				$this->module($module, $parameters);
            // Method add_file() of assets module can be called from the site without the admin beign loged in
			else if(!empty($parameters[0]) && $parameters[0] == 'add_file')
				$this->module($module, $parameters);
			// If the admin is not loged in go to login page
            else
                redirect('/administration/login/');
        }
	}
	
	/**
	* Calls the selected module
	* Loads the administration menu variables from a cofing file 
	*
	* @access public
	* @param string $module The name of the module to be called e.g. projects, users etc.
	* @param string $parameters The parameters to be passed to the module
	*/
	private function module($module='administrators', $parameters = array())
	{
		// Loading the administration menu array from administration_menu.php config file 
        $this->load->config('administration_menu');
		$administration_menu = $this->config->config['administration_menu'];
		// Generate variables from the administration menu array
        $this->load->vars('administration_menu', $administration_menu);
        $this->load->vars('current_module', $module);

        // Call the selected module from the /modules/administration folder
        $module = 'administration/'.$module;
        $this->load->module($module, $parameters);
    }
	
	/**
	* Handles the index module
	*
	* @access public
	*/
	public function index() 
	{
		// If admin is logged in open the pages module
		if (is_admin())
			redirect('/administration/pages/');
		// If admin is not logged in open login page
		else
			redirect('/administration/login/');
	}
	
	/**
	 * Handles the administration login
	 * Loads login form and if filled correctly login and redirect to the administration module
	 *
	 * @access public
	 */
	public function login() 
	{
		// If already logged in go to pages module
		if (is_admin())
			redirect('/administration/pages/');
		
		// Declaring $vars to be passed to the view
		$vars = array();
		
		// If the login form was sent try to login
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			// Array with the user data to be looked for in the database 
			$data = array(
				'username'	=>	h(st($_POST['username'])),
				'password'	=>	md5($_POST['password']),
				'type'		=>	'admin',
				'confirmed'	=>	1,
				'active'	=>	1
			);
			
			// Load users model
			$this->load->model('Users_model', 'users');
			// Get the data for the user trying to log in
			$user_data = reset($this->users->get_users($data));
			
			// If the username and password are correct login and redirect to pages module
			if(isset($user_data) && ! empty($user_data))
			{
				// Add the data of the logged user to the session
				$this->load->helper('session');
				$_SESSION['admin'] = $user_data;
				// Redirect to pages module
				redirect('/administration/pages/');
			}
			// If the username or password are not correct send email to the notify email and reload the login form
			else 
			{
				// Get instance of CodeIgniter super object
				$ci = get_instance();
			
				// Generate the content of the email to be sent to the notify email
				$text = "One of the users can't login correctly, here are the login details that he is using:<br><br>";
				$text .= "<b>Date:</b> ".date("Y-m-d H:i:s")."<br>";
				$text .= "<b>Username:</b> ".$_POST['username']."<br>";
				$text .= "<b>Password:</b> ".$_POST['password']."<br>";
				$text .= "<b>IP:</b> ".$_SERVER['REMOTE_ADDR']."<br>";
				
				// Get the admin and notify emails from Configuration model
				$this->load->model('Configuration_model', 'configuration');
				$notify_email = $this->configuration->get_configuration(array("name" => "notify_email"));
				$notify_email = reset($notify_email);
				
				$admin_email = $this->configuration->get_configuration(array("name" => "admin_email"));
				$admin_email = reset($admin_email);

				// Send email from admin email to notify email
				send_mail($admin_email, $notify_email, "Wrong login information", $text);
				
				// Set an error message to be shown in the view
				$vars['error'] = 'Wrong username or password';
			}
		}
		// Load the login form
		$this->load->view('administration/login', $vars);
	}

	/**
	 * Handles the administration logout
	 *
	 * @access public
	 */
	public function logout()
	{
		// Unset the user data from the session
		unset($_SESSION['admin']);
		// Redirect to the login form
		redirect('/administration/login/');
	}	
}

/* End of file administration.php */ 
/* Location: ./application/controllers/administration.php */