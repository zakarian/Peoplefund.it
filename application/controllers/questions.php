<?php

// Include abstract controller
require_once(APPPATH.'core/base_controller.php');

/**
* Questions controller
* Handles questions create, add, preview, question preview page etc.
*
* @package PeopleFund
* @category Questions
* @author MTR Design
* @link http://peoplefund.it
*/

// Question controller
class Questions extends Base_Controller {
	
	// Index action
	public function index() {
		$vars = array();
		
		// Load questions model
		$this->load->model('Questions_model', 'questions');	
		
		// Get the categories
		$vars['categories'] = $this->questions->get_categories(array('status' => 'active'));

		// Order type
		$vars['type'] = 'recent';
		
		$uri = $this->uri->segment_array();
		
		// Extract page number
		foreach($uri AS $k => $segment){
			if(strstr($segment, "page")){
				if(!empty($uri[$k + 1])){
					$page = intval($uri[$k + 1]);
				} else {
					$page = 0;
				}
			}
		}
		if(empty($page)) $page = 0;
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		$pager['per_page'] = 60;
		$pager['base_url'] = '/questions/index/page';
		$pager['total_rows'] = $this->questions->count_questions();		
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Add page data to the template
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page']) + 1;
		
		// Get questions	
		$vars['questions'] = $this->questions->get_questions('', array("from" => $pager['start_row'], "count" => $pager['per_page']));
		
		// Display template
		$this->view('questions/browse', $vars);
	}
	
	// Search questions
	public function search() {
	
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			// Check if searching by keyword
			if(!empty($_POST['keyword']) && $_POST['keyword'] != "Bike, village, feild etc" && $_POST['keyword'] != "keyword"){
				$search[] = "keyword:".urlencode($_POST['keyword']);
			}
			
			// If using order
			if ($this->input->post('type') == 'popular') {
				$order_string = 'popular';
			} else {
				$order_string = '';
			}
			
			// If we have search params - redirect to search page
			redirect("/questions/search/".implode("/", $search)."/".$order_string);		
		}
	
		$vars = array();
		$where ='';
		
		// Load questions model
		$this->load->model('Questions_model', 'questions');	
		$vars['categories'] = $this->questions->get_categories(array('status' => 'active'));
		$vars['type'] = 'recent';
		
		// Check search option
		$uri = $this->uri->segment_array();
		$search_array = array();

		// Extract segment and set the search criterias
		foreach($uri AS $segment){
			if($segment == 'popular'){
				$vars['type'] = 'popular';				
			}
			// Search by category
			if(strstr($segment, "category")){
				$category_slug = addslashes(urldecode(str_replace("category:", "", $segment)));
				$search_category =  $this->questions->get_categories(array('slug' => $category_slug));
				$search_array['category'] = $search_category[0]->id;
				$vars['category'] = urldecode(str_replace("category:", "", $segment));
				$vars['search_string'][] = $segment;
			}
			// Search by keyword
			if(strstr($segment, "keyword")){
				$search_array['keyword'] = addslashes(urldecode(str_replace("keyword:", "", $segment)));
				$vars['searchPhrase'] = urldecode(str_replace("keyword:", "", $segment));
				$vars['search_string'][] = $segment;
			}
		}
		
		
		// If we have search criterias
		if(!empty($vars['search_string'])){
			$vars['search_string'] = implode("/", $vars['search_string']);
		}
		
		if (isset($search_array['keyword'])) {
			$where = array('keyword' => $search_array['keyword']);
		}
		if (isset($search_array['category'])) {
			$where = array('q.category_id' => $search_array['category']);
		}
		
		// Extract page number
		foreach($uri AS $k => $segment){
			if(strstr($segment, "page")){
				if(!empty($uri[$k + 1])){
					$page = intval($uri[$k + 1]);
				} else {
					$page = 0;
				}
			}
		}
		if(empty($page)) $page = 0;
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Initialize pager
		$this->load->config('pager');
		$pager = $this->config->config['pager'];
		$pager['per_page'] = 60;
		$pager['total_rows'] = $this->questions->count_questions($where);

		// No params action
		if(empty($vars['search_string'])){
				$pager['base_url'] = site_url('/questions/search/page');
		// If we have filters
		} else {
			$pager['base_url'] = site_url('/questions/search/'.$vars['search_string'].'/page');
		}		
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		
		// Add page data to the template
		$vars['page'] = ($page == "0") ? 1 : ($page / $pager['per_page']) + 1;
		
