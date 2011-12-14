<?php 

// Questions class
class Answers_Module extends CI_Module {

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
	public function index($temp_method = "", $letter = "", $temp_string = "", $string = "", $temp_page = "", $page = "") {
		
		// Load pagination library
		$this->load->library('pagination');
		
		// Load model and try to get all questions
		$this->load->model('Questions_model', 'questions');
		
		if ($this->uri->segment(4) == 'member') {
			$where = array( 'member_id' => $this->uri->segment(5) );
		} else if ($this->uri->segment(4) == 'question') {
			$where = array( 'question_id' => $this->uri->segment(5) );
		} else {
			$where ='';
		}
		
		$limit= array();
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/questions/index/letter/'.$letter.'/string/'.$string.'/page/');
		$pager['total_rows'] = $this->questions->count_answers($where);
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		
		
		if ($this->uri->segment(4) <> 'member' && $this->uri->segment(4) <> 'question') {
			$limit = array("from" => $pager['start_row'], "count" => $pager['per_page']);
			$where = array();
			$this->pagination->initialize($pager);
			$vars['pagination'] = $this->pagination->create_links();
		}
		// Get questions
		$vars['items'] = $this->questions->get_answers_admin($where, $limit);

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/questions/answers', $vars);
	}
		
	// Edit question
	public function edit(){
		$this->load->helper('ip');

		// Errors array
		$errors = array();
			
		// Get question id
		$idanswer = (int) $this->parameters[0];

		// Load questions model
		$this->load->model('Questions_model', 'questions');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
			
			// Assign post vars
			$vars['post'] = $_POST;

			// Check text
			if(empty($_POST['text'])){
				$vars['errors'][] = "Question can't be empty";
			} 
			
			// If we don't have any errors
			if(empty($vars['errors'])){
			
				// Save question data
				$data = array(
					"text"				=>	$this->input->post("text"),
					"status"			=>	$this->input->post("status")
				);
				$this->questions->save_question($data, $this->parameters[0]);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The question was saved";
				redirect("/administration/answers/");
			}
			
		}
		$answer = $this->questions->get_answers(array("id" => $idanswer));
		$question = $this->questions->get_questions(array("q.id" => $answer[0]->question_id));

		// Display template
		$this->view('administration/questions/answers_form', 
			array(
				"question" 		=> 	$question[0],
				"answer" 		=> 	$answer[0],
				"action"		=>	"edit",
				"errors"		=>	$errors,
				"tinyMCE"		=>	TRUE
			)
		);
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
		$this->questions->saveAnswer(array("status" => $status), $this->input->post("id"));
		
		// Terminate
		exit("ok");
	}
	
	// Delete question
	public function delete(){
	
		// Get question id
		$idanswer = (int) $this->parameters[0];
		
		// Load model and try to get data
		$this->load->model('Questions_model', 'questions');
		
		// Remove record
		$this->questions->delete_answer($idanswer);
		
		// Terminate
		exit("ok");
	}
	

}