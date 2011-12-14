<?php 

// Partners class
class Partners_Module extends CI_Module {

	// View dispatcher - adds header, footer and params
	public function view($view, $vars = array()) {
	
		// If loading tinyMCE - load header with MCE js + options
		if(!empty($vars['tinyMCE'])){
			$this->load->view('administration/header', array("tinyMCE" => TRUE));
			
		// If not loading tinyMCE - load header
		} else {
			$this->load->view('administration/header');
		}
		
		// Get site settings
		$this->load->model('Configuration_model', 'configuration');
		$configuration = $this->configuration->get_all_configuration();
		$vars['configuration'] = $configuration;		
		
		// Load content
		$this->load->view($view, $vars);
		
		// Load footer
		$this->load->view('administration/footer');
	}
	
	// Index action
	public function index() {
	
		// Load model and try to get partners
		$this->load->model('Partners_model', 'partners');
 	
		// Get partners
		$vars['items'] = $this->partners->get_partners();

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/partners/browse', $vars);
	}
	

	// Add partner
	public function add(){
	
		// Vars array
		$vars = array();
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Load partners model
				$this->load->model('Partners_model', 'partners');
			
				// Add partner data
				$data = array(
					"title"		=>	$this->input->post("title"),
					"url"		=>	$this->input->post("url"),
					//"image"		=>	$this->input->post("image"),
					"active"	=>	$this->input->post("active")
				);
				$this->partners->add_partner($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The partner was added";
				redirect("/administration/partners/");
			}
		}
		
		// Add tinyMCE
		$vars['tinyMCE'] = TRUE;
	
		// Display template
		$this->view('administration/partners/form', $vars);
	}
	
	
	// Edit partner
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get partner id
		$id = (int) $this->parameters[0];

		// Load partners model
		$this->load->model('Partners_model', 'partners');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			
			// If we don't have any errors
			if(empty($errors)){

				// Add partners data
				$data = array(
					"active"	=>	$this->input->post("active"),
					//"image"		=>	$this->input->post("image"),
					"title"		=>	$this->input->post("title"),
					"url"		=>	$this->input->post("url")
				);

				
				// Update partners data
				$this->partners->save_partner($data, $id);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The partner was saved";
				redirect("/administration/partners/");
			}
			
		}

		// Display template
		$this->view('administration/partners/form', 
			array(
				"post" 		=> 	(array) reset($this->partners->get_partners(array("idpartner" => $id))),
				"action"	=>	"edit",
				"errors"	=>	$errors,
				"tinyMCE"	=>	TRUE
			)
		);
	}
	
	// Change active
	public function active(){
		
		// Load model
		$this->load->model('Partners_model', 'partners');
		
		// Update partners
		$this->partners->save_partner(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	
	// Delete partner
	public function delete(){
	
		// Get partner id
		$idpartner = (int) $this->parameters[0];
		
		// Load model and try to get user data
		$this->load->model('Partners_model', 'partners');
		
		// Remove record
		$this->partners->delete_partner($idpartner);
		
		// Terminate
		exit("ok");
	}
	
	// Sort menus
	public function sort(){
		
		// Get params from URL
		$direction = $this->parameters[0];
		$idpartner = $this->parameters[2];
		
		// Load model
		$this->load->model('Partners_model', 'partners');
		
		// Sort categories
		$this->partners->sort($idpartner, $direction);
		
		// Redirect back
		redirect("/administration/partners/");
	}	
	
	// Remove partner image
	public function remove_image($idpartner = 0){
		
		// If we are passing wrong partner id
		if($idpartner == "0"){
			$_SESSION['message'] = "Please specify partner first";
		}
		
		// Load partner model
		$this->load->model('Partners_model', 'partners');
		
		// Update user
		$this->partners->save_partner(array("image" => ""), $idpartner);
		$this->partners->save_cache_file();
	}	

	/**
	 *  Add partner image - upload it and add to database
	 *
	 * @access public
	 */
	public function add_image()
	{
		// Get file extension
		$ext = strtolower(end(explode(".", $_FILES['File']['name'])));
	
		// Check file size - it should not be more than 2MB
		if (($_FILES['File']['size'] / 2048) > 2048)
		{
			$error = "Image size should be less than 2 MB";
		}
	
		// Check file extension - it should be jpg, gif or png
		else if ($ext != "jpg" && $ext != "gif" && $ext != "png") {
			$error = "The image should be JPG, GIF or PNG";
		}
	
		// Form upload path
		$path = SITE_DIR . "public/uploads/partners/" . (int) $_POST['idpartner'] . "." . $ext;
	
		// If we don't have errors
		if(empty($error))
		{
			// Upload image
			move_uploaded_file($_FILES['File']['tmp_name'], $path);
	
			// Load partners model
			$this->load->model('Partners_model', 'partners');
	
			// Save the image url to the database
			$image = "/uploads/partners/" . (int) $_POST['idpartner'] . "." . $ext;
			$this->partners->save_partner(array("image" => $image), (int) $_POST['idpartner']);
	
			// Add anticaching to the output
			$output = $image . '?'.time();
	
			// Set result array
			$result_arr = array(
					'success' => TRUE,
					'html' => $output
			);
	
			// Print the output as JSON
			echo json_encode($result_arr);
			exit();
		}
		// If we have error
		else
		{
			// Set error output array
			$result_arr = array(
					'success' => FALSE,
					'msg' => $error
			);
	
			// Print the output as JSON
			echo json_encode($result_arr);
			exit();
		}
	}
}