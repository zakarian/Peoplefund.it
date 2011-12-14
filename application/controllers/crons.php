<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Crons controller 
* Handles cron operations that are regularly called 
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Crons extends Base_Controller {

	/**
	* Used to check the projects if they are finished and if so if the project has collected the needed amount 
	* tell GoCardless to transfer the money from backers to project owner. 
	*
	* @access public
	*/
	public function projects()
	{
		// Check if the method is called by cron
		if (!defined('CRON') OR !CRON)
		{
			redirect('/');
			exit();
		}	

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Set cancel and thanks urls
		$cancelURL  = 	$this->config->config['base_url']."projects/error/";
		$returnURL 	= 	$this->config->config['base_url']."projects/thanks/";
		
		// Get all projects that are open
		$expired_projects = $this->projects->get_expired_projects();

		// For each project
		foreach ($expired_projects AS $project)
		{			
			// Get transactions for the project
			$pledges = $this->projects->get_project_pledges(array("pl.status" => "accepted", "pl.idproject" => $project->idproject));
			
			// If no pledges - set project as closed and continue to next project
			if (empty($pledges))
			{
				// Set project as closed
				$this->projects->save_project(array("status" => "closed"), $project->idproject);

				continue;
			}
			
			// Check amounts - if the project has ENOUGH PLEDGES transfer the money to the project owner
			if ($project->amount <= $project->amount_pledged)
			{
				// Set project as closed
				$this->projects->save_project(array("status" => "closed", "event_type" => "successful"), $project->idproject);

				// Include GoCardless Class
				require_once(APPPATH.'third_party/gocardless/GoCardless.php');
				$GoCardless = new GoCardless();

				// Set url for the GoCardless transfer request
				$url = str_replace('{merchant_id}', $project->merchant_id, $GoCardless->pre_authorizations_merchant_bills_url);

				// Set headers for the GoCardless transfer request
				$headers = array();
				$headers[] = 'Accept: application/json';
				$headers[] = 'Authorization: bearer ' . $project->access_token;
				
				// For each preapproval
				foreach ($pledges AS $pledge)
				{

					// Prepare payment parameters for the GoCardless transfer request
					$params['bill']['amount'] = $pledge->amount;
					$params['bill']['pre_authorization_id'] = $pledge->resource_id;
					$params['bill']['name'] = substr($project->title, 0, 255);
					$params['bill']['description'] = 'This payment sets up a �one-off� direct debit. Payment will only be taken from your account if the project raises their target within their timescale.';
					
					// The payments to both receivers - project owner and peoplefund are setup by GoCardless dev team
					// ...

					// Make transaction
					$response = $GoCardless->http_post($url, $params, $headers);

					// Log sent and returned params to the database
					$logging = array(
						"idproject" =>  $pledge->idproject,
						"idpledge"	=>	$pledge->idpledge,
						"command"	=>	"bill",
						"status"	=>	"success",
						"sent"		=>	serialize($params),
						"received"	=>	(!empty($response)) ? serialize($response) : "",
						"date"		=>	date("Y-m-d H:i:s")
					);
					// Load logs model
					$this->load->model('Logs_model', 'logs');
					// Insert pledge log that the money transfer was successful
					$this->logs->add($logging);
				}

				// GoCardless will use webhooks method from processor controller to tell us if the transaction is successful				
			}
			// If the project hasn't ENOUGH PLEDGES cancel the preapprovals			
			else 
			{
				// Set project as closed
				$this->projects->save_project(array("status" => "closed", "event_type" => "unsuccessful"), $project->idproject);

				// Include GoCardless Class
				require_once(APPPATH.'third_party/gocardless/GoCardless.php');
				$GoCardless = new GoCardless();

				// For each preapproval
				foreach($pledges AS $pledge){
					
					// Try to cancel the preapproval
					// We don't need to do nothing. The pre approvals will expire after two days :)
					
					// Log sent and returned params to the database
					$logging = array(
						"idproject" =>  $pledge->idproject,
						"idpledge"	=>	$pledge->idpledge,
						"command"	=>	"bill",
						"status"	=>	"error",
						"sent"		=>	'',
						"received"	=>	'The project hasn\'t ENOUGH PLEDGES',
						"date"		=>	date("Y-m-d H:i:s")
					);
					// Load logs model
					$this->load->model('Logs_model', 'logs');
					// Insert pledge log that the preapproval was canceled
					$this->logs->add($logging);
						
					// Update pledge status to cancelled
					$this->projects->save_pledge(array("status" => "cancelled"), array("idpledge" => $pledge->idpledge));					
				}
			}
			
		}
		
		// Print success message
		echo "\nProjects checked.\n";
	}

	/**
	* Not used anymore. Paypal payment.
	*
	* @access public
	*/
	public function projects_paypal(){
		if ( ! defined('CRON') OR !CRON)
		{
			redirect('/');
			exit();
		}	

		// Load projects model
		$this->load->model('Projects_model', 'projects');
		
		// Set urls
		$cancelURL  = 	$this->config->config['base_url']."projects/error/";
		$returnURL 	= 	$this->config->config['base_url']."projects/thanks/";
		
		// Get all projects that are open
		$expired_projects = $this->projects->get_expired_projects();

		// For each project
		foreach($expired_projects AS $project){
			
			// Get transactions
			$pledges = $this->projects->get_project_pledges(array("pl.status" => "accepted"), array("from" => 0, "count" => 50));
			
			// If no pledges - continue to next project
			if(empty($pledges)){
				// Set project as closed
				$this->projects->save_project(array("status" => "closed"), $project->idproject);

				continue;
			}
			
			// Check amounts - if the project has ENOUGH PLEDGES
			if($project->amount <= $project->amount_pledged){
				// Set project as closed
				$this->projects->save_project(array("status" => "closed", "event_type" => "successful"), $project->idproject);

				// Include PayPal Payments Adaptive Payments Class
				require_once(APPPATH.'third_party/paypal/AdaptivePayments.php');
				
				// For each preapproval
				foreach($pledges AS $pledge){

					// Prepare payments
					$payRequest = new PayRequest();
					$payRequest->actionType = "PAY";
					$payRequest->cancelUrl = $cancelURL;
					$payRequest->returnUrl = $returnURL;
					$payRequest->clientDetails = new ClientDetailsType();
					$payRequest->clientDetails->applicationId = $this->config->config['application_id'];
					$payRequest->clientDetails->deviceId = $this->config->config['device_id'];
					$payRequest->clientDetails->ipAddress = "127.0.0.1";
					$payRequest->currencyCode = $this->config->config['currency_code'];
					$payRequest->senderEmail = $pledge->email;
					$payRequest->requestEnvelope = new RequestEnvelope();
					$payRequest->requestEnvelope->errorLanguage = "en_US";
					$payRequest->preapprovalKey = $pledge->key;
			
					// Set first receiver - the site
					$receiver1 = new receiver();
					$receiver1->email = $this->config->config['charge_email'];
					$receiver1->amount = $pledge->amount * ($this->config->config['charge_percent'] / 100);
					
					// Set second receiver - the project owner
					$receiver2 = new receiver();
					$receiver2->email = $project->paypal_email;
					
					// Calculate the percent for the project owner
					$percent = 100 - $this->config->config['charge_percent'];
					$receiver2->amount = $pledge->amount * ($percent / 100);
					
					// Add receivers
					$payRequest->receiverList = new ReceiverList();
					$payRequest->receiverList = array($receiver1, $receiver2);

					// Make transaction
					$ap = new AdaptivePayments();
					$response = $ap->Pay($payRequest);

					// If the transaction wasn't any errors
					if(strtoupper($ap->isSuccess) != 'FAILURE'){
						
						// If the transaction was ok
						if($response->paymentExecStatus == "COMPLETED"){
						
							// Log sent and returned params
							$logging = array(
								"idproject" =>  $pledge->idproject,
								"idpledge"	=>	$pledge->idpledge,
								"command"	=>	"preapproval_pay",
								"status"	=>	"success",
								"sent"		=>	serialize($payRequest),
								"received"	=>	(!empty($response)) ? serialize($response) : "",
								"date"		=>	date("Y-m-d H:i:s")
							);
							$this->load->model('Logs_model', 'logs');
							$this->logs->add($logging);
						
							// Update pledge status
							$this->projects->save_pledge(array("status" => "transferred"), array("idpledge" => $pledge->idpledge));
							
						// If the transaction is not ok for some reason
						} else {
						
							// Get error
							$error = $ap->getLastError();
							
							// Log sent and returned params
							$logging = array(
								"idproject" =>  $pledge->idproject,
								"idpledge"	=>	$pledge->idpledge,
								"command"	=>	"preapproval_pay",
								"status"	=>	"error",
								"sent"		=>	serialize($payRequest),
								"received"	=>	(!empty($response)) ? serialize($response) : "",
								"error"		=>	(!empty($error)) ? serialize($error) : "",
								"date"		=>	date("Y-m-d H:i:s")
							);
							$this->load->model('Logs_model', 'logs');
							$this->logs->add($logging);
							
							// Update pledge status
							$this->projects->save_pledge(array("status" => "failed"), array("idpledge" => $pledge->idpledge));
						}
					}
				}
				
			// If the project hasn't ENOUGH PLEDGES
			} else {
				// Set project as closed
				$this->projects->save_project(array("status" => "closed", "event_type" => "unsuccessful"), $project->idproject);

				// Include PayPal Payments Adaptive Payments Classes
				require_once(APPPATH.'third_party/paypal/AdaptivePayments.php');
				require_once(APPPATH.'third_party/paypal/Stub/AP/AdaptivePaymentsProxy.php');

				// For each preapproval
				foreach($pledges AS $pledge){

					// Form cancel params
					$CPRequest = new CancelPreapprovalRequest();
					$CPRequest->requestEnvelope = new RequestEnvelope();
					$CPRequest->requestEnvelope->errorLanguage = "en_US";
					$CPRequest->preapprovalKey = $pledge->key; 
					
					// Try to cancel the preapproval
					$ap = new AdaptivePayments();
					$response = $ap->CancelPreapproval($CPRequest);
					
					// If the cancel operation was ok
					if(strtoupper($ap->isSuccess) != 'FAILURE'){
						
						// Update pledge status
						$this->projects->save_pledge(array("status" => "cancelled"), array("idpledge" => $pledge->idpledge));
						
					// If the transaction is not ok for some reason
					} else {
						
						// Update pledge status
						$this->projects->save_pledge(array("status" => "failed"), array("idpledge" => $pledge->idpledge));
					}
				}
			}
			
		}
	}
	
	/**
	* Send emails to users to notify them that they have new alerts on the site.
	* Emails could be sent instantly, daily, weekly and monthly.
	*
	* @access public
	*/
	public function notifications()
	{
		// Check if the method is called by cron
		if ( ! defined('CRON') OR !CRON)
		{
			redirect('/');
			exit();
		}	
		
		// Load notifications model
		$this->load->model('Notifications_model', 'notifications');		
		
		// Send notifications
		$this->notifications->send_email_notification('instant');
		$this->notifications->send_email_notification('daily');
		$this->notifications->send_email_notification('weekly');
		$this->notifications->send_email_notification('monthly');
		
		// Print success message
		echo "\nNotifications sent.\n";
	}
}

/* End of file crons.php */
/* Location: ./application/controllers/crons.php */