<?php 

// Config class
class Configuration_Module extends CI_Module {

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
	
		// Load configuration model
		$this->load->model('Configuration_model', 'configuration');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Remove form elements
			unset($_POST['action'], $_POST['submit_x'], $_POST['submit_y']);
			
			// Assign success message
			$_SESSION['message'] = "All settings were saved";
			
			// Save configuration
			$this->configuration->save_configuration($_POST);
		}
 	
		// Get configuration
		$vars['data'] = $this->configuration->get_configuration();
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/configuration/browse', $vars);
	}

}