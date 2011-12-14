<?php 

// Projects class
class Projects_Module extends CI_Module {

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
	
	public function reports() {
		$vars = array();
		$project_id = $user_id = 0;
		$where = '';
		
		$uri = $this->uri->segment_array();
		if(isset($uri[5])) {	
			if(strstr($uri[5], 'p-'))
				$project_id = (int)(str_replace('p-', '', $uri[5]));
			else if(strstr($uri[5], 'u-'))
				$user_id = (int)(str_replace('u-', '', $uri[5]));
		}	
			
		if($project_id) 
			$where = ' WHERE project_id = '.$project_id;
		if($user_id) 
			$where = ' WHERE user_id = '.$user_id;
			
		$vars['items'] = $this->db->query('	
				SELECT pr.*, p.title AS project_title, u.username AS username
				FROM `projects_reports` AS pr
				INNER JOIN `projects` AS p ON p.idproject = pr.project_id
				INNER JOIN `users` AS u ON u.iduser = pr.user_id
				'.$where.'
				ORDER BY date DESC
			')->result();
			
		$this->load->view('administration/header');
		$this->load->view('administration/reports/browse', $vars);
		$this->load->view('administration/footer');
	}
	
	// Index action
	public function index($temp_method = "", $string = "", $temp_status = "", $status = "", $temp_page = "", $page = 0) {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Create where clause array
		$where = array();
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['title'] = "%".$string."%' OR outcome LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['title'] = "%".$string."%' OR outcome LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}
		
		// If browsing by status
		if(empty($status)){
			$status = $this->input->post('status');
			if ( $this->uri->segment(4) == 'editors-pick' )
				$status = 'editors_pick';
			if(!empty($status)){
				if($status == 'editors_pick') {
					$where['editors_pick'] = '1';
				} else {
					$where['status'] = $status;
				}
			} else {
				$status = "all";
			}
		} elseif($status != "all") {
			$where['status'] = $status;
		}


		// Assign the status
		if($status != "all"){
			$vars['status'] = $status;
		}

		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/projects/index/string/'.$string.'/status/'.$status.'/page/');
		$pager['total_rows'] = $this->projects->count_projects($where);
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Column title is ambigious
		if(!empty($where['title'])){
			$where['p.title'] = $where['title'];
			unset($where['title']);
		}
		
		// Get project data
		$order = "idproject";
		if ($status == 'editors_pick') { $order = 'p.order desc, p.date_created'; } // Ordered like on home page
		$vars['items'] = $this->projects->get_projects($where, array("from" => $pager['start_row'], "count" => $pager['per_page']), array("by" => $order, "type" => "desc"));
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Get projects stats
		$vars['stats'] = $this->projects->get_projects_stats();
		
		// Count pledges
		foreach($vars['items'] AS &$project){
			$project->pledges_count = count($this->projects->get_project_pledges(array("pl.idproject" => $project->idproject)));
		}
		
		// Display template
		$this->view('administration/projects/browse', $vars);
	}

	// Edit project
	public function edit(){
		
		// Errors array
		$errors = array();
		
		// Get project id
		$idproject = (int) $this->parameters[0];

		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		$this->load->model('Pages_model', 'pages');

		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// Upload celebrity image
			if(!empty($_FILES['celebrity_image']['name'])) {
				// Add some headers to prevent caching
				$cache_time = mktime(0,0,0,15,2,2004);
				header("last-modified: " . gmdate("D, d M Y H:i:s", $cache_time) . " GMT");

				$ext = strtolower(end(explode(".", $_FILES['celebrity_image']['name'])));
				
				// Check file size
				if (($_FILES['celebrity_image']['size'] / 2048) > 2048) {
					$errors[] = "Image size should be less than 2 MB";
				}
				// Check file type
				else if ($ext != "jpg" && $ext != "gif" && $ext != "png") {
					$errors[] = "The image should be JPG, GIF or PNG";
				}
				
				// Form upload path
				$path = SITE_DIR."public/uploads/celebrities/" . $_FILES['celebrity_image']['name'];
				
				$_POST['celebrity_image'] = $_FILES['celebrity_image']['name'];

				// If we don't have errors
				if(empty($errors)){
					// Upload image
					if ( !move_uploaded_file($_FILES['celebrity_image']['tmp_name'], $path) ) {
						$errors[] = "The file upload was unseccessful!";
					}
				}
			} else {
			// If no image is uploaded use the old image
				$project = $this->projects->get_projects(array("idproject" => $idproject));
				$_POST['celebrity_image'] = isset($project[0]->celebrity_image) ?  $project[0]->celebrity_image : ''; 
			}
			
			// Form slug
			$slug = $this->input->post("slug");
			
			// Check for a project with this slug
			$slug_check = $this->pages->get_pages(array("slug" => $slug));
			if($slug_check){
				$errors[] = "We found a page with this slug, please change it";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Save project categories
				$this->projects->save_project_categories($idproject, $_POST['categories']);
				
				// Remove form params
				unset($_POST['post_check'], $_POST['submit_x'], $_POST['submit_y'], $_POST['categories']);
				if(isset($_POST['skills']) && is_array($_POST['skills']) && count($_POST['skills'])) 	
					$_POST['skills'] = serialize($_POST['skills']);
					
				
				// Update project
				$this->projects->save_project($_POST, $idproject);

				// Assign success message and redirect back
				$_SESSION['message'] = "The project was saved";
				redirect("/administration/projects/");
			}
		}

		// Initialize vzaar SWFuploader
		require_once(APPPATH.'third_party/vzaar/Vzaar.php');
		Vzaar::$token = $this->config->config['vzaar_token'];
		Vzaar::$secret = $this->config->config['vzaar_secret'];
		Vzaar::$enableFlashSupport = TRUE;
		
		// Get amounts
		$amounts = $this->projects->get_project_amounts(array("idproject" => $idproject));
		
		// Check amounts
		foreach($amounts AS &$amount){
			$count = $this->projects->count_project_pledges(array("idamount" => $amount->idamount));
			$count = reset($count[0]);
			if($count > 0){
				$amount->used = TRUE;
			} else {
				$amount->used = FALSE;
			}
		}
		
		// Load categories model
		$this->load->model('Categories_model', 'categories');
		
		// Get project data
		$project = (array) reset($this->projects->get_projects(array("idproject" => $idproject)));
		
		// Mark some categories as used
		$selected_categories = array();
		foreach($project['categories'] AS $k => $v){
			$selected_categories[$v->idcategory] = TRUE;
		}

		// Display template
		$this->view('administration/projects/form', 
			array(
				"post" 		=> 	$project,
				"amounts" 	=> 	$amounts,
				"action"	=>	"edit",
				"errors"	=>	$errors,
				"tinyMCE"	=>	TRUE,
				"selected_categories" => $selected_categories,
				"categories" => $this->categories->get_categories(TRUE)
			)
		);
	}
	
