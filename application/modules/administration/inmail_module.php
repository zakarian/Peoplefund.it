<?php 

// Inmail class
class Inmail_Module extends CI_Module {

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
	public function index($temp_sender = "", $sender = "", $temp_receiver = "", $receiver = "", $temp_string = "", $string = "", $temp_page = "", $page = 0) {

		// Load model and try to get messages
		$this->load->model('Inmail_model', 'inmail');
	
		// Load pagination library
		$this->load->library('pagination');
		
		// Where clause array
		$where = array();
		
		// If browsing by sender
		if(!empty($sender)){
			if($sender != "all"){
				$where['s.username'] = $sender;
				$vars['sender'] = $sender;
			}
		} else {
			if(!empty($_POST['sender'])){
				$sender = $this->input->post("sender");
				$where['s.username'] = $this->input->post("sender");
				$vars['sender'] = $sender;
			} else {
				$sender = "all";
			}
		}
		
		// If browsing by receiver
		if(!empty($receiver)){
			if($receiver != "all"){
				$where['r.username'] = $receiver;
				$vars['receiver'] = $receiver;
			}
		} else {
			if(!empty($_POST['receiver'])){
				$receiver = $this->input->post("receiver");
				$where['r.username'] = $this->input->post("receiver");
				$vars['receiver'] = $receiver;
			} else {
				$receiver = "all";
			}
		}
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['title'] = "%".$string."%' OR text LIKE '%".$string."%";
				$vars['string'] = $string;
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['title'] = "%".$string."%' OR text LIKE '%".$string."%";
			$vars['string'] = $string;
		}
		
		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/inmail/index/sender/'.$sender.'/receiver/'.$receiver.'/string/'.$string.'/page/');
		$pager['total_rows'] = $this->inmail->count_messages($where);
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();

		// Get messages
		$vars['items'] = $this->inmail->get_messages($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));

		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		
		// Get messages stats
		$vars['stats'] = $this->inmail->get_messages_stats();

		// Display template
		$this->view('administration/inmail/browse', $vars);
	}
	
	
	// Edit message
	public function edit(){
	
		// Errors array
		$errors = array();
			
		// Get message id
		$idmessage = (int) $this->parameters[0];

		// Load model and try to get user data
		$this->load->model('Inmail_model', 'inmail');
		
		// If trying to save
		if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){

			// Check title
			if(empty($_POST['title'])){
				$errors[] = "Title can't be empty";
			} else if(empty($_POST['text'])){
				$errors[] = "Text can't be empty";
			}
			
			// If we don't have any errors
			if(empty($errors)){
			
				// Save message data
				$data = array(
					"title"		=>	$this->input->post("title"),
					"text"		=>	$this->input->post("text")
				);
				
				// Update user data
				$this->inmail->save_message($data, $idmessage);
				
				// Assign success message and redirect back
				$_SESSION['message'] = "The message was saved";
				redirect("/administration/inmail/");
			}
		}

		// Display template
		$this->view('administration/inmail/form', 
			array(
				"post" 		=> 	(array) reset($this->inmail->get_messages(array("idmessage" => $idmessage))),
				"action"	=>	"edit",
				"errors"	=>	$errors
			)
		);
	}
	
	// Delete message
	public function delete(){
	
		// Get message id
		$idmessage = (int) $this->parameters[0];
		
		// Load messages model
		$this->load->model('Inmail_model', 'Inmail');
		
		// Remove record
		$this->Inmail->deletemessage($idmessage);
		
		// Terminate
		exit("ok");
	}

}