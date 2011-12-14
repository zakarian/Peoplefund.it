<?php 

// Texts class
class Texts_Module extends CI_Module {

	// View dispatcher - adds header, footer and params
	public function view($view, $vars = array()) {
	
		// If loading tinyMCE - load header with MCE js + options
		if(!empty($vars['tinyMCE']))
			$this->load->view('administration/header', array("tinyMCE" => TRUE));
		
		// If not loading tinyMCE - load header
		else 
			$this->load->view('administration/header');
		
		// Load content
		$this->load->view($view, $vars);
		
		// Load footer
		$this->load->view('administration/footer');
	}
	
	// Index action
	public function index() {
		$vars = array();
		$text_url = '';
		$where = '';
		
		$uri = $this->uri->segment_array();
		if(isset($uri[4])) {	
			if(strstr($uri[4], 'url-'))
				$text_url = base64_decode((str_replace('url-', '', $uri[4])));
		}	
			
		if($text_url) 
			$where = ' WHERE t.url = "'.mysql_real_escape_string($text_url).'"';
		
		// Get items
		$vars['items'] = $this->db->query('	
				SELECT t.* 
				FROM `texts` AS t
				'.$where.'
				ORDER BY date DESC
			')->result();
			
		// Get urls
		$vars['urls'] = $this->db->query('	
				SELECT DISTINCT(t.url)
				FROM `texts` AS t
				ORDER BY url DESC
			')->result();
			
		// Load header
		$this->load->view('administration/header');

		// Display template
		$this->load->view('administration/texts/browse', $vars);
		
		// Load footer
		$this->load->view('administration/footer');
	}
	
	// Add - add text
	public function add(){
		// Template vars array and action
		$errors = $vars = array();
		$vars['action'] = 'add';

		// Load model
		$this->load->model('Texts_model', 'texts');
		
		// If trying to add
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			
			// Check key
			if(empty($_POST['key'])) $vars['errors'][] = 'Key can\'t be empty';
			// Check text
			if(empty($_POST['text'])) $vars['errors'][] = 'Text can\'t be empty';
			// Check url
			if(empty($_POST['url'])) $vars['errors'][] = 'URL can\'t be empty';
			
			if(empty($errors)){
				
				// Prepare text data
				$_POST['key'] = h(st($_POST['key']));
				$_POST['text'] = mysql_real_escape_string($_POST['text']);
				$_POST['url'] = h(st($_POST['url']));
				
				// Add text data
				$data = array(
					'key' 	=> $_POST['key'],
					'text' 	=> $_POST['text'],
					'url' 	=> $_POST['url'],
					'host' 	=> $_SERVER['REMOTE_ADDR']
				);
				$this->db->insert('texts', $data); 
				
				// Assign success message and redirect back
				$_SESSION['message'] = 'The text was saved';
				redirect('/administration/texts/');
				
			}
			
		}

		// Display template
		$this->view('administration/texts/form', $vars);
	}
	
	// Edit text
	public function edit(){
	
		// Errors array
		$errors = array();
		
		// Check for and get user id
		if(!isset($this->parameters[1]))
			redirect('/administration/texts/');
			
		$key = $this->parameters[1];

		// Load model and get text data
		$this->load->model('Texts_model', 'texts');
				
		$vars['post'] = $this->db->query('	
				SELECT t.* 
				FROM `texts` AS t
				WHERE id = "'.(int) $key.'"
				ORDER BY date DESC
			')->result();
			
		if(empty($vars['post'][0]))
			redirect('/administration/texts/');
		
		$vars['post'] = $vars['post'][0];
		$vars['action'] = 'edit';

		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			
			if(empty($_POST['text'])) $vars['errors'][] = 'Text can\'t be empty';
			
			if(empty($errors)){
				// Update data
				$this->db->query('UPDATE `texts` SET text = "'.mysql_real_escape_string($_POST['text']).'" WHERE id = '.(int) $key);
				
				// Assign success message and redirect back
				$_SESSION['message'] = 'The text was saved';
				redirect('/administration/texts/');
			}
			
		}

		// Display template
		$this->view('administration/texts/form', $vars);
	}
	
}