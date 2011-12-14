<?php

// Include abstract controller
require_once(APPPATH.'core/base_controller.php');

/**
* Router controller
*
* @package PeopleFund
* @category Router
* @author MTR Design
* @link http://peoplefund.it
*/

// Router controller
class Router extends Base_Controller {

	// Start router
	public function start(){
		
		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get current URL parts
		$slug = reset($this->uri->segment_array());
		
		// Check if we have project with this slug
		$project = $this->projects->get_one_projects(NULL, $slug);
		
		// Check for saved or non approved project if we did't find any open project
		if(is_logged() && !$project)
			$project = $this->projects->get_one_projects(NULL, $slug, $_SESSION['user']['iduser']);

		// We find the project
		if(isset($project) && $project){

			$vars['current_page'] = 'projects';
			$vars['project'] = reset($project);
			$vars['page_title'] = $vars['project']->title.' - Projects';
			$vars['fb_title'] = $vars['project']->title;
			$vars['fb_description']	= $vars['project']->outcome;
			
			// Get current URL parts
			$url_parts = $this->uri->segment_array();
			
			### PROJECT COMMENTS ###
			if(isset($url_parts[2]) && ($url_parts[2] == 'comments') ){
				if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			
					if(!isset($_POST['from']) || !isset($_POST['count'])) exit('DEBUG: EMPTY FROM/COUNT');
					if(!is_numeric($_POST['from']) || !is_numeric($_POST['count'])) exit('DEBUG: INVALID FROM/COUNT');
						
					$_POST['from'] = (int)$_POST['from'];
					$_POST['count'] = (int)$_POST['count'];
					
					$vars['comments'] = $this->projects->get_all_comments($_POST['idproject'], array('from' => $_POST['from'], 'count' => $_POST['count']));
					
					// Display template
					$this->load->view('projects/comments_items', $vars);
				} else {
					// Get all project comments, amounts and pledges
					$vars['comments'] = $this->projects->get_all_comments($vars['project']->idproject, array('from' => 0, 'count' => 5));
					$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $vars['project']->idproject));
					$vars['pledges'] = $this->projects->get_project_pledgers(array('pl.idproject' => $vars['project']->idproject, 'pl.status' => 'accepted'));
					
					// Count helpers and backers
					foreach($vars['pledges'] AS $row){
						if($row->helper_hours > 0 || $row->helper_text)
							$vars['helpers'][] = $row;
						$vars['backers'][] = $row;
					}

					$vars['btn'] = 'comments';
					
					if(!empty($_SESSION['success'])){
						$vars['success'] = $_SESSION['success'];
						unset($_SESSION['success']);
					} else if(!empty($_SESSION['error'])){
						$vars['error'] = $_SESSION['error'];
						$vars['post'] = $_SESSION['post'];
						unset($_SESSION['error']);
					}

					// Display template
					$this->view('projects/comments', $vars);
					
				}

