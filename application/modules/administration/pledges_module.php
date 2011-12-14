<?php 

// Pledges class
class Pledges_Module extends CI_Module {

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
	public function index($temp_string = "", $string = "", $temp_status = "", $status = "", $temp_page = "", $page = "") {
	
		// Load pagination library
		$this->load->library('pagination');
		
		// Template params array
		$vars = array();
		
		// Create where clause array
		$where = array();
	
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// If browsing by string
		if(empty($string)){
			$string = $this->input->post('string');
			if(!empty($string)){
				$string = $this->input->post('string');
				$where['p.title'] = "%".urldecode($string)."%";
			} else {
				$string = "all";
			}
		} elseif($string != "all") {
			$where['p.title'] = "%".urldecode($string)."%";
		}
		
		// Assign the string
		if($string != "all"){
			$vars['string'] = $string;
		}
		
		// If browsing by status
		if(empty($status)){
			$status = $this->input->post('status');
			if(!empty($status)){
				$status = $this->input->post('status');
				$where['pl.status'] = $status;
			} else {
				$status = "all";
			}
		} elseif($status != "all") {
			$where['pl.status'] = $status;
		}

		// Assign the status
		if($status != "all"){
			$vars['status'] = $status;
		}

		// Initialize pager
		$this->load->config('administration_pager');
		$pager = $this->config->config['administration_pager'];
		$pager['base_url'] = site_url('administration/pledges/index/string/'.$string.'/status/'.$status.'/page/');
		$pager['total_rows'] = reset(reset($this->projects->count_project_pledges($where)));
		$pager['per_page'] = '20';
		$pager['cur_page'] = $page;
		$pager['start_row'] = $pager['per_page'] * ($pager['cur_page'] / $pager['per_page']);
		$this->pagination->initialize($pager);
		$vars['pagination'] = $this->pagination->create_links();
		
		// Get all pledges
		$vars['items'] = $this->projects->get_project_pledges($where, array("from" => $pager['start_row'], "count" => $pager['per_page']));

		// Get pledges stats
		$vars['stats'] = $this->projects->get_pledges_stats();
		
		// Display template
		$this->view('administration/pledges/browse', $vars);
	}

}