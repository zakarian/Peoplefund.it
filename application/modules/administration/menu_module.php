<?php 

// Menu class
class Menu_Module extends CI_Module {

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
	
		// Load model and try to get menu secitons
		$this->load->model('Menu_model', 'menu');
 	
		// Get menu
		$vars['menus'] = $this->menu->get_menus(0);

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/menus/browse', $vars);
	}
	
	// Add menu
	public function add(){
	
		// Vars array
		$vars = array();
		
		// Load model
		$this->load->model('Menu_model', 'menu');
		$this->load->model('Pages_model', 'pages');
	
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// Assign post vars
			$vars['post'] = $_POST;

			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			} 
			
			// If we don't have any errors
			if(empty($vars['errors'])){

				// Add email data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"idsubmenu"		=>	$this->input->post("idsubmenu"),
					"active"		=>	$this->input->post("active"),
					"order"			=>	$this->menu->get_last_order_id($this->input->post("idsubmenu"))
				);
				
				// If using page
				if($_POST['target'] == "page"){
					$data['idpage'] = $this->input->post("idpage");
				} else if($_POST['target'] == "url"){
					$data['url'] = $this->input->post("url");
				} else {
					$data['action'] = $this->input->post("action");
				}
				
				// Add menu
				$this->menu->add_menu($data);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The menu was added";
				redirect("/administration/menu/");
			}
		}
		
		// Get menus
		$vars['menus'] = $this->menu->get_menus(0);
		$vars['pages'] = $this->pages->get_pages();
	
		// Display template
		$this->view('administration/menus/form', $vars);
	}
	
	// Edit menu
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get menu id
		$idmenu = (int) $this->parameters[0];

		// Load menu model
		$this->load->model('Menu_model', 'menu');
		$this->load->model('Pages_model', 'pages');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Check title
			if(empty($_POST['title'])){
				$errors[] = "Title can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){

				// Add email data
				$data = array(
					"title"			=>	$this->input->post("title"),
					"idsubmenu"		=>	$this->input->post("idsubmenu"),
					"active"		=>	$this->input->post("active")
				);
				
				// If using page
				if($_POST['target'] == "page"){
					$data['idpage'] = $this->input->post("idpage");
					
					// Remove old url if there is any
					$this->menu->save_menu(array("url" => ""), $idmenu);
					$this->menu->save_menu(array("action" => ""), $idmenu);
					
				// If using url
				} else if($_POST['target'] == "url"){
					$data['url'] = $this->input->post("url");
					
					// Remove old page url if there is any
					$this->menu->save_menu(array("idpage" => "0"), $idmenu);
					$this->menu->save_menu(array("action" => "0"), $idmenu);
					
				// If using prepared actions
				} else if($_POST['target'] == "action"){
					$data['action'] = $this->input->post("action");
					
					// Remove old page action if there is any
					$this->menu->save_menu(array("idpage" => "0"), $idmenu);
					$this->menu->save_menu(array("url" => ""), $idmenu);
				}

				// Update data
				$this->menu->save_menu($data, $idmenu);
				
				// Fix new category order ids
				$this->menu->refill_orders($this->input->post("idsubmenu"));
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The menu was saved";
				redirect("/administration/menu/");
			}
			
		}

		// Display template
		$this->view('administration/menus/form', 
			array(
				"post" 		=> 	(array) $this->menu->get_menu($idmenu),
				"action"	=>	"edit",
				"pages" 	=> 	$this->pages->get_pages(),
				"menus" 	=> 	$this->menu->get_menus(0),
				"errors"	=>	$errors
			)
		);
	}
	
	// Sort menus
	public function sort(){
		
		// Get params from URL
		$direction = $this->parameters[0];
		$idmenu = $this->parameters[2];
		
		// Load model
		$this->load->model('Menu_model', 'menu');
		
		// Sort categories
		$this->menu->move_menu($idmenu, $direction);
		
		// Redirect back
		redirect("/administration/menu/");
	}
	
	// Delete menu
	public function delete(){
	
		// Get menu id
		$idmenu = (int) $this->parameters[0];
		
		// Load model and try to get menu data
		$this->load->model('Menu_model', 'menu');
		
		// Remove record
		$this->menu->delete_menu($idmenu);
		
		// Terminate
		exit("ok");
	}
	
	// Change active
	public function active(){
		
		// Load menus model
		$this->load->model('Menu_model', 'menu');
		
		// Update user
		$this->menu->save_menu(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}

}