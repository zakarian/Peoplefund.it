<?php   

// Include abstract controller
require_once(APPPATH.'core/base_controller.php');

/**
* Projects controller
* Handles project set-up, add, edit, project page etc.
*
* @package PeopleFund
* @category Projects
* @author MTR Design
* @link http://peoplefund.it
*/

// Projects controller
class Projects extends Base_Controller {

	var $per_page = 12;
	
	// Index action
	public function index(){
		$vars = array();
		
		// Load categories model and projects model
		$this->load->model('Categories_model', 'categories');
		$this->load->model('Projects_model', 'projects');
		
		// Get categories
		$vars['categories'] = $this->categories->get_categories();

		// Get picks projects
		$this->projects->limit = 2;
		$vars['picks_projects'] = $this->projects->get_picks_projects_home();
		foreach($vars['picks_projects'] as $project) $this->projects->reservedIds[] = $project->idproject;	
		
		// Get most liked projects
		$this->projects->limit = 2;
		$vars['most_liked'] = $this->projects->get_liked_projects_home();
		foreach($vars['most_liked'] as $project) $this->projects->reservedIds[] = $project->idproject;	
		
		// Get ending soon projects
		$this->projects->limit = 2;
		$vars['ending_soon'] = $this->projects->get_ending_projects_home();
		foreach($vars['ending_soon'] as $project) $this->projects->reservedIds[] = $project->idproject;	
		
		// Get recent projects
		$this->projects->limit = 2;
		$vars['recent_projects'] = $this->projects->get_recent_projects_home();

		// Get cities
		$vars['cities'] = $this->projects->get_projects_cities();

		$vars['current_page'] = "projects";
		$vars['page_title'] = "Projects";
		$vars['searching'] = FALSE;
		
		// Display template
		$this->view('projects/search', $vars);
	}
	
