<?php 

class Pages_Module extends CI_Module {

	public function view($view, $vars = array()) {
	
		if(!empty($vars['tinyMCE']))
			$this->load->view('administration/header', array("tinyMCE" => TRUE));
		else 
			$this->load->view('administration/header');
	
		$this->load->view($view, $vars);
		$this->load->view('administration/footer');
	}
	
	public function sort(){
		
		$direction = $this->parameters[0];
		$idpage = $this->parameters[2];
		$nav = $this->parameters[4];
		
		$this->load->model('Pages_model', 'pages');
		
		$this->pages->move_pages($idpage, $direction, $nav);
		
		redirect('/administration/pages/index/'.$nav);
	}
	
	public function index() {
		
		$uri = $this->uri->segment_array();
		$this->load->model('Pages_model', 'pages');
		$where = $vars= array();
		$nav = '';
		
		if(!empty($this->parameters[0]) && $this->parameters[0] == "section"){
			
			$where['idsection'] = $this->parameters[1];
			$vars['filterbysection'] = TRUE;
			
			if(isset($uri[4]) && is_numeric($uri[4])) {
				$where['idsection'] = $uri[4];
			} 
			
			$vars['pages'] = $this->pages->get_pages($where, '', $nav);

		} else {
		
			$where['idsection'] = 0;
			
			if(isset($uri[4]) && ($uri[4] == 'in_foot' OR $uri[4] == 'in_main')) {
				$vars['filterbynav'] = $nav = $uri[4];
				$where[$uri[4]] = 1;
			}
				
			$vars['pages'] = $this->pages->get_pages($where, '', $nav);
			$vars['sections'] = $this->pages->get_sections(array('is_section' => 1));
			
		}

		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		$this->view('administration/pages/browse', $vars);
	}
	
	// Add page
	public function add(){
	
		// Vars array
		$vars = array();
		
		// Load model
		$this->load->model('Pages_model', 'pages');
		$this->load->model('Projects_model', 'projects');
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;

			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			} 
			
			// Form slug
			$slug = $this->input->post("slug");
			
			// Check for a project with this slug
			$slug_check = $this->pages->get_pages(array("slug" => $slug));
			if($slug_check){
				$vars['errors'][] = "The page is already added, please change it's title";
			}
			