			### PROJECT HELPERS/BACKERS ###
			} else if(isset($url_parts[2]) && ($url_parts[2] == 'backers' || $url_parts[2] == 'helpers')){
				
				$vars['pledges'] = $this->projects->get_project_pledgers(array('pl.idproject' => $vars['project']->idproject, 'pl.status' => 'accepted'));
				
				foreach($vars['pledges'] AS $row){
					if($row->helper_hours > 0 || $row->helper_text)
						$vars['helpers'][] = $row;
					$vars['backers'][] = $row;
				}
				
				$vars['subpage'] = $url_parts[2];
				
				// Display template
				if(is_logged() && $_SESSION['user']['iduser'] == $vars['project']->iduser)
					$this->view('projects/pledgers_admin', $vars);
				else 
					$this->view('projects/pledgers', $vars);
			
			### PROJECT EDIT ###
			} else if(!empty($url_parts[2]) AND $url_parts[2] == 'edit'){
			
				if(!is_logged()) redirect('/user/login/');
				if($_SESSION['user']['iduser'] <> $vars['project']->iduser) redirect('/');

				$this->load->model('Users_model', 'users');
				$this->load->model('Categories_model', 'categories');
				
				$project = (array)$vars['project'];
				$vars['categories'] = $this->categories->get_categories(TRUE);
				
				$vars['action'] = 'edit';
				$vars['selected_categories'] = array();
				foreach($project['categories'] AS $k => $v){
					$vars['selected_categories'][$v->idcategory] = TRUE;
				}

				// If trying to edit the project
				if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
				
					$_POST['slug'] = slugify($_POST['slug']);
					
					if(!isset($_POST['title']) || (isset($_POST['title']) && !$_POST['title'])) $vars['errors'][] = 'Title can\'t be empty';
					if(!isset($_POST['categories']) || (isset($_POST['categories']) && count($_POST['categories']) == 0)) $vars['errors'][] = 'You should select at least 1 category';
					if(!isset($_POST['websites']) || (isset($_POST['websites']) && !$_POST['websites'])) $_POST['websites'] = array();
					// if(!isset($_POST['paypal_email']) || (isset($_POST['paypal_email']) && !check_email($_POST['paypal_email']))) $vars['errors'][] = 'Please enter valid PayPal email address';
					if(!isset($_POST['merchant_id']) || empty($_POST['merchant_id'])) $vars['errors']['merchant_id'] = "Please associate your project with GoCardless account";
					if(!isset($_POST['slug']) || (isset($_POST['slug']) && !$_POST['slug'])) $vars['errors'][] = 'Please enter project vanity url';
					
					$slug_check = $this->projects->get_projects(array("p.slug" => $_POST['slug']));
					if($slug_check){
						$slug_check = reset($slug_check);
						if($slug_check->idproject != $vars['project']->idproject) $vars['errors'][] = 'This slug is already taken, please enter another one';
					}
					
					if(!isset($_POST['outcome']) || (isset($_POST['outcome']) && !$_POST['outcome'])) $vars['errors'][] = 'Please enter outcome';
					if(!isset($_POST['about']) || (isset($_POST['about']) && !$_POST['about'])) $vars['errors'][] = 'Please enter project information by fillint the about field';	
					
					if($slug_check->status == 'temp') {
						if(!isset($_POST['period']) || (isset($_POST['period']) && !is_numeric($_POST['period']))) $vars['errors']['period'] = 'Please enter valid period';
						if(
							!isset($_POST['amount']) OR 
							(isset($_POST['amount']) && !is_numeric($_POST['amount'])) OR 
							(isset($_POST['amount']) && ((int) $_POST['amount'] < 1000 OR (int) $_POST['amount'] > 50000))
						) $vars['errors']['amount'] = 'Please enter funding target between &pound;1000 and &pound;50,000';
					} else {
						$_POST['period'] = $slug_check->period/7;
						$_POST['amount'] = $slug_check->amount;
					}
					
					
					if(!isset($_POST['postcode']) || (isset($_POST['postcode']) && !$_POST['postcode'])) $vars['errors'][] = 'Please enter postcode';
					else {
						$location_data = $this->users->get_location_by_postcode($_POST['postcode']);
						if(empty($location_data)) $vars['errors'][] = 'Invalid postcode';
					}
					
					// Check if the amounts exists
					$cnt1 = (!empty($_POST['amounts'])) ? count($_POST['amounts']) : 0;
					$cnt2 = (!empty($_POST['amounts_add'])) ? count($_POST['amounts_add']) : 0;
					if($cnt1 + $cnt2 == 0){
						$vars['errors']['amounts_number'] = "You need to add at least 1 reward";
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
							} else if(!is_numeric($_POST['amounts'][$k]) || ((int) $_POST['amounts'][$k] < 0 || (int) $_POST['amounts'][$k] > 50000)){
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
					
					// Check new amounts for wrong values
					if(!empty($_POST['amounts_add'])){
						foreach($_POST['amounts_add'] AS $k => $amount){
					
							// Check descriptions
							if(empty($_POST['amounts_descriptions_add'][$k])){
								$vars['errors']["empty_description"] = "One of the rewards has empty description";
							}
							
							// Check amounts
							if(empty($_POST['amounts_add'][$k])){
								$vars['errors']["empty_amount"] = "One of the rewards has empty amount";
							} else if(!is_numeric($_POST['amounts_add'][$k])){
								$vars['errors']["empty_amount"] = "One of the rewards has wrong amount";
							}
							
							// Check limits and numbers
							if($_POST['amounts_limited_add'][$k] == "yes"){
								if(empty($_POST['amounts_numbers_add'][$k])){
									$vars['errors']['empty_numbers'] = "One of the rewards is limited and has empty number field";
								}
							}
						}
					}
					
					if(isset($_POST['time']) && $_POST['time']) {
						if(strlen($_POST['time']) > 4)
							$vars['errors']['time'] = 'Time field you entered has characters that are not allowed – you can only enter numbers';
						else if(!is_numeric($_POST['time']))
							$vars['errors']['time'] = 'Time field you entered has characters that are not allowed – you can only enter numbers';
					} else $_POST['time'] = 0;
					
					if(isset($_POST['skills']) && is_array($_POST['skills']) && count($_POST['skills'])) {
						foreach($_POST['skills'] as $k=>$skill)
							if(strlen($skill) > 30)
								$vars['errors']['skills'] = 'The skills you entered contains over 30 characters';
							else
								$_POST['skills'][$k] = h(st($_POST['skills'][$k]));
					} else $_POST['skills'] = array();

					$_POST['title'] = h(st($_POST['title']));
					$_POST['postcode'] = h(st($_POST['postcode']));
					$_POST['slug'] = h(st($_POST['slug']));
					$_POST['amount'] = h(st($_POST['amount']));
					$_POST['outcome'] = h(st($_POST['outcome']));
					//$_POST['paypal_email'] = h(st(@$_POST['paypal_email']));
					$_POST['time'] = (int)$_POST['time'];
					$_POST['skills'] = serialize($_POST['skills']);
					
					// Check for attached video
					if(!$vars['project']->vzaar_idvideo && !$vars['project']->embed)
						$vars['errors']['no_video'] = 'Please link to or upload a short video pitching your project.'; 
					
					$vars['post'] = $_POST;

					if(empty($vars['errors'])){
						
						$data = array(
							"title"			=>	$_POST['title'],
							"time"			=>	$_POST['time'],
							"skills"		=>	$_POST['skills'],
							"slug"			=>	$_POST['slug'],
							'websites'		=>	implode('|', $_POST['websites']),
							"outcome"		=>	$_POST['outcome'],
							"about"			=>	$_POST['about'],
							"paypal_email"	=>	$_POST['paypal_email'],
							"merchant_id"	=>	(int) @$_POST['merchant_id'],
							"amount"		=>	(float)$_POST['amount'],
							"pledge_more"	=>	$_POST['pledge_more'],
							"helpers"		=>	$_POST['helpers'],
							"period"		=>	intval($_POST['period']) * 7,
							"postcode"		=>	$_POST['postcode'],
							"date_modified"	=>	date("Y-m-d H:i:s")
						);
						
						
						if(isset($_POST['access_token']) && !empty($_POST['access_token'])) $data['access_token'] = $_POST['access_token'];
						if(isset($_POST['submit']) && !empty($_POST['submit'])) { 
							$data['status'] = 'open';

							if(!$vars['project']->approved && $vars['project']->status != 'open') {
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
						}
						
						$data['town_name'] = $location_data['town_name'];
						$data['county_name'] = $location_data['county_name'];
						$data['location_preview'] = $location_data['location_preview'];
						$data['lat'] = $location_data['lat'];
						$data['lng'] = $location_data['lng'];
						
						// If saving new video
						if(!empty($_SESSION['vzaar_idvideo'])){
							$data['vzaar_idvideo'] = $_SESSION['vzaar_idvideo'];
							$data['vzaar_processed'] = "0";
							unset($_SESSION['vzaar_idvideo']);
						}
					
						// Save project data
						$this->projects->save_project($data, $project['idproject']);
						
						// Save project categories
						$this->projects->save_project_categories($project['idproject'], $_POST['categories']);
						
						// Remove removed amounts
						if(!empty($_POST['remove_amount'] )){
							foreach($_POST['remove_amount'] AS $idamount){
							
								$count = $this->projects->count_project_pledges(array("idamount" => $idamount));
								$count = reset($count[0]);
								if($count == 0){
									$this->projects->delete_project_amount($idamount);
								}
							}
						}
						
						// Save project amounts
						if(isset($_POST['amounts']) && !empty($_POST['amounts'])){
							foreach($_POST['amounts'] as $idamount => $amount){
								$data = array();
								$data['amount'] = $amount;
								$data['description'] = h(st($_POST['amounts_descriptions'][$idamount]));
								$data['limited'] = $_POST['amounts_limited'][$idamount];
								$data['number'] = $_POST['amounts_numbers'][$idamount];
								$this->projects->save_project_amount($data, $idamount);
								$data['idamount'] = $idamount;
							}
						}
						
						if(isset($_POST['amounts_add']) && !empty($_POST['amounts_add'])){
							foreach($_POST['amounts_add'] as $k => $amount){
								$data = array();
								$data['amount'] = $amount;
								$data['text'] = h(st($_POST['amounts_descriptions_add'][$k]));
								$data['limited'] = $_POST['amounts_limited_add'][$k];
								$data['number'] = $_POST['amounts_numbers_add'][$k];
								$this->projects->add_project_amount($vars['project']->idproject, $data);
							}
						}
						
						$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $vars['project']->idproject));
						
						foreach($vars['amounts'] as &$amount){
							$count = $this->projects->count_project_pledges(array("idamount" => $amount->idamount));
							$count = reset($count[0]);
							if($count > 0){
								$amount->used = TRUE;
							} else {
								$amount->used = FALSE;
							}
						}
						
						redirect($_POST['slug']);
					} else {
						$project_data = reset($this->projects->get_projects(array("p.idproject" => $vars['project']->idproject)));
						$project = array(
							"title"	=>	(isset($_POST['title']) && $_POST['title']) ? $_POST['title'] : '',
							"websites"	=>	implode("|", $_POST['websites']),
							"county_name"	=>	$location_data['county_name'],
							"location_preview" 	=> $location_data['location_preview'],
							"postcode"	=>	(isset($_POST['postcode']) && $_POST['postcode']) ? $_POST['postcode'] : '',
							"slug"	=>	(isset($_POST['slug']) && $_POST['slug']) ? $_POST['slug'] : '',
							// "paypal_email"	=>	(isset($_POST['paypal_email']) && $_POST['paypal_email']) ? $_POST['paypal_email'] : '',
							"merchant_id"	=>	(isset($_POST['merchant_id']) && $_POST['merchant_id']) ? (int) $_POST['merchant_id'] : 0,
							"access_token"	=>	(isset($_POST['access_token']) && $_POST['access_token']) ? $_POST['access_token'] : '',
							"categories"	=>	(isset($_POST['categories']) && $_POST['categories']) ? $_POST['categories'] : array(),
							"outcome"	=>	(isset($_POST['outcome']) && $_POST['outcome']) ? $_POST['outcome'] : '',
							"about"	=>	(isset($_POST['outcome']) && $_POST['outcome']) ? $_POST['about'] : '',
							"vzaar_idvideo"	=>	$project_data->vzaar_idvideo,
							"vzaar_processed"	=>	$project_data->vzaar_processed,
							"ext"	=>	$project_data->ext,
							"idproject"	=>	$vars['project']->idproject,
							"period"	=>	(isset($_POST['period']) && $_POST['period']) ? $_POST['period'] : '',
							"amount"	=>	(isset($_POST['amount']) && $_POST['amount']) ? $_POST['amount'] : '',
							"pledge_more"	=>	(isset($_POST['pledge_more']) && $_POST['pledge_more']) ? $_POST['pledge_more'] : '',
							"helpers"	=>	(isset($_POST['helpers']) && $_POST['helpers']) ? $_POST['helpers'] : ''
						);
						$vars['project'] = (object) $project;
						
						// Form the amounts array
						if(!empty($_POST['amounts'])){
							foreach($_POST['amounts'] AS $k => $amount){
								$amount_row = array(
									"description" => $_POST['amounts_descriptions'][$k],
									"limited" => $_POST['amounts_limited'][$k],
									"number" => $_POST['amounts_numbers'][$k],
									"amount" => $amount,
									"idamount" => $k,
								);
								$vars['amounts'][] = (object) $amount_row;
							}
						}
						
						// Add new amounts
						if(!empty($_POST['amounts_add'])){

							foreach($_POST['amounts_add'] AS $k => $amount){
								$description = $_POST['amounts_descriptions_add'][$k];
								$data = array();
								$data['amount'] = $amount;
								$data['description'] = $description;
								$data['limited'] = $_POST['amounts_limited_add'][$k];
								$data['number'] = $_POST['amounts_numbers_add'][$k];
								$vars['amounts'][] = (object) $data;
							}
						}
						
						if(isset($vars['amounts']) && $vars['amounts']) {
							foreach($vars['amounts'] AS &$amount){
								if(empty($amount->idamount)){
									$amount->used = FALSE;
									continue;
								}
								
								$count = $this->projects->count_project_pledges(array("idamount" => $amount->idamount));
								$count = reset($count[0]);
								if($count > 0){
									$amount->used = TRUE;
								} else {
									$amount->used = FALSE;
								}
							}
						}
					
					}
					
				// If not posting information
				} else {

					// Get project amounts
					$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $project['idproject']));
					
					// Check if amounts are used
					if(!empty($vars['amounts'])){
						foreach($vars['amounts'] AS &$amount){
							$count = $this->projects->count_project_pledges(array("idamount" => $amount->idamount));
							$count = reset($count[0]);
							if($count > 0){
								$amount->used = TRUE;
							} else {
								$amount->used = FALSE;
							}
						}
					}

					// Assign project data to template
					$vars['post'] = $project;
					$vars['post']['period'] = $vars['post']['period'] / 7;
				}

				// Load vzaar library
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
				
				$vars['project'] = reset($this->projects->get_projects(array("p.slug" => $slug)));
				
				// Display template
				$this->view('projects/edit', $vars);
				
	
			### PROJECT - REMOVE VIDEO ###
			} else if(!empty($url_parts[2]) AND $url_parts[2] == "removeVideo"){
			
				// Initialize vzaar SWFuploader
				require_once(APPPATH.'third_party/vzaar/Vzaar.php');
				Vzaar::$token = $this->config->config['vzaar_token'];
				Vzaar::$secret = $this->config->config['vzaar_secret'];
				
				// Get video id
				$idvideo = $vars['project']->vzaar_idvideo;

				// Remove from Vzaar.com database
				Vzaar::deleteVideo($idvideo);
				
				// Update project database
				$this->projects->save_project(array("vzaar_idvideo" => "0", "vzaar_processed" => "0"), $vars['project']->idproject);
				
				// Go back
				redirect("/".$vars['project']->slug."/edit/");

			### PROJECT - CLOSE ###
			} else if(!empty($url_parts[2]) AND $url_parts[2] == "close"){
				
				// Get slug
				$slug = $url_parts[1];
			
				// If the user is not logged in - redirect to index page
				if(empty($_SESSION['user'])){
					redirect("/");
				}
				
				// If the slug is empty
				if(empty($slug)){
					redirect("/");
				}
				
				// Load projects model
				$this->load->model('Projects_model', 'projects');
				
				// Try to get project data by slug
				$project = $this->projects->get_projects(array("p.slug" => $slug));
				if(empty($project)){
					redirect("/");
				} else {
					$project = (array) reset($project);
				}
				
				// If the project is closed - user can't edit it
				if($project['status'] == "closed"){
					redirect("/");
				}
				
				// Update project - set status to "closed"
				$this->projects->save_project(array("status" => "closed"), $project['idproject']);
				
				// Success message
				$_SESSION['success'] = "Your project was closed";
				
				// Redirect back to index page
				redirect("/");
			
			### PROJECT UPDATES ###
			} else if(!empty($url_parts[2]) AND $url_parts[2] == "updates"){
				
				// Check if the user can add updates
				if($vars['project']->iduser != $_SESSION['user']['iduser']){
					redirect("/");
				}
					
				// If addint new update
				if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
				
					// Check data
					if(empty($_POST['text'])){
						$error = "The text can't be empty";
					}
					
					// If we have errors
					if(!empty($error)){
						$vars['error'] = $error;
						$vars['post'] = $_POST;
						
					// If the data is correct
					} else {
					
						// Insert array
						$data = array(
							"idproject"		=>	$vars['project']->idproject,
							"title"			=>	"",
							"text"			=>	$_POST['text'],
							"date_added"	=>	date("Y-m-d H:i:s")
						);
						
						// Add update
						$this->projects->add_update($data);
						
						// Assign success message
						$vars['success'] = "Your update was added";
						
						// Display project template
						redirect("/".$slug."/comments/");
					}
				}
				
				// Get project comments
				$vars['comments'] = $this->projects->get_all_comments($vars['project']->idproject, array("from" => 0, "count" => 5));
				
				// Get related projects
				

				// Get project amounts
				$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $vars['project']->idproject));

				// Get project pledgers
				$vars['pledges'] = $this->projects->get_project_pledgers(array("pl.idproject" => $vars['project']->idproject, "pl.status" => "accepted"));

				// Separate pledgers from backers
				foreach($vars['pledges'] AS $row){
					if($row->helper_hours > 0 || $row->helper_text)
						$vars['helpers'][] = $row;
					$vars['backers'][] = $row;
				}

				$vars['btn'] = 'updates';
				
				// If we have success
				if(!empty($_SESSION['success'])){
					$vars['success'] = $_SESSION['success'];
					unset($_SESSION['success']);
					
				// If we have some errors
				} else if(!empty($_SESSION['error'])){
					$vars['error'] = $_SESSION['error'];
					$vars['post'] = $_SESSION['post'];
					unset($_SESSION['error']);
				}

				// Display my projects template
				$this->view('projects/updates', $vars);				

				
			### PROJECT UPDATES - EDIT ###
			} else if(!empty($url_parts[2]) AND $url_parts[2] == "edit_update"){
				
				// Check if the user can edit updates
				if($vars['project']->iduser != $_SESSION['user']['iduser']){
					redirect("/");
				}
				
				// Get update
				$idupdate = $url_parts[3];
				
				// If addint new update
				if(strtolower($_SERVER['REQUEST_METHOD']) == "post"){
					
					// Check data
					if(empty($_POST['title'])){
						$error = "Title can't be empty";
					} else if(empty($_POST['text'])){
						$error = "Title text be empty";
					}
					
					// If we have errors
					if(!empty($error)){
						$vars['error'] = $error;
						$vars['post'] = $_POST;
						
					// If the data is correct
					} else {
					
						// Insert array
						$data = array(
							"title"			=>	$this->input->post("title"),
							"text"			=>	$this->input->post("text")
						);
						
						// Add update
						$this->projects->save_update($data, $idupdate);
						
						// Assign success message
						$vars['success'] = "Your update was saved";
					}
				}
				
				// Get update data
				$data = $this->projects->get_updates(array("idupdate" => $idupdate));
				$vars['post'] = (array) reset($data);
				
				// Display project template
				$this->view('projects/updates_edit', $vars);
			} elseif(!empty($url_parts[2]) AND ($url_parts[2] == "widget") ){
			### VIEW PROJECT WIDGET POPUP ###
				if(uri_string() && preg_match('/ajax/', uri_string())) {
					$vars['ajax'] = TRUE;
				}

				$vars['webroot'] = $this->config->config['base_url'];

				$this->view('projects/widget', $vars);
			} elseif(!empty($url_parts[2]) AND ($url_parts[2] == "widget.js") ){
			### VIEW PROJECT WIDGET JS ###
				$vars['ajax'] = TRUE;
				$vars['webroot'] = $this->config->config['base_url'];

				$hashtag = "project-" . $vars['project']->idproject;
				$cachefile = SITE_DIR . "application/cache/" . $hashtag ."-js.cache";
				$cachetime = 5 * 60; // where 1 is how many minutes you want to cache

				//if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {
					//print file_get_contents($cachefile);

					//exit;
				//} else {
					$js = $this->load->view('templates/widget_js', $vars, TRUE);

					$fp = fopen($cachefile, 'w');
					fwrite($fp, $js);
					fclose($fp);
				//}

				print $js;

				exit;
				
				
			### NOT FOUND SUBURI ###
			} elseif(!empty($url_parts[2])){
				redirect('/404/');

			
			### VIEW PROJECT ###
			} else {

				// Check if the video is not processed
				if($vars['project']->vzaar_processed == "0" /*AND $url_parts[2] != "converting"*/){
				
					// Initialize vzaar SWFuploader
					require_once(APPPATH.'third_party/vzaar/Vzaar.php');
					Vzaar::$token = $this->config->config['vzaar_token'];
					Vzaar::$secret = $this->config->config['vzaar_secret'];
					Vzaar::$enableFlashSupport = TRUE;
						
					$path_dir = SITE_DIR."public/uploads/projects/";

					$video = Vzaar::getVideoDetails($vars['project']->vzaar_idvideo, TRUE);
					
					if(@$video->videoStatusDescription == "Active"){
						$image_resource = file_get_contents($video->framegrabUrl);
						file_put_contents($path_dir . $vars['project']->idproject.".jpg", $image_resource);
						chmod($path_dir . $vars['project']->idproject.".jpg", 0777);
							
						// Make thumbs
						make_project_thumb($path_dir . $vars['project']->idproject.".jpg");
							
						$this->projects->save_project(array("vzaar_processed" => "1", "ext" => "jpg"), $vars['project']->idproject);
					} else {
						
					}
				}

				// Try to get project data by slug
				$project = $this->projects->get_projects(array("p.slug" => $slug));
				$vars['project'] = reset($project);
				
				

				// Get related projects
				

				// Get project amounts
				$vars['amounts'] = $this->projects->get_project_amounts(array("idproject" => $vars['project']->idproject));
				
				// Check if the amount has limit
				/*foreach($vars['amounts'] AS $k => $amount){
					if($amount->limited == "yes"){
						$number = $amount->number;
						$number_now = reset($this->projects->count_project_pledges(array("idamount" => $amount->idamount, "pl.status" => "accepted")));
						if(reset($number_now) >= $number){
							unset($vars['amounts'][$k]);
						}
					}
				}*/

				// Get project pledgers
				$vars['pledges'] = $this->projects->get_project_pledgers(array("pl.idproject" => $vars['project']->idproject, "pl.status" => "accepted"));
				
				// Separate pledgers from backers
				foreach($vars['pledges'] AS $row){
					if($row->helper_hours > 0 || $row->helper_text)
						$vars['helpers'][] = $row;
					$vars['backers'][] = $row;
				}

				// Load notifications model
				$this->load->model('Notifications_model', 'notifications');
				$member_id = (int)@$_SESSION['user']['iduser'];
				$object_id = $vars['project']->idproject;
				$object_role = 'watch';
				$object_type = 'project';
				$vars['watching_status'] = $this->notifications->get_status($member_id, $object_id, $object_role, $object_type);

				// Display project template
				$this->view('projects/view', $vars);

			}

		### SECTION + PAGE ###
		} else if(count($this->uri->segment_array()) == 2){
			
			// Get slug
			$slug = end($this->uri->segment_array());

			// Load pages model
			$this->load->model('Pages_model', 'pages');
			
			// Try to get page data
			$page = $this->pages->get_pages(array("slug" => $slug));
			
			// If page was not found
			if(empty($page)){
				
				// The page was not found
				redirect("/404/");
				
			// If the page was found	
			} else {
				$vars['current_page'] = $page[0]->slug;
				$vars['page_title'] = $page[0]->meta_title;
				$vars['page_keywords'] = $page[0]->keywords;
				$vars['page_description'] = $page[0]->description;

				// Reset pages array
				$vars['page'] = reset($page);
			}
			
			// If the page is not active
			if($vars['page']->active == 0){
				redirect("/404");
			}
			
			// Get section
			$section = $this->pages->get_sections(array("slug" => reset($this->uri->segment_array())));
			$section = reset($section);
			
			// If the page is not active
			if($section->active == 0){
				redirect("/404");
			}	
			
			// Display page template
			$this->view('pages/view', $vars);
		
		### 404 - PAGE NOT FOUND ###
		} else if($slug == "404"){
		
			// Load pages model
			$this->load->model('Pages_model', 'pages');
			
			// Try to get page data
			$page = $this->pages->get_pages(array("slug" => $slug));
			
			$vars['page_title'] = 'People Fund - '.$page[0]->meta_title;
			$vars['page_keywords'] = $page[0]->keywords;
			$vars['page_description'] = $page[0]->description;
				
			// Reset page vars
			$vars['page'] = reset($page);
			
			// Display page template
			$this->view('pages/view', $vars);
		
		### SECTION ###
		} else {

			// Load pages model
			$this->load->model('Pages_model', 'pages');
			
			// Try to get page data
			$page = $this->pages->get_pages(array("slug" => $slug));

			// If page was not found
			if(empty($page)){
				
				// The page was not found
				redirect("/404/");
				
			// If the page was found	
			} else {
			
				// Reset pages array
				$vars['page'] = reset($page);
				
				// If the page has section
				if($vars['page']->idsection != 0){
					
					// Get section
					$section = $this->pages->get_sections(array("idsection" => $vars['page']->idsection));
					$section = reset($section);
					
					// If the section is not active
					if($section->active == 0){
						redirect("/404/");
					}

					// Redirect to fixed url
					redirect("/".$section->slug."/".$slug);
				}
				
				// If the page is not active
				if($vars['page']->active == 0){
					redirect("/404/");
				}
			}

			// Display page template
			if($vars['page']->slug){
				$vars['current_page'] = $vars['page']->slug;
			}

			$vars['page_title'] = 'People Fund - '.$vars['page']->meta_title;
			$vars['page_keywords'] = $vars['page']->keywords;
			$vars['page_description'] = $vars['page']->description;

			$this->view('pages/view', $vars);
			
		}	
	}
}