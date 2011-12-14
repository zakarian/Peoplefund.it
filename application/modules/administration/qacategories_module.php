<?php
// Question and answer categories class
class Qacategories_Module extends CI_Module {
	
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
	
	public function index() {
		// Load model and try to get all questions
		$this->load->model('Questions_model', 'questions');

		// Get questions
		$vars['categories'] = $this->questions->get_categories_admin();

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/questions/categories', $vars);
	}
	
	// Change active
	public function active(){
		// Load model
		$this->load->model('Questions_model', 'questions');
		if ($_POST['active'] == '1') { 
			$status = 'active';		
		} else {
			$status = 'inactive';	
		}
		
		// Update question
		$this->questions->save_category(array("status" => $status), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	
	// Sort categories
	public function sort(){
		
		// Get params from URL
		$direction = $this->parameters[0];
		$idcategory = $this->parameters[2];
		
		// Load categories model
		$this->load->model('Questions_model', 'questions');
		
		// Sort categories
		$this->questions->move_category($idcategory, $direction);
		
		// Redirect back
		redirect("/administration/qacategories/");
	}
	
	// Add category
	public function add(){
	
		// Vars array
		$vars = array();
		
		// Load categories model
		$this->load->model('Questions_model', 'questions');
	
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
			

				// Add category data
				$data = array(
					"title"				=>	$this->input->post("title"),
					"slug"				=>	$this->input->post("slug"),
					"status"			=>	$this->input->post("status"),
					"desc"				=>	$this->input->post("desc"),
					"sort_id"			=>	$this->questions->get_category_next_order_id()
				);
				$this->questions->add_category($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The category was added";
				redirect("/administration/qacategories/");
			}
		}
		
		// Get all main categories for select
		$vars['categories'] = $this->questions->get_categories(FALSE);
	
		// Display template
		$this->view('administration/questions/categories_form', $vars);
	}
	
	// Edit question
	public function edit(){
		$this->load->helper('ip');

		// Errors array
		$errors = array();
			
		// Get question id
		$idquestion = (int) $this->parameters[0];

		// Load questions model
		$this->load->model('Questions_model', 'questions');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Assign post vars
			$vars['post'] = $_POST;

			// Check text
			if(empty($_POST['desc'])){
				$vars['errors'][] = "Question can't be empty";
			} 
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Save question data
				$data = array(
					"title"				=>	$this->input->post("title"),
					"slug"				=>	$this->input->post("slug"),
					"status"			=>	$this->input->post("status"),
					"desc"				=>	$this->input->post("desc")
				);
				$this->questions->save_category($data, $idquestion);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The question was saved";
				redirect("/administration/qacategories/");
			}
			
		}
		$category = $this->questions->get_categories(array('id' => $idquestion));
		// Display template
		$this->view('administration/questions/categories_form', 
			array(
				"category"	=>  $category[0],
				"action"		=>	"edit",
				"errors"		=>	$errors
			)
		);
	}
	
	// Delete category
	public function delete(){
	
		// Get category id
		$idcategory = (int) $this->parameters[0];
		
		// Load model and try to get data
		$this->load->model('Questions_model', 'categories');
		
		// Remove record
		$this->categories->delete_category($idcategory);
		
		// Terminate
		exit("ok");
	}
}
?>