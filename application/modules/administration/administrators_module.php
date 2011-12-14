<?php 

// Administration class
class Administrators_Module extends CI_Module {

	// View dispatcher - adds header, footer and params
	public function view($view, $vars = array()) {
	
		// If loading tinyMCE - load header with MCE js + options
		if(!empty($vars['tinyMCE'])){
			$this->load->view('administration/header', array("tinyMCE" => TRUE));
			
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
	public function index($page = 0) {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load users model
		$this->load->model('Users_model', 'users');
		
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/administrators/index/');
		$pager['total_rows'] = $this->users->count_users(array("type" => "admin"));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * $pager['cur_page'];
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
 	
		// Get user data
		$vars['items'] = $this->users->get_users(array("type" => "admin"), array("from" => $pager['start_row'], "count" => $pager['per_page']));
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/administrators/browse', $vars);
	}
	
	// Add - add administrator
	public function add(){
	
		// Vars array
		$vars = array();
		
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
					"type"		=>	"admin"
				);
				$this->users->add_user($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The administrator was added";
				redirect("/administration/administrators/");
			}
		}
	
		// Display template
		$this->view('administration/administrators/form', $vars);
	}
	
	// Edit administrator
	public function edit(){
		
		// Errors array
		$errors = array();
			
		// Get user id
		$iduser = (int) $this->parameters[0];

		// Load model and try to get user data
		$this->load->model('Users_model', 'users');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

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
					"active"	=>	$this->input->post("active"),
					"email"		=>	$this->input->post("email")
				);
				
				// If updating password too
				if(!empty($password)){
					$data['password'] = md5($password);
				}
				$this->users->save_user($data, $iduser);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The administrator was saved";
				redirect("/administration/administrators/");
			}
		}

		// Display template
		$this->view('administration/administrators/form', 
			array(
				"post" 		=> 	(array) reset($this->users->get_users(array("iduser" => $iduser))),
				"action"	=>	"edit",
				"errors"	=>	$errors
			)
		);
	}
	
	// Delete administrator
	public function delete(){
	
		// Get administrator id
		$iduser = (int) $this->parameters[0];
		
		// Load model and try to get administrator data
		$this->load->model('Users_model', 'users');
		
		// Remove record
		$this->users->delete_user($iduser);
		
		// Terminate
		exit("ok");
	}
	
	// Change active
	public function active(){
		
		// Load users module
		$this->load->model('Users_model', 'users');
		
		// Update administrator
		$this->users->save_user(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
}