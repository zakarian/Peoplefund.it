<?php 

// Users class
class Users_Module extends CI_Module {

	// View dispatcher - adds header, footer and params
	public function view($view, $vars = array()) {
		$this->load->model('Categories_model', 'categories');
		$vars['categories'] = $this->categories->get_categories(true);
		// If loading tinyMCE - load header with MCE js + options
		if(!empty($vars['tinyMCE'])){
			$this->load->view('administration/header', array("tinyMCE" => true));
			
		// If not loading tinyMCE - load header
		} else {
			$this->load->view('administration/header');
		}
		
		// Load content
		$this->load->view($view, $vars);
		
		// Load footer
		$this->load->view('administration/footer');
	}
	
	// Index action
	public function index($temp_method = "", $letter = "", $temp_string = "", $string = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load users model and projects model
		$this->load->model('Users_model', 'users');
		$this->load->model('Projects_model', 'projects');
		
		// Form the main where clause
		$where = array(
			"type" => "user"
		);

		// If browsing by letter
		if(!empty($letter)){
			if($letter != "all"){
				$where['username'] = $letter."%";
			}
		} else {
			$letter = "all";
		}
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}

		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/users/index/letter/'.$letter.'/string/'.$string.'/page/');
		$pager['total_rows'] = $this->users->count_users($where);
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Get user data (for page or csv)
		$vars['items'] = $this->users->get_users($where, (isset($_GET['csv']) ? array() : array("from" => $pager['start_row'], "count" => $pager['per_page'])));
		
		// Generate csv 
		if(isset($_GET['csv'])) {
			$filename = 'members-on-'.date('Y-m-d-g-i').'.csv';

			// Set headers
			header('Content-Type: text/csv; charset=utf8');
			header('Content-Disposition: attachment; filename='.$filename);

			// Set columns
			$columns = array('Username', 'Email', 'Name', 'Town', 'County', 'Postcode', 'Registered at', 'Activated', 'Confirmed', 'Newsletter');
			
			// Open output
			$f = fopen('php://output', 'w');
			fputcsv($f, $columns);
			
			// Print rows
			foreach($vars['items'] as $item) {
				$row = array();

				$row[] = $item->username;
				$row[] = $item->email;
				$row[] = $item->name;
				$row[] = $item->location_preview;
				$row[] = $item->county_name;
				$row[] = $item->postcode;
				$row[] = $item->date_register;
				$row[] = ($item->active > 0 ? 'yes' : 'no');
				$row[] = ($item->confirmed > 0 ? 'yes' : 'no');
				$row[] = ($item->newsletter > 0 ? 'yes' : 'no');
				
				fputcsv($f, $row);
			}
			
			exit;
		}
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Get users stats
		$vars['stats'] = $this->users->get_users_stats();
		
		// Get users projects
		foreach($vars['items'] AS &$user){
			$user->projects = $this->projects->get_projects(array("p.iduser" => $user->iduser));
		}