			$slug_check = $this->projects->get_projects(array("p.slug" => $slug));
			if($slug_check){
				$vars['errors'][] = "There is a project with this slug, please change it";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){

				// Add email data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"in_foot"		=>	$this->input->post("in_foot"),
					"order_foot"	=>	$this->pages->get_next_order_id(array('in_foot' => $this->input->post("in_foot"))),
					"in_main"		=>	$this->input->post("in_main"),
					"order_main"	=>	$this->pages->get_next_order_id(array('in_main' => $this->input->post("in_main"))),
					"slug"			=>	$this->input->post("slug"),
					"meta_title"	=>	$this->input->post("meta_title"),
					"idsection"		=>	$this->input->post("idsection"),
					"keywords"		=>	$this->input->post("keywords"),
					"description"	=>	$this->input->post("description"),
					"body"			=>	$this->input->post("body"),
					"active"		=>	$this->input->post("active")
				);
				$this->pages->add_page($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The page was added";
				redirect("/administration/pages/");
			}
		}
		
		// Get sections
		$vars['sections'] = $this->pages->get_sections(array('is_section' => 1));
		
		// Add tinyMCE
		$vars['tinyMCE'] = TRUE;
	
		// Display template
		$this->view('administration/pages/form', $vars);
	}
	
	// Edit page
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get page id
		$idpage = (int) $this->parameters[0];

		// Load pages model
		$this->load->model('Pages_model', 'pages');
		$this->load->model('Projects_model', 'projects');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// Check title
			if(empty($_POST['title'])){
				$errors[] = "Title can't be empty";
			}
			
			$slug_check = $this->projects->get_projects(array("p.slug" => $this->input->post("slug")));
			if($slug_check){
				$errors[] = "There is a project with this slug, please change it";
			}

			// If we don't have any errors
			if(empty($errors)){
			
				// Update data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"in_foot"		=>	$this->input->post("in_foot"),
					"in_main"		=>	$this->input->post("in_main"),
					"slug"			=>	$this->input->post("slug"),
					"meta_title"	=>	$this->input->post("meta_title"),
					"idsection"		=>	$this->input->post("idsection"),
					"keywords"		=>	$this->input->post("keywords"),
					"description"	=>	$this->input->post("description"),
					"body"			=>	$this->input->post("body"),
					"active"		=>	$this->input->post("active")
				);
				
				// Update page data
				$this->pages->save_page($data, $idpage);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The page was saved";
				redirect("/administration/pages/");
			}
		}

		// Display template
		$this->view('administration/pages/form', 
			array(
				"post" 		=> 	(array) reset($this->pages->get_pages(array("idpage" => $idpage))),
				"action"	=>	"edit",
				"tinyMCE" 	=> 	TRUE,
				"sections" 	=> 	$this->pages->get_sections(array('is_section' => 1)),
				"errors"	=>	$errors
			)
		);
	}
	
	// Delete page
	public function delete(){
	
		// Get page id
		$idpage = (int) $this->parameters[0];
		
		// Load model
		$this->load->model('Pages_model', 'pages');
		
		// Remove record
		$this->pages->delete_page($idpage);
		
		// Terminate
		exit("ok");
	}
	
	// Change active
	public function active(){
		
		// Load model
		$this->load->model('Pages_model', 'pages');
		
		// Update page
		$this->pages->save_page(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}

	
	// Add section
	public function add_section(){
	
		// Vars array
		$vars = array();
		
		// Load model
		$this->load->model('Pages_model', 'pages');
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;

			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			} 
			
			// Form slug
			$slug = $this->input->post("slug");
			
			// Check for a project with this slug
			$slug_check = $this->pages->get_sections(array("slug" => $slug, 'is_section' => 1));
			if($slug_check){
				$vars['errors'][] = "The page is already added, please change it's title";
			}
			
			// If we don't have any errors
			if(empty($vars['errors'])){

				// Add section data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"slug"			=>	$this->input->post("slug"),
					"in_foot"		=>	$this->input->post("in_foot"),
					"order_foot"	=>	$this->pages->get_next_order_id(array('in_foot' => $this->input->post("in_foot"))),
					"in_main"		=>	$this->input->post("in_main"),
					"order_main"	=>	$this->pages->get_next_order_id(array('in_main' => $this->input->post("in_main"))),
					"is_section"	=>	1,
					"active"		=>	$this->input->post("active")
				);
				$this->pages->add_page($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The section was added";
				redirect("/administration/pages/");
			}
		}

		// Display template
		$this->view('administration/pages/form_sections', $vars);
	}
	
	
	// Edit section
	public function edit_section(){
	
		// Errors array
		$errors = array();
			
		// Get section id
		$idsection = (int) $this->parameters[0];

		// Load pages model
		$this->load->model('Pages_model', 'pages');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// Check title
			if(empty($_POST['title'])){
				$errors[] = "Title can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Update data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"slug"			=>	$this->input->post("slug"),
					"in_foot"		=>	$this->input->post("in_foot"),
					"in_main"		=>	$this->input->post("in_main"),
					"is_section"	=>	1,
					"active"		=>	$this->input->post("active")
				);
				
				// Update section data
				$this->pages->add_page($data, $idsection);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The section was saved";
				redirect("/administration/pages/");
			}
		}

		// Display template
		$this->view('administration/pages/form_sections', 
			array(
				"post" 		=> 	(array) reset($this->pages->get_sections(array("idsection" => $idsection))),
				"action"	=>	"edit",
				"errors"	=>	$errors
			)
		);
	}
	
	// Change active for section
	public function active_section(){
		
		// Load model
		$this->load->model('Pages_model', 'pages');
		
		// Update section
		$this->pages->save_section(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	// Delete section
	public function delete_section(){
	
		// Get section id
		$idsection = (int) $this->parameters[0];
		
		// Load model
		$this->load->model('Pages_model', 'pages');
		
		// Remove record
		$this->pages->delete_section($idsection);
		
		// Terminate
		exit("ok");
	}
}