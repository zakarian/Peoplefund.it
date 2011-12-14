<?php   
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* GoCardless Controller 
* Handles pledging and payment with GoCardless online payment system.
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Processor extends Base_Controller {

	/**
	* Not used. Redirect to 404 not found page.
	* 
	* @access public
	*/
	public function index(){
		redirect('/404/');
	}
	
	/**
	* Send PreAuthorization request to the GoCardless server.
	* When pledge is confirmed by the user send the pledge information to the GoCardless server, 
	* so it will check if the user have the pledged amount in his bank account and will  
	* remember that the user has confirmed this amount to be taken from his account if the 
	* project is successful.
	* 
	* @access public
	*/
	public function send_preauthorization() 
	{
		// Casting / initializing the post vars taken from pledge confirmation page, containing pledge amount info
		$_POST['amount'] = empty($_POST['amount']) ? -1 : (float)$_POST['amount'];
		$_POST['idproject'] = empty($_POST['idproject']) ? -1 : (int)$_POST['idproject'];
		$_POST['idamount'] = empty($_POST['idamount']) ? -1 : (int)$_POST['idamount'];

		// Load projects model
		$this->load->model('Projects_model', 'projects');

		// Check is this project valid
		$project = reset($this->projects->get_one_projects($_POST['idproject']));
		if ( ! $project)
			redirect("/404/");
		
		// Get the amount and if it's valid
		$amount = reset($this->projects->get_project_amounts(array('idproject' => $_POST['idproject'], 'idamount' => $_POST['idamount'])));
		if ( ! $amount)
			redirect("/404/");

		if($amount->limited == 'yes' && $amount->remaining <= 0) redirect('/'.$project->slug.'/?limited');

		// If the user has pledged more money that the usual amount for the choosen reward set the 'new amount' he has pledged
		if (isset($_POST['new_amount']) && is_numeric($_POST['new_amount']))
		{
			// Cast the 'new amount'
			$_POST['new_amount'] = (float)$_POST['new_amount'];
			// If the 'new amount' is bigger than the standard amount use the 'new amount'
			if ($_POST['new_amount'] > $amount->amount) 
			{
				$amount->amount = $_POST['new_amount'];
			}
		}

		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();
		
		// Get first and last name of the current user		
		$name = explode(' ', !empty($_SESSION['user']['name']) ? $_SESSION['user']['name'] : $_SESSION['user']['username']);
		$first_name = array_shift($name);
		$last_name = join(' ', $name);

		// Set the parameters to be sended to the GoCardelss server		
		$params['pre_authorization[interval_length]'] = (int) $project->period / 7;
		$params['pre_authorization[interval_unit]'] = 'week';
		$params['pre_authorization[description]'] = 'This payment sets up a “one-off” direct debit. Payment will only be taken from your account if the project raises their target within their timescale.';
		$params['pre_authorization[expires_at]'] = date('Y-n-d', strtotime('+'.(int) ($project->period + 2).' DAYS', strtotime($project->date_created)));
		$params['pre_authorization[name]'] = substr($project->title, 0, 255);
		$params['pre_authorization[merchant_id]'] = $project->merchant_id;
		$params['pre_authorization[max_amount]'] = $amount->amount;
		$params['pre_authorization[user][first_name]'] = $first_name;
		$params['pre_authorization[user][last_name]'] = $last_name;
		$params['pre_authorization[user][billing_town]'] = $_SESSION['user']['location_preview'];
		$params['pre_authorization[user][billing_county]'] = $_SESSION['user']['county_name'];
		$params['pre_authorization[user][billing_postcode]'] = $_SESSION['user']['postcode'];
		$params['pre_authorization[user][email]'] = $_SESSION['user']['email'];
		
		$params['client_id'] = $GoCardless->app_id;
		$params['nonce'] = $GoCardless->nonce;
		$params['pre_authorization_url'] = $GoCardless->pre_authorization_url;
		$params['redirect_uri'] = $this->config->config['base_url'] . 'processor/add_preauthorization/';
		$params['state[idproject]'] = $project->idproject;
		$params['state[idamount]'] = $amount->idamount;
		$params['state[max_amount]'] = $amount->amount;
		$params['state[public]'] = !isset($_POST['state']['public']) ? 'yes' : 'no';
		$params['state[reward]'] = !isset($_POST['state']['reward']) ? 'yes' : 'no';
		$params['timestamp'] = $GoCardless->timestamp;
		
		$params['signature'] = $GoCardless->generate_signature($GoCardless->convert_to_encoded_query($params));
		
		ksort($params);
		
		// var_dump($GoCardless->convert_to_encoded_query($params)); // Need debug? See all params here

		// Redirect (send request) to the GoCardless system
		header('Location: '.$GoCardless->pre_authorization_url.'/?'.$GoCardless->convert_to_encoded_query($params));
		exit;
	}
	
	/**
	* After the PreAuthorization request is sent to the GoCardless server it will redirect back to this method, so we can 
	* add the pledge data to the database then send confirmation to GoCardless and then send emails to the project owner
	* and to the user who has pledged
	*
	* @access public
	*/
	public function add_preauthorization() 
	{

		// If the user is not logged in redirect to login
		if (empty($_SESSION['user']))
		{
			print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/user/login/"; self.close();</script></body></html>';
			exit;
		}
		
		// Define array to contain error
		$errors = array();

		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Get the pledge parameters we have sent to the GoCardless server
		$idproject = (isset($_GET['state']['idproject']) ? (int) $_GET['state']['idproject'] : $errors['idproject'] = 'not.set');
		$idamount = (isset($_GET['state']['idamount']) ? (int) $_GET['state']['idamount'] : $errors['idamount'] = 'not.set');
		$max_amount = (isset($_GET['state']['max_amount']) ? (float) $_GET['state']['max_amount'] : 0);
		$public = (isset($_GET['state']['public']) && $_GET['state']['public'] == 'no' ? '0' : '1');
		$reward = (isset($_GET['state']['reward']) && $_GET['state']['reward'] == 'no' ? '0' : '1');

		// Check if the PreAuthorization info sent from the server is valid
		if (isset($_GET['resource_id']) && !empty($_GET['resource_id']) && isset($_GET['resource_type']) && $_GET['resource_type'] == 'pre_authorization' && isset($_GET['state'])) 
		{
			// Check if state is array
			if (is_array($_GET['state'])) 
			{
				$get = array();
				foreach($_GET['state'] as $key => $value)
					$_GET['state[' . $key . ']'] = $value;
					
				unset($_GET['state']);
			}
			
			// Generate signature from returned params
			$get = $_GET;
			unset($get['signature']);
			$query = $GoCardless->convert_to_encoded_query($get);
			$signature = $GoCardless->generate_signature($query);
			
			// Compare our with provided signatures - if they do not match show error
			if ($signature != $_GET['signature']) 
			{
				print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/projects/error/?signature"; self.close();</script> 01</body></html>';
				exit;
			}

			// Get the project from database
			$project = reset($this->projects->get_projects(array("p.idproject" => (int) $idproject)));			
			// Check the project - if cannot find it show error
			if (empty($project)) 
			{
				print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/projects/error/?project"; self.close();</script> 02</body></html>';
				exit;
			}

			// Check the amount - if cannot find it show error
			$amount = reset($this->projects->get_project_amounts(array("idproject" => (int) $idproject, "idamount" => (int) $idamount)));
			if (empty($amount)) 
			{
				print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/projects/error/?amount"; self.close();</script> 03</body></html>';
				exit;
			}
			
			// Check the amount for limits and remaing slots - if cannot find it show error
			if($amount->limited == 'yes' && $amount->remaining <= 0) {
				print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/'.$project->slug.'/?limited"; self.close();</script> 03</body></html>';
				exit;
			}
			
			// If the 'new amount' is bigger than the standard amount use the 'new amount'
			if ($max_amount > $amount->amount) 
			{
				$amount->amount = $max_amount;
			}
			
			// ==== Store the pledge info to the database ==== //
			
				// Set the pledge data to be inserted in the database
				$data = array(
					"idproject"  	=>  $idproject,
					"idamount"  	=>  $idamount,
					"iduser"		=>	$_SESSION['user']['iduser'],
					"status"		=>	"pending",
					"amount"		=>	$amount->amount,
					"public"		=>	$public,
					"reward_want"	=>	$reward,
					"email" 		=>  $_SESSION['user']['email'],
					"resource_id" 	=>  $_GET['resource_id'],
					"resource_type"	=>  $_GET['resource_type'],
					"key"			=>	(isset($_GET['signature']) ? $_GET['signature'] : 'error.no.signature'),
					"date_added"	=>	date("Y-m-d H:i:s")
				);
					
				// Add pledge data to the database
				$this->projects->add_pledge($data);
				// Get the id of the new pledge
				$idpledge = $this->db->insert_id();
				// Set the pledge id to the session
				$_SESSION['last_pledge_id'] = $idpledge;
				
				// Store log of the sent and returned parameters for this pledge to tha database
				$logging = array(
					"iduser"	=>	$_SESSION['user']['iduser'],
					"idproject" =>  $idproject,
					"idamount"  =>  $idamount,
					"idpledge"	=>	$idpledge,
					"command"	=>	"pre_authorization",
					"status"	=>	"success",
					"sent"		=>	'',
					"received"	=>	(!empty($_GET)) ? serialize($_GET) : "",
					"date"		=>	date("Y-m-d H:i:s")
				);
				
				// Get the pledge logs model
				$this->load->model('Logs_model', 'logs');
				// Add the pledge log to the database
				$this->logs->add($logging);
			
			// ==== Confirm the process ==== //
			
				// Get url to confirm the process
				$url = $GoCardless->confirmation_url;
	
				// Set up the needed parameters to confirm the process
				$params['resource_id'] = $_GET['resource_id'];
				$params['resource_type'] = $_GET['resource_type'];
	
				// Set up the needed headers to confirm the process
				$headers[] = 'Authorization: Basic ' . base64_encode($GoCardless->app_id.':'.$GoCardless->app_secret);
	
				// Make request to GoCardless to confirm the pledge and get the response
				$response = $GoCardless->http_post($url, $params, $headers);
				
				// If the response is not OK (status code 200) print error
				if ($response['code'] != 200) 
				{
					print 'APIError:' . $GoCardless->get_error_message($response['code']);
					print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/projects/error/?'.$response['code'].'";</script></body></html>';
					exit;
				}				
					
				// Get the signature assigned to this pledge to be updated to the database
				$key = $_GET['signature'];			
				
				// Update the record in database to mark that the pledge was confirmed and accepted
				$this->projects->save_pledge(array("status" => "accepted"), array("key" => $key));
				
				// Update project pledged amount
				$this->projects->update_pledged_amount($key);

			// ==== Send 'new pledge' email to the project owner ==== //
						
				// Get config object
				$this->load->config('emails');
				
				// Load users model
				$this->load->model('Users_model', 'users');
				
				// Get email data for new pledge email
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
	
				// Get the site title
				$this->load->model('Configuration_model', 'configuration');
				$site_title = (array) reset($this->configuration->get_configuration(array("idconfiguration" => "1")));
				$site_title = reset($site_title);
	
				// Parameters array to be used in the email
				$params = array(
					"[site_name]"		=>	$site_title,
					"[project_name]"	=>	$project->title,
					"[amount]"			=>	$pledge->amount
				);
	
				// If the email is active
				if ($email_data['active'] > 0)
				{
					// Send 'new pledge' email
					send_mail($this->config->item('FROM_EMAIL'), $owner->email, $email_data['subject'], $email_data['text'], $params, $params);
				}
			 
			// ==== Send thanks email to the user who has pledged ==== //
				
				// Get thanks email data from database
				$email_data = (array)reset($this->emails->get_emails(array("idemail" => "15")));
				
				// Set the parameters to be used in the email
				$params = array(
					"[usename]"	=>	$_SESSION['user']['username'],
					"[amount]"	=>	$pledge->amount,
					"[project]"	=>	$project->title,
					"[reward]"	=>	$amount->description
				);
				
				// If the email is active
				if ($email_data['active'] > 0)
				{	
					// Send thanks email
					send_mail($this->config->item('FROM_EMAIL'), $_SESSION['user']['email'], $email_data['subject'], $email_data['text'], $params, $params);
				}

			// ==== Set the notification settings for the project for the user who has pledged ==== //
				
				$member_id = $pledge->iduser;
				$object_type = 'project';
				$object_role = 'support';
				$notification_type = @$pledge_user->alerts_backing; // Type of notifications where user is backer of the object
					
				// Load notification model
				$this->load->model('Notifications_model', 'notifications');
				$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'comment', $notification_type);
				$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'update', $notification_type);
				$this->notifications->configure_event_for_member($member_id, $pledge->idproject, $object_role, $object_type, 'status_change', $notification_type);
			
			// Redirect to thank you page	
			print '<html><head></head><body><script type="text/javascript">window.opener.document.location = "/projects/thanks/"; self.close();</script>04</body></html>';

			exit;
		}
		
		exit($GoCardless->get_error_message(401));
	}
	
	/**
	* Make a request to GoCardless when we add new merchant to get it's merchant id and access token. 
	* Used after we have sent request to GoCardless to add new account for the project owner when 
	* creating new project or when the project GoCardless account is edited - GoCardless is 
	* redirecting back to this method.
	*
	* @access public
	*/
	public function add_merchant() 
	{
		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();

		// Check if merchant id and access token are not already set (the merchant is added) and that the code variable is sent by the GoCardless request 
		if ( ! isset($_SESSION['gocardless']) && isset($_GET['code']) &&  ! empty($_GET['code'])) 
		{
			// Set the url for the request
			$url = $GoCardless->oauth_access_token_url;

			// Set up the needed params
			$params['client_id'] = $GoCardless->app_id;
			$params['code'] = $_GET['code'];
			$params['grant_type'] = 'authorization_code';
			$params['redirect_uri'] = $this->config->config['base_url'] . 'processor/add_merchant/';

			// Set up the needed headers
			$headers = array();
			$headers[] = 'Accept: application/json';
			// Set up the authorization
			$headers[] = 'Authorization: Basic ' . base64_encode($GoCardless->app_id.':'.$GoCardless->app_secret);

			// Make request to GoCardless and get the response
			$response = $GoCardless->http_post($url, $params, $headers);

			// If response is not OK print error
			if ($response['code'] != 200) 
			{
				print 'APIError: ' . $GoCardless->get_error_message($response['code']);
				exit();
			}

			// JSON decode the response
			$response['content'] = (array) json_decode($response['content']);

			// Store the access token, token_type and scope into the session
			if(!isset($_SESSION['gocardless']))
				$_SESSION['gocardless'] = $response['content'];
			
			// Get the merchant id 
			$response['content']['scope'] = (int) str_replace('manage_merchant:', '', $response['content']['scope']);
			
			// Setup the merchant id and the access token and close the popup window
			print '<html><head></head><body><script type="text/javascript">window.opener.document.getElementById("merchant_id").value = "'.$response['content']['scope'].'"; window.opener.document.getElementById("access_token").value = "'.$response['content']['access_token'].'"; window.opener.document.getElementById("gocardless-no-block").style.display = "none"; window.opener.document.getElementById("gocardless-no").style.display = "none"; window.opener.document.getElementById("gocardless-yes").style.display = "block"; self.close();</script></body></html>';
			exit;
		} 
		// If the merchant data is already stored to the session
		elseif (isset($_SESSION['gocardless'])) 
		{
			// Get the merchant data from the session
			$response['content'] = $_SESSION['gocardless'];

			// Get the merchant id 
			$response['content']['scope'] = (int) str_replace('manage_merchant:', '', $response['content']['scope']);

			// Setup the merchant id and the access token and close the popup window
			print '<html><head></head><body><script type="text/javascript">window.opener.document.getElementById("merchant_id").value = "'.$response['content']['scope'].'"; window.opener.document.getElementById("access_token").value = "'.$response['content']['access_token'].'"; window.opener.document.getElementById("gocardless-no-block").style.display = "none"; window.opener.document.getElementById("gocardless-no").style.display = "none"; window.opener.document.getElementById("gocardless-yes").style.display = "block"; self.close();</script></body></html>';
			exit;
		}

		// If the method is not called correctly show unauthorized error
		exit($GoCardless->get_error_message(401));
	}
	
	/**
	* Web hooks - used when the real transactions are made and the money are transferred from the backer to the 
	* project owner to tell if the transaction was successful and to log the result. The method is called by 
	* GoCardless. Works similar to Paypal IPN. 
	*
	* @access public
	*/
	function webhooks() 
	{
		// Include GoCardless Class
		require_once(APPPATH.'third_party/gocardless/GoCardless.php');
		$GoCardless = new GoCardless();

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Load logs model
		$this->load->model('Logs_model', 'logs');

		// Decode the request...
		$_POST = @json_decode($_POST);

	// ==== Send email to the dev team ==== //

		// Get email config
		$this->load->config('emails');

		// Send wlecome email with confirmation link
		send_mail('dev@mtr-design.com', $this->config->item('FROM_EMAIL'), '[People Fund] Web Hooks signal', var_export($_POST, true));
		
		// If bill param is not set show error
		if ( ! isset($_POST['bill'])) 
		{
			// Set header and error
			header("HTTP/1.1 200 OK");
			$error[] = $_POST['error'] = 'Invalid request';
			
			// Print the JSON
			print json_encode($_POST);

			exit;
		}

		// Generate signature from returned params
		$post = $_POST['bill'];

		$query = $GoCardless->convert_to_encoded_query($post);
		$signature = $GoCardless->generate_signature($query);

		// Compare our with provided signatures - if they do not match log error
		if (!isset($_POST['signature']) OR $signature != $_POST['signature']) 
		{
			// Set header and error
			header("HTTP/1.1 200 OK");
			$error[] = $_POST['error'] = 'Invalid signature';

			// Log sent and returned params to the database
			$logging = array(
				"idproject" =>  0,
				"idpledge"	=>	0,
				"command"	=>	"webhooks",
				"status"	=>	"error",
				"sent"		=>	'',
				"received"	=>	@serialize($_POST),
				"error"		=>	@serialize($error),
				"date"		=>	date("Y-m-d H:i:s")
			);
			// Insert pledge log to the database
			$this->logs->add($logging);
			
			// Print the JSON
			print json_encode($_POST);

			exit;
		}

		// Get pledge data
		$pledge = reset($this->projects->get_project_pledges(array("pl.resource_id" => $_POST['bill']['source_id'], 'pl.resource_type' => $_POST['bill']['source_type'])));
		
		// Check if pledge is not found log error
		if(empty($pledge)) 
		{
			// Set header and error
			header("HTTP/1.1 200 OK");
			$error[] = $_POST['error'] = 'Pledges not found';

			// Log sent and returned params to the database
			$logging = array(
				"idproject" =>  0,
				"idpledge"	=>	0,
				"command"	=>	"webhooks",
				"status"	=>	"error",
				"sent"		=>	'',
				"received"	=>	@serialize($_POST),
				"error"		=>	@serialize($error),
				"date"		=>	date("Y-m-d H:i:s")
			);
			// Insert pledge log to the database
			$this->logs->add($logging);

			// Print the JSON
			print json_encode($_POST);

			exit;
		}

		// Set header
		header("HTTP/1.1 200 OK");
		
		// If the transaction was ok
		if ($_POST['bill']['status'] == 'paid')
		{
			// Log sent and returned params to the database
			$logging = array(
				"idproject" =>  $pledge->idproject,
				"idpledge"	=>	$pledge->idpledge,
				"command"	=>	"webhooks",
				"status"	=>	"success",
				"sent"		=>	'',
				"received"	=>	@serialize($_POST),
				"date"		=>	date("Y-m-d H:i:s")
			);
			// Insert pledge log to the database
			$this->logs->add($logging);
						
			// Update pledge status as successful
			$this->projects->save_pledge(array("status" => "transferred"), array("idpledge" => $pledge->idpledge));

		// If the transaction is not ok for some reason
		} 
		// If the transaction was not ok
		else 
		{						
			// Get error
			$error[] = $_POST['failure_reason'];

			// Log sent and returned params to the database
			$logging = array(
				"idproject" =>  $pledge->idproject,
				"idpledge"	=>	$pledge->idpledge,
				"command"	=>	'webhooks',
				"status"	=>	"error",
				"sent"		=>	'',
				"received"	=>	@serialize($_POST),
				"error"		=>	@serialize($error),
				"date"		=>	date("Y-m-d H:i:s")
			);
			// Insert pledge log to the database
			$this->logs->add($logging);

			// Mark pledge as failed
			$this->projects->save_pledge(array("status" => "failed"), array("idpledge" => $pledge->idpledge));
		}

		// Print the JSON
		print json_encode($_POST);

		exit;
	}
} 

/* End of file processor.php */
/* Location: ./application/controllers/processor.php */