		// Display template
		$this->view('administration/users/browse', $vars);
	}
	
	public function project_helpers($temp_method = "", $letter = "", $temp_string = "", $string = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load users model and projects model
		$this->load->model('Users_model', 'users');
		$this->load->model('Projects_model', 'projects');
		
		// Form the main where clause
		$where = array(
			"type" => "user"
		);

		// If browsing by letter
		if(!empty($letter)){
			if($letter != "all"){
				$where['username'] = $letter."%";
			}
		} else {
			$letter = "all";
		}
		if(isset($_POST['string']) && $_POST['string']) $string = $_POST['string'];
		// If browsing by string
		if(empty($string)){
			if(!empty($string)){
				$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}

		$vars['subslug'] = 'project_helpers';
		
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/users/'.$vars['subslug'].'/letter/'.$letter.'/string/'.$string.'/page/');
		$pager['total_rows'] = count($this->users->get_project_helpers($where));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Get user data
		$vars['items'] = $this->users->get_project_helpers($where, (isset($_GET['csv']) ? array() : array("from" => $pager['start_row'], "count" => $pager['per_page'])));

		// Generate csv 
		if(isset($_GET['csv'])) {
			$filename = 'members-on-'.date('Y-m-d-g-i').'.csv';

			// Set headers
			header('Content-Type: text/csv; charset=utf8');
			header('Content-Disposition: attachment; filename='.$filename);

			// Set columns
			$columns = array('Username', 'Email', 'Name', 'Town', 'County', 'Postcode', 'Registered at', 'Activated', 'Confirmed', 'Newsletter');
			
			$f = fopen('php://output', 'w');
			fputcsv($f, $columns);
			
			foreach($vars['items'] as $item) {
				$row = array();

				$row[] = $item->username;
				$row[] = $item->email;
				$row[] = $item->name;
				$row[] = $item->location_preview;
				$row[] = $item->county_name;
				$row[] = $item->postcode;
				$row[] = $item->date_register;
				$row[] = ($item->active > 0 ? 'yes' : 'no');
				$row[] = ($item->confirmed > 0 ? 'yes' : 'no');
				$row[] = ($item->newsletter > 0 ? 'yes' : 'no');
				
				fputcsv($f, $row);
			}
			
			exit;
		}
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Get users projects
		foreach($vars['items'] AS &$user){
			$user->projects = $this->projects->get_projects(array("p.iduser" => $user->project_iduser));
		}
		
		
		$this->view('administration/users/browse', $vars);
	}
	
	// Projects owners
	public function project_owners($temp_method = "", $letter = "", $temp_string = "", $string = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load users model and projects model
		$this->load->model('Users_model', 'users');
		$this->load->model('Projects_model', 'projects');
		
		// Form the main where clause
		$where = array(
			"type" => "user"
		);

		// If browsing by letter
		if(!empty($letter)){
			if($letter != "all"){
				$where['username'] = $letter."%";
			}
		} else {
			$letter = "all";
		}
		if(isset($_POST['string']) && $_POST['string']) $string = $_POST['string'];
		// If browsing by string
		if(empty($string)){
			if(!empty($string)){
				$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}

		$vars['subslug'] = 'project_owners';
		
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/users/'.$vars['subslug'].'/letter/'.$letter.'/string/'.$string.'/page/');
		$pager['total_rows'] = $this->users->count_project_owners($where);
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Get user data
		$vars['items'] = $this->users->get_project_owners($where, (isset($_GET['csv']) ? array() : array("from" => $pager['start_row'], "count" => $pager['per_page'])));
		
		// Generate csv 
		if(isset($_GET['csv'])) {
			$filename = 'members-on-'.date('Y-m-d-g-i').'.csv';
			
			// Set headers
			header('Content-Type: text/csv; charset=utf8');
			header('Content-Disposition: attachment; filename='.$filename);

			// Set columns
			$columns = array('Username', 'Email', 'Name', 'Town', 'County', 'Postcode', 'Registered at', 'Activated', 'Confirmed', 'Newsletter');
			
			$f = fopen('php://output', 'w');
			fputcsv($f, $columns);
			
			foreach($vars['items'] as $item) {
				$row = array();

				$row[] = $item->username;
				$row[] = $item->email;
				$row[] = $item->name;
				$row[] = $item->location_preview;
				$row[] = $item->county_name;
				$row[] = $item->postcode;
				$row[] = $item->date_register;
				$row[] = ($item->active > 0 ? 'yes' : 'no');
				$row[] = ($item->confirmed > 0 ? 'yes' : 'no');
				$row[] = ($item->newsletter > 0 ? 'yes' : 'no');
				
				fputcsv($f, $row);
			}
			
			exit;
		}
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Get users projects
		foreach($vars['items'] AS &$user){
			$user->projects = $this->projects->get_projects(array("p.iduser" => $user->iduser));
		}
		
		
		$this->view('administration/users/browse', $vars);
	}
	
	// Projects backers
	public function project_backers($temp_method = "", $letter = "", $temp_string = "", $string = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load users model and projects model
		$this->load->model('Users_model', 'users');
		$this->load->model('Projects_model', 'projects');
		
		// Form the main where clause
		$where = array();

		// If browsing by letter
		if(!empty($letter)){
			if($letter != "all"){
				$where['username'] = $letter."%";
			}
		} else {
			$letter = "all";
		}
		if(isset($_POST['string']) && $_POST['string']) $string = $_POST['string'];
		// If browsing by string
		if(empty($string)){
			if(!empty($string)){
				$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['email'] = "%".$string."%' OR username LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}

		$vars['subslug'] = 'project_backers';
		
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/users/'.$vars['subslug'].'/letter/'.$letter.'/string/'.$string.'/page/');
		$pager['total_rows'] = count($this->users->get_project_backers($where));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Get user data
		$vars['items'] = $this->users->get_project_backers($where, (isset($_GET['csv']) ? array() : array("from" => $pager['start_row'], "count" => $pager['per_page'])));
		
		// Generate csv 
		if(isset($_GET['csv'])) {
			$filename = 'members-on-'.date('Y-m-d-g-i').'.csv';

			// Set headers
			header('Content-Type: text/csv; charset=utf8');
			header('Content-Disposition: attachment; filename='.$filename);

			// Set columns
			$columns = array('Username', 'Email', 'Name', 'Town', 'County', 'Postcode', 'Registered at', 'Activated', 'Confirmed', 'Newsletter');
			
			$f = fopen('php://output', 'w');
			fputcsv($f, $columns);
			
			foreach($vars['items'] as $item) {
				$row = array();

				$row[] = $item->username;
				$row[] = $item->email;
				$row[] = $item->name;
				$row[] = $item->location_preview;
				$row[] = $item->county_name;
				$row[] = $item->postcode;
				$row[] = $item->date_register;
				$row[] = ($item->active > 0 ? 'yes' : 'no');
				$row[] = ($item->confirmed > 0 ? 'yes' : 'no');
				$row[] = ($item->newsletter > 0 ? 'yes' : 'no');
				
				fputcsv($f, $row);
			}
			
			exit;
		}
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Get users projects
		foreach($vars['items'] AS &$user){
			$user->projects = $this->users->get_project_backers(array("pp.iduser" => $user->iduser));
		}
		
		
		$this->view('administration/users/browse', $vars);
	}
	
	// Add - add user
	public function add(){
	
		// Template vars array
		$vars = array("page" => "add");
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;
			
			// Form query params
			$data = array(
				"username"	=>	$this->input->post("username")
			);
			
			// Load model and try to get user data
			$this->load->model('Users_model', 'users');
			$user_data = reset($this->users->get_users($data));
			
			// Check username length
			if(strlen($this->input->post("username")) < 3){
				$vars['errors'][] = "Username can't be less than 3 symbols";
			
			// If the username is taken
			} else if($user_data){
				$vars['errors'][] = "The username is already taken";
			}
			
			// Check passwords
			if(strlen($this->input->post("password")) < 3){
				$vars['errors'][] = "Password can't be less than 3 symbols";
			
			// Check the second password
			} else if($this->input->post("password") != $this->input->post("password_repeat")){
				$vars['errors'][] = "Passwords don't match";
			}
			
			// Try to find this email
			$user_data = reset($this->users->get_users(array("email" => $this->input->post("email"))));
			
			// Check email syntax
			if(!check_email($this->input->post('email'))){
				$vars['errors'][] = "Invalid email address";
			} else if($user_data){
				$vars['errors'][] = "The email is already used in our system";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Add user data
				$data = array(
					"username"	=>	$this->input->post("username"),
					"password"	=>	md5($this->input->post("password")),
					"active"	=>	$this->input->post("active"),
					"email"		=>	$this->input->post("email"),
					"type"		=>	"user"
				);
				$this->users->add_user($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The user was added";
				redirect("/administration/users/");
			}
		}
	
		// Display template
		$this->view('administration/users/form', $vars);
	}
	
	// Edit user
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get user id
		$iduser = (int) $this->parameters[0];

		// Load model and try to get user data
		$this->load->model('Users_model', 'users');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			if(isset($_POST['interests']) && $_POST['interests'])
				$_POST['interests'] = serialize($_POST['interests']);
			else
				$_POST['interests'] = serialize(array());
				
			// Get password
			$password = $this->input->post("password");
			
			// If updating passwords
			if(!empty($password)){
			
				// Check passwords
				if(strlen($this->input->post("password")) < 3){
					$errors[] = "Password can't be less than 3 symbols";
				
				// Check the second password
				} else if($this->input->post("password") != $this->input->post("password_repeat")){
					$errors[] = "Passwords don't match";
				}
			}
			
			// Check email
			if(!check_email($this->input->post('email'))){
				$errors[] = "Invalid email address";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Add user data
				$data = array(
					"fbid"		=>	$this->input->post("fbid"),
					"username"	=>	$this->input->post("username"),
					"type"		=>	$this->input->post("type"),
					"name"		=>	$this->input->post("name"),
					"slug"		=>	$this->input->post("slug"),
					"bio"		=>	$this->input->post("bio"),
					"postcode"	=>	$this->input->post("postcode"),
					"location"	=>	$this->input->post("location"),
					"town_name"	=>	$this->input->post("town_name"),
					"county_name"	=>	$this->input->post("county_name"),
					"location_preview"	=>	$this->input->post("location_preview"),
					"websites"	=>	$this->input->post("websites"),
					"alerts_own"	=>	$this->input->post("alerts_own"),
					"alerts_backing"	=>	$this->input->post("alerts_backing"),
					"alerts_watch"	=>	$this->input->post("alerts_watch"),
					"newsletter"	=>	$this->input->post("newsletter"),
					"hash"		=>	$this->input->post("hash"),
					"active"	=>	$this->input->post("active"),
					"email"		=>	$this->input->post("email"),
					"confirmed" =>	$this->input->post("confirmed"),
					'interests' => $_POST['interests']
				);
				
				// If updating password too
				if(!empty($password)){
					$data['password'] = md5($password);
				}
				
				// Update user data
				$this->users->save_user($data, $iduser);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The user was saved";
				redirect("/administration/users/");
			}
		}

		// Display template
		$this->view('administration/users/form', 
			array(
				"post" 		=> 	(array) reset($this->users->get_users(array("iduser" => $iduser))),
				"action"	=>	"edit",
				"errors"	=>	$errors
			)
		);
	}
	
	// Delete user
	public function delete(){
	
		// Get user id
		$iduser = (int) $this->parameters[0];
		
		// Load model and try to get user data
		$this->load->model('Users_model', 'users');
		
		// Remove record
		$this->users->delete_user($iduser);
		
		// Terminate
		exit("ok");
	}
	
	// Change active
	public function active(){
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Update user
		$this->users->save_user(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	// Autocomplete username
	public function autocomplete(){
		
		// Get field value
		$username = $this->input->get("term");
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get usernames
		$users = $this->users->get_users(array("username" => $username."%"));
		
		// Return array
		$return = array();
		
		// Regroup
		foreach($users AS $user){
			$return[] = array(
				"id" => $user->iduser,
				"val" => $user->username,
				"label" => $user->username
			);
		}
		
		// Return JSON encoded results
		echo json_encode($return);
		
	}
	
	// Login as user
	public function login($iduser = 0){
	
		// If we are passing wrong user id
		if($iduser == "0"){
			$_SESSION['message'] = "Please specify user first";
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Get user data
		$user = $this->users->get_users(array("iduser" => $iduser), array("from" => 0, "count" => 1));
		$user = (array) reset($user);
		
		// Start user session
		$_SESSION['user'] = $user;
		
		// Redirect to index page
		redirect("/");
	}
	
	// Remove user image
	public function remove_image($iduser = 0){
		
		// If we are passing wrong user id
		if($iduser == "0"){
			$_SESSION['message'] = "Please specify user first";
		}
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Update user
		$this->users->save_user(array("ext" => ""), $iduser);
	}
	
}