	// Report project
	public function report() {
		$vars = array();
		
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			
			// Get current URL parts
			$uri = $this->uri->segment_array();

			// Validate args
			if(!isset($uri[4]) OR (isset($uri[4]) && !is_numeric($uri[4])))
				$vars['errors'][] = 'Please select a project.';
			
			$uri[4] = (int)$uri[4];		
			$project = $this->db->query('	
				SELECT *
				FROM `projects` 
				WHERE `idproject` = "'.$uri[4].'" AND `active` = 1 AND `status` = "open"
			')->num_rows();
			
			if(!$project)
				$vars['errors'][] = 'Please select a project.';
				
			// Check if user logged in
			if(!isset($_SESSION['user']['iduser']) OR  (isset($_SESSION['user']['iduser']) && !is_numeric($_SESSION['user']['iduser'])))
				$vars['errors'][] = 'Please login first.';
			
			$_SESSION['user']['iduser'] = (int)$_SESSION['user']['iduser'];
			if(!isset($vars['errors'])) {
				$user = $this->db->query('	
					SELECT *
					FROM `users` 
					WHERE `iduser` = "'.(int) $_SESSION['user']['iduser'].'" AND `confirmed` = 1 AND `active` = 1
				')->num_rows();
				if(!$user)
					$vars['errors'][] = 'Please login first.';
			}
				
			if(!isset($vars['errors']) && (!isset($_POST['report_project']) OR (isset($_POST['report_project']) && !$_POST['report_project'])))
				$vars['errors'][] = 'Please select why you are reporting this project.';
			
			// Check for valid reason
			if(
				!isset($vars['errors']) && 
				(
					$_POST['report_project'] <> 'illegal_activity' 
					&& $_POST['report_project'] <>  'peoplefund_guidelines' 
					&& $_POST['report_project'] <>  'wrong_category' 
					&& $_POST['report_project'] <>  'other'
				)
			)
				$vars['errors'][] = 'Please select why you are reporting this project.';
				
			if(!isset($vars['errors']) && $_POST['report_project'] == 'other' && (!isset($_POST['text']) OR (isset($_POST['text']) && !$_POST['text'])))
				$vars['errors'][] = 'Please fill in other field.';
			
			// Insert report
			if(!isset($vars['errors'])) {
				$_POST['text'] = h(st($_POST['text']));
				$data = array(
					'project_id' 	=> $uri[4],
					'user_id' 		=> $_SESSION['user']['iduser'],
					'type' 			=> $_POST['report_project'],
					'text' 			=> ($_POST['report_project'] == 'other') ? $_POST['text'] : '',
					'host' 			=> $_SERVER['REMOTE_ADDR']
				);
				$this->db->insert('projects_reports', $data); 
				
				// Set the success message
				$vars['success'][] = 'Thanks for your message. We will get back to you shortly.';
			}
			
		}
		
		// If ajax is used
		if(uri_string() && preg_match('/ajax/', uri_string()))
			$vars['ajax'] = TRUE;
		
		// Display template
		$this->view('projects/report', $vars);
	}
	
	// Search project
	public function search(){
		
		// Add vars
		$vars = $search_array = $search = array();
		$vars['searching'] = TRUE;
		$vars['current_page'] = 'projects';
		$order_string = '';
		$page = 0;  
		$vars['filter_type'] = 'latest';
		$vars['searchSql'] = array();
		
		// Load pagination library
		$this->load->library('pagination');
		$this->load->config('pager');
		
		// Load categories model and projects model
		$this->load->model('Categories_model', 'categories');
		$this->load->model('Projects_model', 'projects');

		// Get current URL parts
		$uri = $this->uri->segment_array();
		
		$pager = $this->config->config['pager'];
		
		// Get categories
		$vars['categories'] = $this->categories->get_categories();
		// Get cities
		$vars['cities'] 	= $this->projects->get_projects_cities();

		// If search request
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
		
			// Search by keyword
			if(isset($_POST['keyword']) && $_POST['keyword'] && $_POST['keyword'] <> 'keyword')
				$search[] = "keyword:".urlencode(h(st($_POST['keyword'])));
			
			// Search by category
			if(isset($_POST['category']) && $_POST['category'])
				$search[] = "category:".urlencode(h(st($_POST['category'])));
			
			// Search by string
			if(isset($_POST['string']) && $_POST['string'] && $_POST['string'] <> 'postcode')
				$search[] = "string:".urlencode(h(st($_POST['string'])));
			
			// Order results
			if(!empty($_POST['order']))
				$order_string = 'order/'.urlencode(h(st($_POST['order']))).'/';
		
			// Redirecting
			redirect('/projects/search/'.implode('/', $search).'/'.$order_string);
		}		

		// Generate search string and query
		foreach($uri as $segment){
			if(strstr($segment, 'json')){
				$json = true;
			}
			if(strstr($segment, 'keyword')){
				$vars['searchSql']['keywords'] = str_replace('keyword:', '', $segment);
				$vars['searchSql']['keywords'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['keywords']))));
				$vars['search_string'][] = $segment;
			}
			if(strstr($segment, 'category')){
				$vars['searchSql']['category'] = urldecode(str_replace('category:', '', $segment));
				$vars['searchSql']['category'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['category']))));
				$vars['search_string'][] = $segment;
			}
			if(strstr($segment, 'string')){
				$vars['searchSql']['string'] = urldecode(str_replace('string:', '', $segment));
				$vars['searchSql']['string'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['string']))));
				$vars['search_string'][] = $segment;
			}
		}
		
		foreach($uri as $k => $segment)
			if(strstr($segment, 'page'))
				if(!empty($uri[$k + 1])) $page = intval($uri[$k + 1]);

		// Set filter type by order
		foreach($uri AS $k => $segment)
			if(strstr($segment, 'order')) {
				$order = $uri[$k + 1];
				if($order == 'latest')
					$vars['filter_type'] = 'latest';
				else if($order == 'picks')
					$vars['filter_type'] = 'picks';
				else if($order == 'liked')
					$vars['filter_type'] = 'liked';
				else if($order == 'funded')
					$vars['filter_type'] = 'funded';
				else if($order == 'ending_soon')
					$vars['filter_type'] = 'ending_soon';
			}
			
		if(!empty($vars['search_string']))
			$vars['search_string'] = urlencode(implode('/', $vars['search_string']));
			
		if(empty($vars['search_string']))
			if(!empty($order)) $pager['base_url'] = site_url('/projects/search/order/'.$vars['filter_type'].'/page');
			else $pager['base_url'] = site_url('/projects/search/page');
		else
			if(!empty($order)) $pager['base_url'] = site_url('/projects/search/'.$vars['search_string'].'/order/'.$vars['filter_type'].'/page');
			else $pager['base_url'] = site_url('/projects/search/'.$vars['search_string'].'/page');
		
		$this->projects->limit = 0;	
		$this->projects->offset = 0;	
		$this->projects->filter_type = $vars['filter_type'];	
		$this->projects->searchSql = $vars['searchSql'];	

		// Search by postcode
		if(isset($vars['searchSql']['string'])) {
			$vars['search_location_data'] = $this->projects->public_search_postcode_2_string($vars['searchSql']['string']);

			$this->projects->lat = $vars['search_location_data']['lat'];	
			$this->projects->lng = $vars['search_location_data']['lng'];
		}
		
		// Initialize pager
		$pager['total_rows'] = count($this->projects->public_search());
		$pager['per_page'] = $this->per_page;
		$pager['cur_page'] = $page;
		$pager['start_row'] = $this->per_page * ($pager['cur_page'] / $this->per_page);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		$vars['page'] = ($page == 0) ? 1 : ($page / $this->per_page) + 1;
		
		$this->projects->limit = $this->per_page;	
		$this->projects->offset = $pager['start_row'];
		
		// Get projects by criteria
		$vars['projects'] = $this->projects->public_search();
		
		// Json request from other site
		if(isset($json)) {
			$json = array('projects' => array());

			foreach($vars['projects'] as $k => $project) {
				$categories = array();

				foreach($project->categories as $category)
					$categories[] = array('title' => $category->title, 'slug' => slugify(h(st($category->slug))), 'url' => $this->config->config['base_url'].'projects/search/category:'.$category->slug.'/');

				$json['projects'][$k]['title'] = $project->title;
				$json['projects'][$k]['url'] = $this->config->config['base_url'].$project->slug.'/';
				$json['projects'][$k]['description'] = $project->outcome;
				
				$json['projects'][$k]['categories'] = $categories;
				
				$json['projects'][$k]['editors_pick'] = $project->editors_pick;
				$json['projects'][$k]['celebrity_backed'] = @$project->celebrity_backed;
				$json['projects'][$k]['celebrity_title'] = @$project->celebrity_title;
				
				$json['projects'][$k]['amount'] = $project->amount;
				$json['projects'][$k]['amount_pledged'] = $project->amount_pledged;
				$json['projects'][$k]['pledged_percent'] = $project->pledged_percent;

				$json['projects'][$k]['funding_period'] = $project->period;
				$json['projects'][$k]['days_left'] = $project->days_left;
				$json['projects'][$k]['deadline'] = $project->deadline;
				// $json['projects'][$k]['pledged_days'] = $project->pledged_days;
				
				$json['projects'][$k]['owner'] = $this->config->config['base_url'].'user/'.$project->username.'/';
				$json['projects'][$k]['owner_url'] = $this->config->config['base_url'].'user/'.$project->user_slug.'/';
				
				$json['projects'][$k]['location'] = $project->location_preview;
				$json['projects'][$k]['location_town'] = $project->town_name;
				$json['projects'][$k]['location_county'] = $project->county_name;
				$json['projects'][$k]['location_lat'] = $project->lat;
				$json['projects'][$k]['location_lng'] = $project->lng;
				
				if(!empty($project->ext) && file_exists('uploads/projects/'.$project->idproject.'_211x130.jpg')) 
					$json['projects'][$k]['thumb'] = $this->config->config['base_url'].'uploads/projects/'.$project->idproject.'_211x130.jpg';
				else
					$json['projects'][$k]['thumb'] = DEFAULT_PROJECT_THUMB;
			}

			// Display template
			print json_encode($json['projects']);
			exit;
		}

		// Show results
		if(isset($vars['searchSql']['string'])) {
			$vars['show_map'] = TRUE;
			$this->view('projects/browse', $vars);
		} else {
			$vars['show_map'] = FALSE;
			$this->view('projects/search', $vars);
		}
		
	}
	
	// Show projects by order
	public function do_featured($title, $projects) {
		$vars = array();
		$vars['searching'] = TRUE;
		
		// Load categories model and projects model
		$this->load->model('Projects_model', 'projects');
		$this->load->model('Categories_model', 'categories');
		
		// Get categories
		$vars['categories'] = $this->categories->get_categories();

		// Get cities
		$vars['cities'] = $this->projects->get_projects_cities();

		// Set projects
		$vars['projects'] = $projects;
		$vars['current_page'] = 'projects';
		$vars['current_page_h1'] = $title;
		
		// Show results
		$this->view('projects/search', $vars);
	}
	
	// Show most watched
	public function most_watched($temp = "", $page = 0){
		$title = 'Most Watched';

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		$this->projects->limit = 12;
		
		// Get the projects
		$projects = $this->projects->get_liked_projects_home();

		$this->do_featured($title, $projects);
	}
	
	// Show most recent
	public function most_recent($temp = "", $page = 0){
		$title = 'Most Recent';
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		$this->projects->limit = 12;
		
		// Get the projects
		$projects = $this->projects->get_recent_projects_home();
		
		$this->do_featured($title, $projects);
	}
	
	// Show most funded
	public function most_funded($temp = "", $page = 0){
		$title = 'Most Funded';

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		$this->projects->limit = 12;
		
		// Get the projects
		$projects = $this->projects->get_funded_projects_home();
		
		$this->do_featured($title, $projects);
	}
	
	// Show our pick
	public function our_picks($temp = "", $page = 0){
		$reservedIds = array();
		$title = 'Our Picks';

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		$this->projects->limit = 12;
		
		// Get the projects
		$projects = $this->projects->get_picks_projects_home();
		
		$this->do_featured($title, $projects);
	}
	
	public function ending_soon($temp = "", $page = 0){
		$reservedIds = array();
		$title = 'ENDING SOON';

		// Load projects model
		$this->load->model('Projects_model', 'projects');

		$this->projects->limit = 12;
		
		// Get the projects
		$projects = $this->projects->get_ending_projects_home();

		$this->do_featured($title, $projects);
	}
	
	// Display checklist template
	public function checklist(){ 
		$vars = array();
		unset($_SESSION['terms_policy']);
		
		$vars['current_page'] = "projects_add";
		
		// Load pages model
		$this->load->model('Pages_model', 'pages');
		
		$vars['page'] = $this->pages->get_pages(array('slug' => 'checklist'));
		
		// Check for accepted terms
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			if(isset($_POST['conditions']) && $_POST['conditions'] == 1 && isset($_POST['privacy']) && $_POST['privacy'] == 1) {
				$_SESSION['terms_policy'] = TRUE;
				
				// Redirect
				redirect('/projects/add/');
			} else {
				// Detect and store errors
				if ($this->input->post('conditions') !== '1') 
					$vars['errors'][] = 'Please check "I accept the peoplefund.it term and conditions" checkbox';
				if ($this->input->post('privacy') !== '1') 
					$vars['errors'][] = 'Please check "I accept the peoplefund.it privacy policy" checkbox';
			}
		} 
		
		// Display template
		$this->view('projects/checklist', $vars);	
	}
	
	// Add project
	public function add(){
		// Check if user logged
		if(!is_logged()) redirect('/');
		
		// Check for accepted terms
		if(!isset($_SESSION['terms_policy'])) redirect('/projects/checklist/');

		// Check for confirmed user account
		if($_SESSION['user']['confirmed'] == '0') redirect('/user/confirm_account/');

		$vars = array();
		
		// Load categories model and projects model
		$this->load->model('Projects_model', 'projects');
		$this->load->model('Categories_model', 'categories');
		
		// Get categories
		$vars['categories'] = $this->categories->get_categories(TRUE);
		
		// Try to add if the form is submitted 
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			// Validate all fields
			$vars['errors'] = $this->projects->_validation($_POST);
			
			// Add post data to the template
			$vars['post'] = $_POST;

			if(empty($vars['errors'])){
			// Form the project array
				$data = array(
					'iduser'			=>	$_SESSION['user']['iduser'],
					'title'				=>	$_POST['title'],
					'slug'				=>	$_POST['slug'],
					'websites'			=>	implode('|', $_POST['websites']),
					'outcome'			=>	$_POST['outcome'],
					'time'				=>	$_POST['time'],
					'skills'			=>	$_POST['skills'],
					'amount'			=>	$_POST['amount'],
					'about'				=>	$_POST['about'],
					'pledge_more'		=>	(string) $_POST['pledge_more'],
					'helpers'			=>	(string) $_POST['helpers'],
					'period'			=>	$_POST['period'],
					'postcode'			=>	$_POST['postcode'],
					'date_created'		=>	$_POST['date_created'],
					'hostname'			=>	$_POST['hostname'],
					'vzaar_idvideo' 	=>	$_POST['vzaar_idvideo'],
					'vzaar_processed' 	=>	(string) 0,
					'embed'				=>	$_POST['add_project']['embed'],
					'ext'				=>	$_POST['add_project']['ext'],
					'status'			=>	'temp',
					'active'			=>	1,
					'town_name'			=>	$_POST['location_data']['town_name'],
					'county_name'		=>	$_POST['location_data']['county_name'],
					'location_preview'	=>	$_POST['location_data']['location_preview'],
					'lat'				=>	$_POST['location_data']['lat'],
					'lng'				=>	$_POST['location_data']['lng'],
				);
				
				// Add the new project to the database
				$idproject = $this->projects->add_project($data);
				// Add the project categories to the database
				$this->projects->add_project_categories($idproject, $_POST['categories']);
				
				// Generate thumbnails
				if(isset($_SESSION['add_project']['external_source'])) {
					if(copy($_SESSION['add_project']['thumb'], SITE_DIR.'public/uploads/projects/'.$idproject.'.jpg'))
						make_project_thumb(SITE_DIR.'public/uploads/projects/'.$idproject.'.jpg');
				}
				
				// Add project amounts
				if (isset($_POST['amounts']) && !empty($_POST['amounts'])){
					foreach($_POST['amounts'] as $k => $amount){
						$data = array();
						$data['text'] = $_POST['amounts_descriptions'][$k];
						$data['limited'] = $_POST['amounts_limited'][$k];
						$data['number'] = $_POST['amounts_numbers'][$k];
						$data['amount'] = $amount;
						$this->projects->add_project_amount($idproject, $data);
					}
				}
				
				// Remove some stored into the session variables
				unset($_SESSION['terms_policy']);
				unset($_SESSION['vzaar_idvideo']);
				unset($_SESSION['add_project']['external_source']);
				unset($_SESSION['add_project']['embed']);
				unset($_SESSION['add_project']['ext']);
				
				// Redirect
				if(!empty($_POST['save'])) redirect('/projects/my/');
				else redirect('/projects/gocardless/'.$idproject.'/');
			}
		}

		$vars['post']['embed'] = isset($_POST['add_project']['embed']) ? $_POST['add_project']['embed'] : '';

		$vars['action'] = 'add';
		$vars['current_page'] = 'projects_add';
		
		// Load Vzaar api
		require_once(APPPATH.'third_party/vzaar/Vzaar.php');
		Vzaar::$token = $this->config->config['vzaar_token'];
		Vzaar::$secret = $this->config->config['vzaar_secret'];
		Vzaar::$enableFlashSupport = TRUE;
		
		// Display template
		$this->view('projects/add', $vars);
	}
	
	
	// Set up GC account project
	public function gocardless($idproject){
	
		// Current page
		$vars['current_page'] = "projects_add";
		$vars['current_step'] = "gocardless";
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Check project owner and project status
		$project_data = reset($this->projects->get_projects(array("p.idproject" => $idproject)));

		if($_SESSION['user']['iduser'] != $project_data->iduser){
			redirect("/");
		}
		if($project_data->status != "temp"){
			redirect("/");
		}

		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();
		
		$vars['gocardless']['app_id'] = $GoCardless->app_id;
		$vars['gocardless']['oauth_authorize_url'] = $GoCardless->oauth_authorize_url;
		$vars['gocardless']['redirect_uri'] = $this->config->config['base_url'] . 'processor/add_merchant/';
		$vars['gocardless']['scope'] = 'manage_merchant';
		$vars['gocardless']['response_type'] = 'code';

		// If trying to post project data
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Add post data to the template
			$vars['post'] = $_POST;

			// Check GoCardless account
			if(!isset($_POST['merchant_id']) OR empty($_POST['merchant_id']) OR !isset($_POST['access_token']) OR empty($_POST['access_token'])){
				$vars['errors'][] = "Please associate your project with GoCardless account";
			}
			
			// If there are no errors
			if(empty($vars['errors'])){
			
				// Form the update array
				$data = array(
					"merchant_id"		=>	(int) $this->input->post("merchant_id"),
					"access_token"		=>	$this->input->post("access_token")
				);
				
				// Update project data
				$this->projects->save_project($data, $idproject);

				if(isset($_POST['save']) && !empty($_POST['save'])) redirect("/projects/my/");
				redirect("/projects/preview/".$idproject."/");
			} else {
				
				// Form the project array
				$project = array(
					"title"	=>	$project_data->title,
					"merchant_id" => (int) $this->input->post("merchant_id"),
					"access_token" => $this->input->post("access_token"),
					"vzaar_idvideo"	=>	$project_data->vzaar_idvideo,
					"idproject"	=>	$project_data->idproject,
					"status"	=>	$project_data->status
				);
				$vars['project'] = (object) $project;
			}

		// If not posting data
		} else {
		
			// Get project data
			$vars['project'] = $project_data;
		}
		
		// Display template
		$this->view('projects/gocardless', $vars);
	}
	
	
	// Preview project
	public function preview($idproject){
	
		// Current page
		$vars['current_page'] = "projects_add";
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Check project owner and project status
		$project_check = reset($this->projects->get_projects(array("p.idproject" => $idproject)));
		if($_SESSION['user']['iduser'] != $project_check->iduser){
			redirect("/");
		}
		if($project_check->status != "temp"){
			redirect("/");
		}

		// Initialize vzaar SWFuploader
		require_once(APPPATH.'third_party/vzaar/Vzaar.php');
		Vzaar::$token = $this->config->config['vzaar_token'];
		Vzaar::$secret = $this->config->config['vzaar_secret'];
		Vzaar::$enableFlashSupport = TRUE;

		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();
		
		$vars['gocardless']['app_id'] = $GoCardless->app_id;
		$vars['gocardless']['oauth_authorize_url'] = $GoCardless->oauth_authorize_url;
		$vars['gocardless']['redirect_uri'] = $this->config->config['base_url'] . 'processor/add_merchant/';
		$vars['gocardless']['scope'] = 'manage_merchant';
		$vars['gocardless']['response_type'] = 'code';
		
		// Load categories model
		$this->load->model('Categories_model', 'categories');
		$this->load->model('Users_model', 'users');
		$vars['categories'] = $this->categories->get_categories(TRUE);

		
		// If trying to post project data
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
		
			// If now posting websites
			if(empty($_POST['websites'])){
				$_POST['websites'] = array();
			}
			
			// Add post data to the template
			$vars['post'] = $_POST;

			// Check paypal email
			// if(empty($_POST['paypal_email'])){
				// $vars['errors'][] = "Please enter PayPal email address";
			// } else if(!check_email($_POST['paypal_email'])){
				// $vars['errors'][] = "Invalid PayPal email address";
			// }

			// Check GoCardless account
			if(!isset($_POST['merchant_id']) OR empty($_POST['merchant_id'])){
				$vars['errors'][] = "Please associate your project with GoCardless account";
			}
			
			// Check title
			if(empty($_POST['title'])){
				$vars['errors'][] = "Title can't be empty";
			}
			
			// Form slug
			$slug = slugify($_POST['slug']);
			if(empty($slug)){
				$vars['errors'][] = "Please enter project vanity url";
			}
			
			// Check for a project with this slug
			$slug_check = $this->projects->get_projects(array("p.slug" => $slug));
			if($slug_check){
				$slug_check = reset($slug_check);
				if($slug_check->idproject != $idproject)
					$vars['errors'][] = "This slug is already taken, please enter another one";
			}
			
			// Check outcome
			if(empty($_POST['outcome'])){
				$vars['errors'][] = "Please enter outcome";
			}
			
			// Check about
			if(empty($_POST['about'])){
				$vars['errors'][] = "Please enter project information by fillint the about field";
			}
			
			if(
				!isset($_POST['amount']) OR 
				(isset($_POST['amount']) && !is_numeric($_POST['amount'])) OR 
				(isset($_POST['amount']) && ((int) $_POST['amount'] < 1000 OR (int) $_POST['amount'] > 50000))
			) $vars['errors']['amount'] = 'Please enter funding target between &pound;1000 and &pound;50,000';

			// Check postcode
			if(empty($_POST['postcode'])){
				$vars['errors'][] = "Please enter postcode";
			}
			$location_data = $this->users->get_location_by_postcode($_POST['postcode']);
			if(empty($location_data)){
				$vars['errors'][] = "Invalid postcode";
			}
			
			// Check amounts for wrong values
			if(!empty($_POST['amounts'])){
				foreach($_POST['amounts'] AS $k => $amount){
			
					// Check descriptions
					if(empty($_POST['amounts_descriptions'][$k])){
						$vars['errors']["empty_description"] = "One of the rewards has empty description";
					}
					
					// Check amounts
					if(empty($_POST['amounts'][$k])){
						$vars['errors']["empty_amount"] = "One of the rewards has empty amount";
					} else if(!is_numeric($_POST['amounts'][$k]) OR ((int) $_POST['amounts'][$k] < 0 OR (int) $_POST['amounts'][$k] > 50000)){
						$vars['errors']["empty_amount"] = "One of the rewards has wrong amount";
					}
					
					// Check limits and numbers
					if($_POST['amounts_limited'][$k] == "yes"){
						if(empty($_POST['amounts_numbers'][$k])){
							$vars['errors']['empty_numbers'] = "One of the rewards is limited and has empty number field";
						}
					}
				}
			}
			
			// Check amounts
			if(empty($_POST['amounts'])){
				$vars['errors']['amounts_number'] = "You need to add at least 1 reward";
			}

			// Check if a category is checked
			if(count($_POST['categories']) == 0){
				$vars['errors'][] = "You should select at least 1 category";
			}
			
			// If there are no errors
			if(empty($vars['errors'])){
			
				// Form the update array
				$data = array(
					//"idcategory"		=>	(int) $this->input->post("idcategory"),
					"title"				=>	$this->input->post("title"),
					"slug"				=>	$slug,
					"websites"			=>	implode("|", $_POST['websites']),
					"outcome"			=>	$this->input->post("outcome"),
					"paypal_email"		=>	$this->input->post("paypal_email"),
					"merchant_id"		=>	(int) $this->input->post("merchant_id"),
					"amount"			=>	(float) $this->input->post("amount"),
					"about"				=>	$this->input->post("about"),
					"pledge_more"		=>	$this->input->post("pledge_more"),
					"period"			=>	intval($this->input->post("period")) * 7,
					"postcode"			=>	$this->input->post("postcode"),
					"hostname"			=>	$_SERVER['REMOTE_ADDR'],
					"active"			=>	"1"
				);
				
				// Add access token to update query
				if(isset($_POST["access_token"]) && !empty($_POST["access_token"])){
					$data['access_token'] = $this->input->post("access_token");
				}
				
				// If saving for later
				if(!empty($_POST['save'])){
					$data['status'] = "temp";
				} else {
					$data['status'] = "open";
					
				// ==== Send email with project submission information ==== //
				
					// Get email data for the project submission email
					$this->load->model('Emails_model', 'emails');
					$email_data = (array) reset($this->emails->get_emails(array("idemail" => "16")));
	
					// Get site title
					$this->load->model('Configuration_model', 'configuration');
					$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "16")));
					$site_title = reset($site_title);
					
					// Params that will be replaced in email subject
					$title_params = array(
						"[project_name]"	=>	$this->input->post("title"),
						"[site_name]"	=>	$site_title
					);					
					
					// Get email config
					$this->load->config('emails');
	
					// Params that will be replaced in the email text
					$text_params = array(
						"[project_name]"	=>	$this->input->post("title"),
						'[site_name]'	=>	$site_title
					);

					// Send wlecome email with confirmation link
					send_mail($this->config->item('FROM_EMAIL'), $_SESSION['user']['email'], $email_data['subject'], $email_data['text'], $title_params, $text_params);

					$_SESSION['new_project'] = true;
					
				// ==== Send email to administrator ==== //

					$email_data = (array) reset($this->emails->get_emails(array("idemail" => "16")));
	
					// Get site title
					$this->load->model('Configuration_model', 'configuration');
					$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "16")));
					$site_title = reset($site_title);
					
					// Params that will be replaced in email subject
					$title_params = array(
						"[project_name]"	=>	$this->input->post("title"),
						"[site_name]"	=>	$site_title
					);					
					
					// Get email config
					$this->load->config('emails');

					// Project link for the administration
					$link = '<a href="'.$this->config->item('base_url').'administration/projects/edit/'.encode_string($idproject).'">'.$this->config->item('base_url').'administration/projects/edit/'.encode_string($idproject).'</a>';
	
					// Params that will be replaced in the email text
					$text_params = array(
						"[project_name]"	=>	$this->input->post("title"),
						'[project_url]'	=>	$link,
						'[site_name]'	=>	$site_title,
					);

					// Send wlecome email with confirmation link
					send_mail($this->config->item('FROM_EMAIL'), $this->config->item('FROM_EMAIL'), $email_data['subject'], $email_data['text'], $title_params, $text_params);
				}

				// If adding video
				if(!empty($_SESSION['vzaar_idvideo'])){
					$data['vzaar_idvideo'] = $_SESSION['vzaar_idvideo'];
				}
				
				// Update project data
				$this->projects->save_project($data, $idproject);
				
				// Save project categories
				$this->projects->save_project_categories($idproject, $_POST['categories']);
				
				// Remove all amounts to add new ones later
				$this->projects->delete_project_amounts($idproject);
				
				// Update project amounts
				foreach($_POST['amounts'] AS $k => $amount){
					$data = array();
					$data['description'] = h(st($_POST['amounts_descriptions'][$k]));
					$data['text'] = h(st($_POST['amounts_descriptions'][$k]));
					$data['limited'] = $_POST['amounts_limited'][$k];
					$data['number'] = $_POST['amounts_numbers'][$k];
					$data['amount'] = $amount;
					$this->projects->add_project_amount($idproject, $data);
				}
				
				
				redirect($slug);
				
			} else {
			
				// Get category name
				//$category_name = reset($this->categories->get_categories(array("idcategory" => $_POST['idcategory'])));
				$project_data = reset($this->projects->get_projects(array("p.idproject" => $idproject)));
				
				// Form the project array
						$project = array(
							"title"	=>	(isset($_POST['title']) && $_POST['title']) ? $_POST['title'] : '',
							"websites"	=>	implode("|", $_POST['websites']),
							"county_name"	=>	$location_data['county_name'],
							"location_preview" 	=> $location_data['location_preview'],
							"postcode"	=>	(isset($_POST['postcode']) && $_POST['postcode']) ? $_POST['postcode'] : '',
							"slug"	=>	(isset($_POST['slug']) && $_POST['slug']) ? $_POST['slug'] : '',
							"paypal_email"	=>	(isset($_POST['paypal_email']) && $_POST['paypal_email']) ? $_POST['paypal_email'] : '',
							"merchant_id"	=>	(isset($_POST['merchant_id']) && $_POST['merchant_id']) ? (int) $_POST['merchant_id'] : 0,
							"access_token"	=>	(isset($_POST['access_token']) && $_POST['access_token']) ? $_POST['access_token'] : '',
							"categories"	=>	$this->categories->get_categories(TRUE),
							"categories_in_project"	=>	(isset($_POST['categories']) && $_POST['categories']) ? $_POST['categories'] : array(),
							"outcome"	=>	(isset($_POST['outcome']) && $_POST['outcome']) ? $_POST['outcome'] : '',
							"about"	=>	(isset($_POST['outcome']) && $_POST['outcome']) ? $_POST['about'] : '',
							"vzaar_idvideo"	=>	$project_data->vzaar_idvideo,
							"vzaar_processed"	=>	$project_data->vzaar_processed,
							"ext"	=>	$project_data->ext,
							"idproject"	=>	$project_data->idproject,
							"period"	=>	(isset($_POST['period']) && $_POST['period']) ? intval($this->input->post("period")) * 7 : '',
							"amount"	=>	(isset($_POST['amount']) && $_POST['amount']) ? $_POST['amount'] : '',
							"pledge_more"	=>	(isset($_POST['pledge_more']) && $_POST['pledge_more']) ? $_POST['pledge_more'] : '',
							"helpers"	=>	(isset($_POST['helpers']) && $_POST['helpers']) ? $_POST['helpers'] : ''
						);
				$vars['project'] = (object) $project;
				
				// Form the amounts array
				if(!empty($_POST['amounts'])){
					foreach($_POST['amounts'] AS $idamount => $amount){
						$amount_row = array(
							"description" => $_POST['amounts_descriptions'][$k],
							"limited" => $_POST['amounts_limited'][$k],
							"number" => $_POST['amounts_numbers'][$k],
							"amount" => $amount,
							"idamount" => $idamount,
						);
						$vars['amounts'][] = (object) $amount_row;
					}
				}
			}

		// If not posting data
		} else {
		
			// Get project data
			$vars['project'] = reset($this->projects->get_projects(array("p.idproject" => $idproject)));
			
			// Get amounts
			$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $idproject));
		}
		
		// Display template
		$this->view('projects/preview', $vars);
	}

	// Add image
	public function add_image(){
		// If the user is not logged in - redirect to index page
		if(empty($_SESSION['user']) OR $_SESSION['user']['confirmed'] == "0"){
			exit;
		}

		// Get extension
		$ext = strtolower(end(explode(".", $_FILES['File']['name'])));

		// Check file size
		if (($_FILES['File']['size'] / 2048) > 2048) {
			$error = "Image size should be less than 2 MB";
		}

		// Check file type
		else if ($ext != "jpg" && $ext != "gif" && $ext != "png") {
			$error = "The image should be JPG, GIF or PNG";
		}

		// If we don't have errors
		if(empty($error)){

			// If we are adding the image for a project - project edit page
			if(!empty($_POST['idproject'])){
			
				// Form upload path
				$path = SITE_DIR."public/uploads/projects/" . (int) $_POST['idproject'] . "." . $ext;
				
				// Upload image
				move_uploaded_file($_FILES['File']['tmp_name'], $path);
				
				// Load projects model
				$this->load->model('Projects_model', 'projects');
				
				// Update project extension
				$this->projects->save_project(array("vzaar_idvideo" => "0", "vzaar_processed" => "0", "ext" => $ext), (int) $_POST['idproject']);
				
				// Make thumb
				make_project_thumb($path);
				
				// Show file
				echo (int) $_POST['idproject'] . "." . $ext;
			
			// If adding a project with new image
			} else {
			
				// Form upload path
				$path_dir = SITE_DIR."public/uploads/projects/";
				$path = $path_dir . $_FILES['File']['name'];
				
				// Upload image
				if (file_exists($path_dir)){
					@mkdir($path_dir, 0777);
				}
				move_uploaded_file($_FILES['File']['tmp_name'], $path);
				
				// Show message
				echo $_FILES['File']['name'];
			}
			
		// If we have errors
		} else {
			exit;
		}
	}

	// Add OEmbed resourse
	function insert_oembed() {
		// If the user is not logged in - redirect to index page
		if(empty($_SESSION['user']) OR $_SESSION['user']['confirmed'] == "0"){
			die(json_encode(array('success' => FALSE, 'errors' => array('auth' => 'You must be logged in user'))));
		}

		$image = @file_get_contents($url);

		if(!empty($image) && !preg_match('/\!DOCTYPE/is', $image)) {
			die(json_encode(array('success' => FALSE, 'msg' => 'Unsupported content type', 'errors' => array('msg' => 'Unsupported content type', 'url' => 'Unsupported content type'))));
		} else {

			static $services = array(
				'youtube.com' => 'http://www.youtube.com/oembed?format=json&maxwidth=624',
				'youtu.be' => 'http://www.youtube.com/oembed?format=json&maxwidth=624',
				'vimeo.com' => 'http://vimeo.com/api/oembed.json?maxwidth=624',
				// 'flickr.com' => 'http://www.flickr.com/services/oembed?format=json&maxwidth=624',
				'vzaar.com' => 'http://api.vzaar.com/videos/{video}.json'
			);
			static $oembed_types = array('video' => 'video');
			
			header('content-type: text/plain; charset=utf-8');
			if (empty($_POST['url']))
				die(json_encode(array('success' => FALSE, 'errors' => array('url' => 'Missing URL'))));
			$url = @$_POST['url'];

			// figure out oembed endpoint
			$host = parse_url($url, PHP_URL_HOST);
			$host = join('.', array_slice(explode('.', $host), -2));

			if (!isset($services[$host]))
				die(json_encode(array('success' => FALSE, 'msg' => 'Unsupported service', 'errors' => array('url' => 'Unsupported service'))));

			// Vzaar patch
			if( $host == 'vzaar.com') {
				$video_id = preg_replace('/[^0-9]/', '', $url);
				$services[$host] = str_replace('{video}', $video_id, $services[$host]);
			}

			// Include HTTP Class
			require_once(APPPATH.'third_party/http/HTTP.php');

			// fetch the embedding code
			$page = HTTP::get($services[$host], array('url' => $url, 'maxwidth' => 624));
			if ($page['code'] != 200 && $page['code'] != 301)
				die(json_encode(array('success' => FALSE, 'msg' => 'Content not found', 'errors' => array('url' => 'Content not found'))));
			$oembed = json_decode($page['content'], TRUE);
			if (!$oembed)
				die(json_encode(array('success' => FALSE, 'msg' => 'Service error', 'errors' => array('url' => 'Service error'))));
			if (!in_array($oembed['type'], array_keys($oembed_types)))
				die(json_encode(array('success' => FALSE, 'msg' => 'Unsupported content type', 'errors' => array('msg' => 'Unsupported content type', 'url' => 'Unsupported content type'))));
			if (($oembed['type'] == 'video') && empty($oembed['html']))
				die(json_encode(array('success' => FALSE, 'msg' => 'No media file was detected', 'errors' => array('url' => 'Could not find embeddable content'))));

			// Forcing youtube videos to use iframe
			if(isset($oembed['provider_url']) && preg_match('/youtube.com/is', $oembed['provider_url'])) {
				// Extract youtube id from thumbnail_url
				$explode = explode('/', $oembed['thumbnail_url']);
				array_pop($explode);
				$id = array_pop($explode);

				$oembed['html'] = '<iframe width="420" height="315" src="http://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
			}

			$resource = array(
				'type' => $oembed_types[$oembed['type']],
				'mime_type' => 'x-oembed/'.$oembed['type'],
				'title' => (empty($oembed['title']) ? 'Untitled '.$oembed['type']: $oembed['title']),
				'member_id' => @$_SESSION['member']['id'],
				'author' => (empty($oembed['author_name'])? '' : $oembed['author_name']),
				'url' => (isset($oembed['url']) ? $oembed['url'] : $url),
				'embed' => (isset($oembed['html']) ? $oembed['html'] : ''),
				'thumbnail' => (isset($oembed['framegrab_url']) ? $oembed['framegrab_url'] : $oembed['thumbnail_url'])
			);
			
			$image = @file_get_contents($resource['thumbnail']);
		}

		// If we are adding the image for a project - project edit page
		if(!empty($_POST['idproject'])){
			// Get extension
			$ext = strtolower(end(explode(".", $resource['thumbnail'])));
			
			if(!preg_match('/(gif|png|jpg|jpeg)/is', $ext))
				$ext = 'jpg';
			
			// Form upload path
			$path = SITE_DIR."public/uploads/projects/" . (int) $_POST['idproject'] . "." . $ext;

			// Store the image
			if($fp = @fopen($path, 'a'))
				fclose( $fp );

			file_put_contents($path, $image);

			// Convert the image
			$cmd = IMAGICK_BINARY_PATH . 'convert ' . $path . ' "' . SITE_DIR."public/uploads/projects/" . (int) $_POST['idproject'] . ".jpg" . '"';
			exec($cmd, $output);

			// Clear tmp files
			if($ext != 'jpg')
				@unlink($path);
				
			// Load projects model
			$this->load->model('Projects_model', 'projects');
				
			// Update project extension
			$this->projects->save_project(array("vzaar_idvideo" => "0", "vzaar_processed" => "0", "embed" => $resource['embed'], "ext" => 'jpg'), (int) $_POST['idproject']);
				
			// Make thumb
			make_project_thumb(SITE_DIR."public/uploads/projects/" . (int) $_POST['idproject'] . ".jpg");
				
			// Show file
			$thumbnail = (int) $_POST['idproject'] . ".jpg";
			
			if($_POST['idproject'] == -1) {		
				$randId = rand();
				copy(SITE_DIR.'public/uploads/projects/'.$_POST['idproject'].'.jpg', SITE_DIR.'public/uploads/projects/temp_'.$randId.'.jpg');
				$_SESSION['add_project']['external_source'] = true;
				$_SESSION['add_project']['thumb'] = SITE_DIR.'public/uploads/projects/temp_'.$randId.'.jpg';
				$_SESSION['add_project']['ext'] = 'jpg';
				$_SESSION['add_project']['embed'] = $resource['embed'];
			}
		// If adding a project with new image
		} else {
			
			// Form upload path
			$path_dir = SITE_DIR."public/uploads/projects/";
			$path = $path_dir . slugify($url);
				
			// Upload image
			if (file_exists($path_dir)){
				@mkdir($path_dir, 0777);
			}
			
			// Store the image
			if($fp = @fopen($path, 'a'))
				fclose( $fp );

			file_put_contents($path, $image);

			// Convert the image
			$cmd = IMAGICK_BINARY_PATH . 'convert ' . $path . ' "' . $path . ".jpg" . '"';
			exec($cmd, $output);

			// Clear tmp files
			@unlink($path);
			
			$thumbnail = slugify($url) . ".jpg";
		}
		
		die(json_encode(array('success' => TRUE, 'msg' => 'ok', 'file' => $thumbnail, 'oembed' => $resource )));
	}
	
	
	// Remove resource - image or video
	public function remove_resource($idproject = 0){
		
		// Load model and try to get project data
		$this->load->model('Projects_model', 'projects');
		
		if(isset($_SESSION['vzaar_idvideo'])) unset($_SESSION['vzaar_idvideo']);
		if(isset($_SESSION['add_project']['embed']))unset($_SESSION['add_project']['embed']);
		if(isset($_SESSION['add_project']['ext']))unset($_SESSION['add_project']['ext']);

		$this->projects->save_project(array("embed" => "", "ext" => "", "vzaar_idvideo" => "0", "vzaar_processed" => "0"), $idproject);
	}
	
	
	// Process vzaar video
	public function process_video(){
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
			
		// Initialize vzaar SWFuploader
		require_once(APPPATH.'third_party/vzaar/Vzaar.php');
		Vzaar::$token = $this->config->config['vzaar_token'];
		Vzaar::$secret = $this->config->config['vzaar_secret'];

		// Prevent access
		if(empty($_POST['guid'])){
			exit("GUID can't be empty");
		}
		
		// Add output header
		header('Content-type: text/html');

		// If the data is correct
		if (isset($_POST['guid'])) {
			$apireply = Vzaar::processVideo($_POST['guid'], "People Fund", "People Fund", "People Fund");
			$idvideo = $apireply;
			$_SESSION['vzaar_idvideo'] = $idvideo;
			
			// If editing project - update the video
			$this->projects->save_project(array("ext" => "", "vzaar_idvideo" => $idvideo), (int) $_POST['idproject']);
			
		// If we can't get GUID
		} else {
			echo('GUID is missing');
		}
	}
	
	// My projects
	public function my(){

		// If the user is not logged in - redirect to index page
		if(empty($_SESSION['user'])){
			redirect('/user/login/');
		}
		redirect('/user/'.$_SESSION['user']['slug'].'/projects/');
	
		// Template params array
		$vars = array();
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		$vars['projects'] = $this->projects->get_projects(array("p.iduser" => $_SESSION['user']['iduser']));
		
		// Display my projects template
		$this->view('projects/my', $vars);
	}
	
	// Add comment
	public function add_comment($slug){
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Check for this project
		$vars['data'] = $this->projects->get_projects(array("p.slug" => $slug));
		
		// If we don't have such project
		if(empty($vars['data'])){
			redirect("/");
		} else {
			$vars['data'] = reset($vars['data']);
		}
		
		// If trying to save project comment
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// If the title is empty
			if(empty($_POST['text'])){
				$vars['error'] = "The text can't be empty";
			}

			// If we have errors - assign POST data
			if(!empty($vars['error'])){
			
				// Assign posted vars
				$_SESSION['error'] = $vars['error'];
				$_SESSION['post'] = $_POST;
			
			// If we don't have errors
			} else {
			
				// Insert comment data
				$data = array(
					"idproject"		=>	$vars['data']->idproject,
					"iduser"		=>	$_SESSION['user']['iduser'],
					"text"			=>	addslashes($this->input->post("text")),
					"date_added"	=>	date("Y-m-d H:i:s")
				);
				$this->projects->add_comment($data);
				
				// Assign success message
				$_SESSION['success'] = "Your message was saved";
			}
			
		}
		
		// Redirect back
		redirect("/".$vars['data']->slug."/comments/");
	}
	
	
	// Add comment
	public function add_update_comment(){
	
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Check text
		if(empty($_POST['text'])) exit;
		
		// Insert comment data
		$data = array(
			"idupdate"		=>	intval($_POST['idupdate']),
			"iduser"		=>	$_SESSION['user']['iduser'],
			"text"			=>	$this->input->post("text"),
			"date_added"	=>	date("Y-m-d H:i:s")
		);
		$this->projects->add_update_comment($data);
	}
	
	
	// Confirm
	public function confirm(){

		if(!is_logged()) redirect('/user/login/');

		$uri = $this->uri->segment_array();

		if(isset($uri[3]) && preg_match('/d:/i', $uri[3])) {
			$data = explode('-', str_replace('d:', '', $uri[3]));
			if(count($data) == 3) {
				$_POST['amount'] = $data[0];
				$_POST['idamount'] = $data[1];
				$_POST['idproject'] = $data[2];
			}
		}
	
		$vars['amount'] 	= empty($_POST['amount']) 		? -1 : (float)$_POST['amount'];
		$vars['idproject'] 	= empty($_POST['idproject']) 	? -1 : (int)$_POST['idproject'];
		$vars['idamount'] 	= empty($_POST['idamount']) 	? -1 : (int)$_POST['idamount'];
		
		if($vars['amount'] < 0) redirect('/404/');
		if($vars['idproject'] < 0) redirect('/404/');
		if($vars['idamount'] < 0) redirect('/404/');

		$this->load->model('Projects_model', 'projects');

		$project = reset($this->projects->get_one_projects($vars['idproject']));
		if(!$project) redirect('/404/');
		
		$amount = reset($this->projects->get_project_amounts(array('idproject' => $vars['idproject'], 'idamount' => $vars['idamount'])));
		if(!$amount) redirect('/404/');
		if($amount->limited == 'yes' && $amount->remaining <= 0) redirect('/'.$project->slug.'/?limited');

		$this->view('projects/confirm', $vars);
	}
	
	// Generate signature on the fly
	public function generate_sugnature(){
		// If the user is not logged in
		if(empty($_SESSION['user'])){
			exit;
		}

		// Check and validate pre_authorization[user] array
		if(isset($_POST['pre_authorization']['user']) && is_array($_POST['pre_authorization']['user'])) {
			foreach($_POST['pre_authorization']['user'] as $key => $value)
				$_POST['pre_authorization[user][' . $key . ']'] = $value;
				
			unset($_POST['pre_authorization']['user']);
		}

		// Check and validate pre_authorization array
		if(isset($_POST['pre_authorization']) && is_array($_POST['pre_authorization'])) {
			foreach($_POST['pre_authorization'] as $key => $value)
				$_POST['pre_authorization[' . $key . ']'] = $value;
				
			unset($_POST['pre_authorization']);
		}

		// Check and validate state array
		if(isset($_POST['state']) && is_array($_POST['state'])) {
			foreach($_POST['state'] as $key => $value)
				$_POST['state[' . $key . ']'] = $value;
				
			unset($_POST['state']);
		}
		
		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();

		print $GoCardless->generate_signature($GoCardless->convert_to_encoded_query($_POST));
		exit;
	}
	
	// PayPal
	public function paypal(){
	
		// If the user is not logged in
		if(empty($_SESSION['user'])){
			redirect("/");
		}
		
		$this->load->model('Projects_model', 'projects');
	
		// Template params array
		$vars = array();
		
		// Include PayPal Approvals Class
		require_once(APPPATH.'third_party/paypal/AdaptivePayments.php');

		// Approval params
		$returnURL 						= 	$this->config->config['base_url']."projects/thanks/";
		$cancelURL 						= 	$this->config->config['base_url']."projects/error/";
		$notificationURL 				= 	$this->config->config['base_url']."projects/ipn/";
		$startingDate					=	date("Y-m-d");
		$endingDate						=	date("Y-m-d", strtotime("+90 DAYS"));
		$maxNumberOfPayments			=	1;
		$maxTotalAmountOfAllPayments	=	$_POST['amount'];
		
		// Create approval object and set params
		$preapprovalRequest = new PreapprovalRequest();
		$preapprovalRequest->cancelUrl = $cancelURL;
		$preapprovalRequest->returnUrl = $returnURL;
		$preapprovalRequest->clientDetails = new ClientDetailsType();
		$preapprovalRequest->clientDetails->applicationId = $this->config->config['application_id'];
		$preapprovalRequest->clientDetails->deviceId = $this->config->config['device_id'];
		$preapprovalRequest->clientDetails->ipAddress = "127.0.0.1";
		$preapprovalRequest->currencyCode = $this->config->config['currency_code'];
		$preapprovalRequest->startingDate = $startingDate;
		$preapprovalRequest->endingDate = $endingDate;
		$preapprovalRequest->maxNumberOfPayments = $maxNumberOfPayments;
		$preapprovalRequest->maxTotalAmountOfAllPayments = $maxTotalAmountOfAllPayments;
		$preapprovalRequest->requestEnvelope = new RequestEnvelope();
		$preapprovalRequest->requestEnvelope->errorLanguage = "en_US";
		//$preapprovalRequest->senderEmail = $_SESSION['user']['email'];
		$preapprovalRequest->ipnNotificationUrl = $notificationURL;

		$ap = new AdaptivePayments();
		$response = $ap->Preapproval($preapprovalRequest);	
		
		//print_r($ap); exit;
		
		// If we have errors
		if(strtoupper($ap->isSuccess) == 'FAILURE'){
		
			// Error
			$error = $ap->getLastError();
			
			// Log sent and returned params
			$logging = array(
				"iduser"	=>	$_SESSION['user']['iduser'],
				"idproject" =>  $_POST['idproject'],
				"idamount"  =>  $_POST['idamount'],
				"command"	=>	"preapproval_request",
				"status"	=>	"error",
				"sent"		=>	serialize($preapprovalRequest),
				"received"	=>	(!empty($response)) ? serialize($response) : "",
				"error"		=>	(!empty($error)) ? serialize($error) : "",
				"date"		=>	date("Y-m-d H:i:s")
			);
			$this->load->model('Logs_model', 'logs');
			$this->logs->add($logging);
			
			// Assign the errors
			$vars['error'] = $error->error->message;
			
			// Display my projects template
			$this->view('projects/error', $vars);

			
		// If we don't have errors
		} else {

			// Redirect to PayPal.com
			$token = $response->preapprovalKey;
			$payPalURL = 'https://www.sandbox.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$token;
			//$payPalURL = 'https://www.paypal.com/webscr&cmd=_ap-preapproval&preapprovalkey='.$token;
			
			// Load projects model
			$this->load->model('Projects_model', 'projects');
			
			// Pledge data
			$data = array(
				"idproject"		=>	$_POST['idproject'],
				"idamount"  	=>  $_POST['idamount'],
				"iduser"		=>	$_SESSION['user']['iduser'],
				"status"		=>	"pending",
				"amount"		=>	$_POST['amount'],
				"email" 		=>  $_SESSION['user']['email'],
				"key"			=>	$response->preapprovalKey,
				"date_added"	=>	date("Y-m-d H:i:s")
			);
			
			// Add pledge data
			$this->projects->add_pledge($data);
			$idpledge = $this->db->insert_id();
			$_SESSION['last_pledge_id'] = $idpledge;
			
			// Log sent and returned params
			$logging = array(
				"iduser"	=>	$_SESSION['user']['iduser'],
				"idproject" =>  $_POST['idproject'],
				"idamount"  =>  $_POST['idamount'],
				"idpledge"	=>	$idpledge,
				"command"	=>	"preapproval_request",
				"status"	=>	"success",
				"sent"		=>	serialize($preapprovalRequest),
				"received"	=>	(!empty($response)) ? serialize($response) : "",
				"date"		=>	date("Y-m-d H:i:s")
			);
			$this->load->model('Logs_model', 'logs');
			$this->logs->add($logging);
		
			// Redirect to payPal
			header("Location: ".$payPalURL);
		}
	}
	
	
	// Thanks page
	public function thanks(){

		if(empty($_SESSION['user']) OR empty($_SESSION['last_pledge_id'])) {
			redirect("/");
		}
		
		// Template params array
		$vars = array();
		 
		$this->load->model('Projects_model', 'projects');
		$pledge = reset($this->projects->get_project_pledges(array("pl.idpledge" => $_SESSION['last_pledge_id'])));
		$vars['project'] = reset($this->projects->get_projects(array("idproject" => $pledge->idproject)));
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Display my projects template
		$this->view('projects/thanks', $vars);
	}
	
	
	// Error
	public function error(){

		// Template params array
		$vars = array();
		
		// Display my projects template
		$this->view('projects/error', $vars);
	}
	
	
	// IPN notification for PayPal
	public function ipn(){

		// Get preapproval key
		$key = $_REQUEST['preapproval_key'];
		if(empty($key)){
			exit;
		}
		
		// Log sent and returned params
		$logging = array(
			"command"	=>	"preapproval_ipn",
			"status"	=>	"success",
			"sent"		=>	"",
			"received"	=>	(!empty($_REQUEST)) ? serialize($_REQUEST) : "",
			"error"		=>	"",
			"date"		=>	date("Y-m-d H:i:s")
		);
		$this->load->model('Logs_model', 'logs');
		$this->logs->add($logging);
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Load users model
		$this->load->model('Users_model', 'users');
			
		// Update the record in database
		$this->projects->save_pledge(array("status" => "accepted"), array("key" => $key));
		
		// Update project pledget amount
		$this->projects->update_pledged_amount($key);
		
		
		// Get config object
		$ci = get_instance();
		$ci->load->config('emails');
		
		// Get email data
		$this->load->model('Emails_model', 'emails');
		$email_data = (array) reset($this->emails->get_emails(array("idemail" => "5")));

		// Get pledge data
		$pledge = reset($this->projects->get_project_pledges(array("pl.key" => $key)));
		
		// Get project data
		$project = reset($this->projects->get_projects(array("idproject" => $pledge->idproject), array("from" => 0, "count" => 1)));
		
		// Get pledge user data
		$pledge_user = reset($this->users->get_users(array("iduser" => $pledge->iduser), array("from" => 0, "count" => 1)));		

		// Get project owner data
		$owner = reset($this->users->get_users(array("iduser" => $project->iduser), array("from" => 0, "count" => 1)));

		$this->load->model('Configuration_model', 'configuration');
		$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
		$site_title = reset($site_title);

		// Params array
		$params = array(
			"[site_name]"		=>	$site_title,
			"[project_name]"	=>	$project->title,
			"[amount]"			=>	$pledge->amount
		);

		// If the email is active
		if($email_data['active'] > 0){

			// Send success email
			send_mail($ci->config->item('FROM_EMAIL'), $owner->email, $email_data['subject'], $email_data['text'], $params, $params);
		}

		$member_id = $pledge->iduser;
		$object_type = 'project';
		$object_role = 'support';
		$notification_type = @$pledge_user->alerts_backing; //Type of notifications where user is backer of the object
			
		$this->load->model('Notifications_model', 'notifications');
		$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'comment', $notification_type);
		$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'update', $notification_type);
		$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'status_change', $notification_type);
	}
	
	
	// Get location data by string
	public function get_location_data($string){
	
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get location data
		$data = $this->projects->get_location_by_postcode_or_string($string);
		
		// Show response
		echo json_encode($data);
		
		// Terminate
		exit;
	}
	
	
	// Show categories names by ids
	public function get_categories_names_by_ids(){
	
		// Load categories model
		$this->load->model('Categories_model', 'categories');
		
		// Get categories data
		foreach($_POST['ids'] AS $idcategory){
			
			// Get data
			$data = $this->categories->get_category(array("idcategory" => $idcategory));
			$result[] = $data->title;
		}
		
		echo implode(", ", $result);
	}
	
	
	// Add helpers
	public function add_helper(){
	
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		$data = array();
		if(isset($_POST['helper_hours']) && $_POST['helper_hours'])
			$data['helper_hours'] = (float) $_POST['helper_hours'];
			 
		if(isset($_POST['helper_text']) && is_array($_POST['helper_text']) && $_POST['helper_text'])
			$data['helper_text'] = implode(', ', $_POST['helper_text']);
		
		// Update pledge
		$this->projects->save_pledge($data, array("idpledge" => $_SESSION['last_pledge_id']));
		
		// Get pledge details
		$pledge = reset($this->projects->get_project_pledges(array("pl.idpledge" => $_SESSION['last_pledge_id'])));
		
		// Get project by id
		$project = reset($this->projects->get_projects(array("idproject" => $pledge->idproject)));
		
		// Redirect
		redirect("/".$project->slug."/");
		
	}
	
	public function mark_pledge(){
	
		if(!is_logged()) exit('DEBUG: EMPTY MEMBER');
	
		if(!isset($_POST['idpledge'])) exit('DEBUG: EMPTY ID PLEDGE');
		if(!is_numeric($_POST['idpledge'])) exit('DEBUG: INVALID ID PLEDGE');
		
		if(!isset($_POST['value'])) exit('DEBUG: EMPTY VALUE');
		if($_POST['value'] <> 0 && $_POST['value'] <> 1) exit('DEBUG: INVALID VALUE');
		
		if(!isset($_POST['field'])) exit('DEBUG: EMPTY FIELD');
		if($_POST['field'] <> 'thanked' && $_POST['field'] <> 'reward_sent') exit('DEBUG: INVALID FIELD');
		
		$_POST['idpledge'] = (int)$_POST['idpledge'];
		
		$this->load->model('Projects_model', 'projects');
		
		$pledge_data = reset($this->projects->get_project_pledges(array('pl.idpledge' => $_POST['idpledge'], 'p.iduser' => $_SESSION['user']['iduser'])));
		if(!$pledge_data) exit('DEBUG: INVALID PLEDGE');
		
		if($this->projects->save_pledge(array($_POST['field'] => $_POST['value']), array('idpledge' => $_POST['idpledge'])))
			exit('OK');
	}
	
	
	// Export pledges
	public function export_pledges($pledges = ""){
		
		// Downloading headers
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename='Peoplefund_backers_".date("Y-m-d")."'.csv");
		
		// Show headings
		$output = "Username;Date of pledge;Amount;Time / Skill Pledged;Reward type;Thanked;Reward Sent\r\n";
			
		// If no pledges - do nothing
		if(!empty($pledges)){
		
			// Get params
			if(substr_count(urldecode($pledges), ",") > 0){
				$ids = explode(",", urldecode($pledges));
			} else {
				$ids = array("0" => urldecode($pledges));
			}
			
			// Load model
			$this->load->model('Projects_model', 'projects');

			// Get data
			foreach($ids AS $id){

				$help = '';
				
				$pledge_data = reset($this->projects->get_project_pledgers(array("pl.idpledge" => $id, "pl.status" => "accepted")));
				
				$date = new DateTime($pledge_data->date_added); 
				$thanked = ($pledge_data->thanked == "1") ? "Yes" : "No";
				$reward_sent = ($pledge_data->reward_sent == "1") ? "Yes" : "No";
				$username = ($pledge_data->public == 0) ? $pledge_data->username : $pledge_data->username;
				$reward_want = ($pledge_data->reward_want == 1) ? 'UP TO '.$pledge_data->amount : 'No reward';
	
				if($pledge_data->helper_hours > 0) 
					$help .= $pledge_data->helper_hours;
				else 
					$help .= '-';
				
				$help .= ' / ';
				if($pledge_data->helper_text) 
					$help .= 'YES';
				else 
					$help .= '-';
				
				
				$output .= 	$username.";".
							$date->format('d M Y H:s').";".
							$pledge_data->amount.";".
							$help.";".
							$reward_want.";".
							$thanked .";".
							$reward_sent.
							"\r\n";
			}
			
			
		}
		
		echo $output;
	}
}

/* End of file projects.php */ 
/* Location: ./application/controllers/projects.php */