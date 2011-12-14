<?php 

// Emails class
class Emails_Module extends CI_Module {

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
	public function index() {
	
		// Load model and try to get emails
		$this->load->model('Emails_model', 'emails');
 	
		// Get emails
		$vars['items'] = $this->emails->get_emails();

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/emails/browse', $vars);
	}
	
	// Add email
	public function add(){
	
		// Vars array
		$vars = array();
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;

			// Check subject
			if(empty($_POST['subject'])){
				$vars['errors'][] = "Subject can't be empty";
			} 
			
			// Check email text
			if(empty($_POST['text'])){
				$vars['errors'][] = "Text can't be empty";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Load emails model
				$this->load->model('Emails_model', 'emails');
			
				// Add email data
				$data = array(
					"subject"	=>	$this->input->post("subject"),
					"active"	=>	$this->input->post("active"),
					"text"		=>	$this->input->post("text")
				);
				$this->emails->add_email($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The email was added";
				redirect("/administration/emails/");
			}
		}
		
		// Add tinyMCE
		$vars['tinyMCE'] = TRUE;
	
		// Display template
		$this->view('administration/emails/form', $vars);
	}
	
	// Edit email
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get email id
		$idemail = (int) $this->parameters[0];

		// Load emails model
		$this->load->model('Emails_model', 'emails');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Check subject
			if(empty($_POST['subject'])){
				$errors[] = "Subject can't be empty";
			} 
			
			// Check email text
			if(empty($_POST['text'])){
				$errors[] = "Text can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){

				// Add email data
				$data = array(
					"active"	=>	$this->input->post("active"),
					"subject"	=>	$this->input->post("subject"),
					"text"		=>	$this->input->post("text")
				);

				
				// Update email data
				$this->emails->save_email($data, $idemail);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The email was saved";
				redirect("/administration/emails/");
			}
			
		}

		// Display template
		$this->view('administration/emails/form', 
			array(
				"post" 		=> 	(array) reset($this->emails->get_emails(array("idemail" => $idemail))),
				"action"	=>	"edit",
				"errors"	=>	$errors,
				"tinyMCE"	=>	TRUE
			)
		);
	}
	
	// Change active
	public function active(){
		
		// Load model
		$this->load->model('Emails_model', 'emails');
		
		// Update email
		$this->emails->save_email(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}

}