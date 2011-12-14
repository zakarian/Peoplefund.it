<?php 

// Categories class
class Categories_Module extends CI_Module {

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
	
		// Load model and try to get all categories
		$this->load->model('Categories_model', 'categories');
 	
		// Get categories
		$vars['items'] = $this->categories->get_categories(TRUE, FALSE);

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/categories/browse', $vars);
	}
	
	// Add category
	public function add(){
	
		// Vars array
		$vars = array();
		
		// Load categories model
		$this->load->model('Categories_model', 'categories');
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;

			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			} 
			
			// Check slug
			if(empty($_POST['slug'])){
				$vars['errors'][] = "Slug can't be empty";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Get id of the main category
				$idsubcategory = !empty($_POST['idsubcategory']) ? $this->input->post("idsubcategory") : 0;
				
				// Add category data
				$data = array(
					"title"				=>	$this->input->post("title"),
					"idsubcategory"		=>	$idsubcategory,
					"slug"				=>	$this->input->post("slug"),
					"active"			=>	$this->input->post("active"),
					"description"		=>	$this->input->post("description"),
					"order"				=>	$this->categories->get_next_order_id(array("idsubcategory" => $idsubcategory))
				);
				$this->categories->add_category($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The category was added";
				redirect("/administration/categories/");
			}
		}
		
		// Add tinyMCE
		$vars['tinyMCE'] = TRUE;
		
		// Get all main categories for select
		$vars['categories'] = $this->categories->get_categories(FALSE);
	
		// Display template
		$this->view('administration/categories/form', $vars);
	}
	
	// Edit category
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get category id
		$idcategory = (int) $this->parameters[0];

		// Load categories model
		$this->load->model('Categories_model', 'categories');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Assign post vars
			$vars['post'] = $_POST;

			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			} 
			
			// Check slug
			if(empty($_POST['slug'])){
				$vars['errors'][] = "Slug can't be empty";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Get id of the main category
				$idsubcategory = !empty($_POST['idsubcategory']) ? $this->input->post("idsubcategory") : 0;
				
				// Save category data
				$data = array(
					"title"				=>	$this->input->post("title"),
					"idsubcategory"		=>	$idsubcategory,
					"slug"				=>	$this->input->post("slug"),
					"active"			=>	$this->input->post("active"),
					"description"		=>	$this->input->post("description")
				);
				$this->categories->save_category($data, $this->parameters[0]);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The category was saved";
				redirect("/administration/categories/");
			}
			
		}

		// Display template
		$this->view('administration/categories/form', 
			array(
				"post" 			=> 	(array) $this->categories->get_category(array("idcategory" => $idcategory)),
				"action"		=>	"edit",
				"errors"		=>	$errors,
				"categories" 	=>	$this->categories->get_categories(FALSE),
				"tinyMCE"		=>	TRUE
			)
		);
	}
	
	// Sort categories
	public function sort(){
		
		// Get params from URL
		$direction = $this->parameters[0];
		$idcategory = $this->parameters[2];
		
		// Load categories model
		$this->load->model('Categories_model', 'categories');
		
		// Sort categories
		$this->categories->move_category($idcategory, $direction);
		
		// Redirect back
		redirect("/administration/categories/");
	}
	
	// Change active
	public function active(){
		
		// Load model
		$this->load->model('Categories_model', 'categories');
		
		// Update category
		$this->categories->save_category(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	// Delete category
	public function delete(){
	
		// Get category id
		$idcategory = (int) $this->parameters[0];
		
		// Load model and try to get data
		$this->load->model('Categories_model', 'categories');
		
		// Remove record
		$this->categories->delete_category($idcategory);
		
		// Terminate
		exit("ok");
	}

}