	// Delete project
	public function delete(){
	
		// Get project id
		$idproject = (int) $this->parameters[0];
		
		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Remove record
		$this->projects->delete_project($idproject);
		
		// Terminate
		exit("ok");
	}
	
	// Change active
	public function active(){
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Update project
		$this->projects->save_project(array("active" => $_POST['active']), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	// Change picks
	public function picks(){
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		// Update project
		if ($_POST['active']) {
			$new_order = $this->projects->get_new_order();
			$where = array("editors_pick" => $_POST['active'], "order" => $new_order);
		} else {
			$where = array("editors_pick" => $_POST['active']);
		}
		$this->projects->save_project($where, $this->input->post("id"));
		// Terminate
		exit("ok");
	}
	
	// Autocomplete project
	public function autocomplete(){
		
		// Get field value
		$project = $this->input->get("term");
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get projects
		$projects = $this->projects->get_projects(array("title" => $project."%"));
		
		// Return array
		$return = array();
		
		// Regroup
		foreach($projects AS $project){
			$return[] = array(
				"id" => $project->idproject,
				"val" => $project->title,
				"label" => $project->title
			);
		}
		
		// Return JSON encoded results
		echo json_encode($return);
		
	}
	
	// Comments action
	public function comments($temp_method = "", $string = "", $temp_idproject = "", $idproject = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Create where clause array
		$where = array();
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['pc.title'] = "%".$string."%' OR pc.text LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['pc.title'] = "%".$string."%' OR pc.text LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}
		
		// If browsing by idproject
		if(empty($idproject)){
			$idproject = $this->input->post('idproject');
			if(!empty($idproject)){
				$idproject = $this->input->post('idproject');
				$where['p.idproject'] = $idproject;
			} else {
				$idproject = "all";
			}
		} elseif($idproject != "all") {
			$where['p.idproject'] = $idproject;
		}

		// Assign the idproject
		if($idproject != "all"){
			$vars['idproject'] = $idproject;
		}

		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/projects/comments/string/'.$string.'/idproject/'.$idproject.'/page/');
		$pager['total_rows'] = reset(reset($this->projects->count_comments($where)));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * $pager['cur_page'];
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		
		// Get project data
		$vars['items'] = $this->projects->get_comments($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Count pledges
		foreach($vars['items'] AS &$comment){
			$comment->comments_count = reset(reset($this->projects->count_comments(array("pc.idproject" => $comment->idproject))));
		}

		// Display template
		$this->view('administration/projects/comments', $vars);
	}
	
	
	// Updates action
	public function updates($temp_method = "", $string = "", $temp_idproject = "", $idproject = "", $temp_page = "", $page = "") {

		// Load pagination library
		$this->load->library('pagination');
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Create where clause array
		$where = array();
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['pc.title'] = "%".$string."%' OR pc.text LIKE '%".$string."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['pc.title'] = "%".$string."%' OR pc.text LIKE '%".$string."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}
		
		// If browsing by idproject
		if(empty($idproject)){
			$idproject = $this->input->post('idproject');
			if(!empty($idproject)){
				$idproject = $this->input->post('idproject');
				$where['p.idproject'] = $idproject;
			} else {
				$idproject = "all";
			}
		} elseif($idproject != "all") {
			$where['p.idproject'] = $idproject;
		}

		// Assign the idproject
		if($idproject != "all"){
			$vars['idproject'] = $idproject;
		}

		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/projects/comments/string/'.$string.'/idproject/'.$idproject.'/page/');
		$pager['total_rows'] = reset(reset($this->projects->count_updates($where)));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * $pager['cur_page'];
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		
		// Get project data
		$vars['items'] = $this->projects->get_updates($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Count pledges
		foreach($vars['items'] AS &$comment){
			$comment->comments_count = reset(reset($this->projects->count_updates(array("pu.idproject" => $comment->idproject))));
		}

		// Display template
		$this->view('administration/projects/updates', $vars);
	}
	
	
	// Edit comment
	public function edit_comment(){
		
		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Errors array
		$errors = array();
		
		// Get project id
		$idcomment = (int) $this->parameters[0];
		
		// Get comment data
		$data = (array) reset($this->projects->get_comments(array("idcomment" => $idcomment)));
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// If the title is empty
			if(empty($_POST['text'])){
				$errors = "The text can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Remove form params
				unset($_POST['post_check'], $_POST['submit_x'], $_POST['submit_y']);

				// Update project
				$this->projects->save_comment($_POST, $idcomment);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The comment was saved";
				redirect("/administration/projects/comments/");
				
			// If we have errors
			} else {
				$errors = (array) $errors;
			}
		}

		// Display template
		$this->view('administration/projects/comments_form', 
			array(
				"post" 		=> 	$data,
				"action"	=>	"edit",
				"errors"	=>	$errors,
				"tinyMCE"	=>	TRUE
			)
		);
	}
	
	// Edit update
	public function edit_update(){
		
		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Errors array
		$errors = array();
		
		// Get project id
		$idupdate = (int) $this->parameters[0];
		
		// Get comment data
		$data = (array) reset($this->projects->get_updates(array("idupdate" => $idupdate)));
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// If the title is empty
			if(empty($_POST['text'])){
				$errors = "The text can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Remove form params
				unset($_POST['post_check'], $_POST['submit_x'], $_POST['submit_y']);

				// Update project
				$this->projects->save_update($_POST, $idupdate);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The update was saved";
				redirect("/administration/projects/updates/");
				
			// If we have errors
			} else {
				$errors = (array) $errors;
			}
		}

		// Display template
		$this->view('administration/projects/updates_form', 
			array(
				"post" 		=> 	$data,
				"action"	=>	"edit",
				"errors"	=>	$errors,
				"tinyMCE"	=>	TRUE
			)
		);
	}
	
	// Delete comment
	public function delete_comment(){
	
		// Get comment id
		$idcomment = (int) $this->parameters[1];

		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Remove record
		$this->projects->delete_comment($idcomment);
		
		// Terminate
		exit("ok");
	}
	
	// Delete update
	public function delete_update(){
	
		// Get comment id
		$idupdate = (int) $this->parameters[1];

		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Remove record
		$this->projects->delete_update($idupdate);
		
		// Terminate
		exit("ok");
	}
	
	// Remove resource - image or video
	public function remove_resource($idproject){
		
		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// Update
		$this->projects->save_project(array("ext" => "", "vzaar_idvideo" => "0", "vzaar_processed" => "0"), $idproject);
	}
	
	
	// Add amount
	public function add_amount($idproject){
		
		// Errors array
		$errors = array();
		
		// Get project id
		$idproject = (int) $this->parameters[0];

		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// If the amount
			if(empty($_POST['amount'])){
				$errors[] = "The amount field can't be empty";
			} else if(!is_numeric($_POST['amount'])){
				$errors[] = "The amount value must be integer";
			} else if($_POST['limited'] == "yes"){
				if(empty($_POST['number'])){
					$errors[] = "The number field can't be empty";
				} else if(!is_numeric($_POST['number'])){
					$errors[] = "The number value must be integer";
				}
			} else if(empty($_POST['description'])){
				$errors[] = "The description field can't be empty";
			}
			
			// If we don't have errors
			if(empty($errors)){
			
				// Add data
				$data = array(
					"amount"		=>	$_POST['amount'],
					"limited"		=>	$_POST['limited'],
					"number"		=>	(!empty($_POST['limited'])) ? $_POST['number'] : "0",
					"text"			=>	h(st($_POST['description']))
				);
				$this->projects->add_project_amount($idproject, $data);
				
				// Redirect back
				redirect("/administration/projects/edit/".$idproject."/");
			}
			
		}

		// Display template
		$this->view('administration/amounts/form', 
			array(
				"action"	=>	"add",
				"errors"	=>	$errors,
				"post"		=>	(!empty($_POST)) ? $_POST : FALSE,
				"tinyMCE"	=>	TRUE
			)
		);
	}
	