		// Get questions	
		$vars['questions'] = $this->questions->get_questions($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));
		
		// Display template
		$this->view('questions/browse', $vars);
	}
	
	// Preview, add or edid answer
	public function preview() {
		$vars = array();
		
		// Load questions model
		$this->load->model('Questions_model', 'questions');	
		$question = $this->questions->get_questions(array('q.id' => $this->uri->segments[3]));
		$vars['question'] = $question[0];
		$vars['answers'] = $this->questions->get_answers(array('question_id' => $this->uri->segments[3]));

		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			if($this->input->post('action') == 'newanswer') {
				$this->load->helper('ip');
				if(!isset($_POST['text']) OR (isset($_POST['text']) && !$_POST['text'])) $vars['errors']['text'] = 'Text can\'t be empty';
				$_POST['text'] = h(st($_POST['text']));
				if(empty($vars['errors'])){
					// Insert answer data
					$data = array(
						'text'			=>	$_POST['text'],
						'posted_at'		=>	time(),
						'member_id'		=>	$_SESSION['user']['iduser'],
						'status'		=>	'active',
						'ip'			=>	get_ip(),
						'member_status'	=>	'published',
						'question_id'	=>	$_POST['question_id']
					);
					$this->questions->add_answer($data);
					redirect(current_url());
				} else {
					redirect(current_url());
				}
			
			// Edit answer
			} else if ($this->input->post('action') == 'editanswer') {
				if(!isset($_POST['text']) OR (isset($_POST['text']) && !$_POST['text'])) $vars['errors']['text'] = 'Text can\'t be empty';
				if(empty($vars['errors'])){
					$_POST['text'] = h(st($_POST['text']));
					// Update answer data
					$data = array(
						'text'			=>	$_POST['text'],
					);
					$this->questions->saveAnswer($data, $_POST['answer_id']);
					redirect(current_url());
				} else {
					redirect(current_url());
				}
			}
		} else {	
			// Display template
			$this->view('questions/view', $vars);
		}
	}

	// Add question
	public function add() {
		$this->load->helper('ip');
		$vars = array();
		
		// Check if user logged
		if(empty($_SESSION['user'])) redirect('/');
		
		// Load questions model
		$this->load->model('Questions_model', 'questions');		
		$vars['qa_categories'] = $this->questions->get_categories(array('status' => 'active'));

		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			if(!isset($_POST['text']) OR (isset($_POST['text']) && !$_POST['text'])) $vars['errors']['text'] = 'Text can\'t be empty';
			if(!isset($_POST['category_id']) OR (isset($_POST['category_id']) && !$_POST['category_id'])) $vars['errors']['category_id'] = 'Category can\'t be empty';
			
			$_POST['text'] = h(st($_POST['text']));
			$_POST['category_id'] = h(st($_POST['category_id']));
			
			if(empty($vars['errors'])){
				$data = array(
					'text'			=>	$_POST['text'],
					'category_id'	=>	$_POST['category_id'],
					'posted_at'		=>	time(),
					'member_id'		=>	$_SESSION['user']['iduser'],
					'group_id'		=>	(isset($_POST['group_id']) ? $_POST['group_id'] : 0),
					'status'		=>	'active',
					'ip'			=>	get_ip(),
					'visibility'	=>	'yes',
					'username'	=>	$_SESSION['user']['username'],
					'slug'	=>	$_SESSION['user']['slug']
				);
				$this->questions->add_question($data);
				redirect('/questions');
			} 
			$this->view('questions/add', $vars);
		}
		
		// Display template
		$this->view('questions/add', $vars);
	}
	
	// Edit question
	public function edit() {
		
	}
	
	// Delete answer
	public function delete() {
		// Get question id
		$idanswer = $_POST['answer_id'];
		
		// Load model and try to get data
		$this->load->model('Questions_model', 'questions');
		
		// Remove record
		$this->questions->delete_answer($idanswer);
		
		// Terminate
		exit("success");
	}

	// Preview question
	public function helpful() {

		// Check if user logged
		if(!isset($_SESSION['user']['iduser']))
			exit('fail');
		// Get answer id
		if(!isset($_POST['answer_id']))
			exit('fail2');
			
		// Load questions model
		$this->load->model('Questions_model', 'questions');		

		// Check if user allready voted
		$vote = $this->questions->get_vote($_POST['answer_id'], $_SESSION['user']['iduser']);
		if(!empty($vote))
			exit('already_voted');

		// Set helpful flag
		if(isset($_POST['helpful']) && $_POST['helpful'] == 'no') {}
		else $_POST['helpful'] = 'yes';

		// Add question helpful data
		if( $this->questions->add_helpful($_POST['answer_id'], $_SESSION['user']['iduser'], $_POST['helpful'])) {
			// Update questions data
			if($this->questions->update_helpful_answer($_POST['helpful'], (int) $_POST['answer_id'])) {
				// Return the JS action
				if($_POST['helpful'] == 'yes')
					print 'mass-increment';
				else
					print 'single-increment';
			}
		} else {
			// Terminate
			exit('fail');
		}
	}
	
	// Show questions in some category
	public function category() {
		$vars = array();
		
		// Load questions model
		$this->load->model('Questions_model', 'questions');	
		
		// Get the questions in the selected category
		$vars['questions'] = $this->questions->get_questions(array('c.slug' => $this->uri->segments[3]));
		
		// Display template
		$this->view('questions/browse', $vars);
	}
	
}

/* End of file questions.php */ 
/* Location: ./application/controllers/questions.php */