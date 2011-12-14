<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Files controller 
* Handles the files operations such as upload 
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Files extends Base_Controller {

	/**
	*  Uploader action
	*  Generates browse field for upload to be used from Tiny MCE editor
	*  
	* @access public
	*/	
	public function uploader()
	{
		// Terminate if the user is not logged in
		if(empty($_SESSION['user']['iduser']))
		{
			exit();
		}

		// Loads the upload field
		$this->load->view('files/uploader', $vars = array());
	}
}

/* End of file files.php */
/* Location: ./application/controllers/files.php */