	// Remove amount
	public function remove_amount($idamount){
	
		// Load model
		$this->load->model('Projects_model', 'projects');

		// Remove amount
		$this->projects->delete_project_amount($idamount);
		
		// Show success
		echo "ok";
	}
	
	// Edit amount
	public function edit_amount($idamount, $idproject){
		
		// Errors array
		$errors = array();
		
		// Load model
		$this->load->model('Projects_model', 'projects');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// If the amount
			if(empty($_POST['amount'])){
				$errors[] = "The amount field can't be empty";
			} else if(!is_numeric($_POST['amount'])){
				$errors[] = "The amount value must be integer";
			} else if($_POST['limited'] == "yes"){
				if(empty($_POST['number'])){
					$errors[] = "The number field can't be empty";
				} else if(!is_numeric($_POST['number'])){
					$errors[] = "The number value must be integer";
				}
			} else if(empty($_POST['description'])){
				$errors[] = "The description field can't be empty";
			}
			
			// If we don't have errors
			if(empty($errors)){
			
				// Add data
				$data = array(
					"amount"		=>	$_POST['amount'],
					"limited"		=>	$_POST['limited'],
					"number"		=>	(!empty($_POST['limited'])) ? $_POST['number'] : "0",
					"description"	=>	h(st($_POST['description']))
				);
				$this->projects->save_project_amount($data, $idamount);
				
				// Redirect back
				redirect("/administration/projects/edit/".$idproject."/");
			}
			
		}
		
		// Display template
		$this->view('administration/amounts/form', 
			array(
				"action"	=>	"add",
				"errors"	=>	$errors,
				"post"		=>	(array) reset($amounts = $this->projects->get_project_amounts(array("idamount" => $idamount))),
				"tinyMCE"	=>	TRUE
			)
		);
	}
		// Sort projects
	public function sort(){
		
		// Get params from URL
		$direction = $this->parameters[0];
		$idproject = $this->parameters[2];
		
		// Load categories model
		$this->load->model('Projects_model', 'projects');
		
		// Sort categories
		$this->projects->move_project($idproject, $direction);

		// Redirect back
		redirect("/administration/projects/index/editors-pick");
	}
	
}