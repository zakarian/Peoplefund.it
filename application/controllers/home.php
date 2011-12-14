<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Home controller - handles the home page
* 
* @package PeopleFund 
* @category Home 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Home extends Base_Controller {

	/** 
	* Displays the home page  
	* Gets most funded, editors pick, most recent and most liked projects and passes them to the view
	* 
	* @access public 
	*/
	public function index()
	{
		
		// Loading projects and categories modules
		$this->load->model('Projects_model', 'projects');
		$this->load->model('Categories_model', 'categories');
		
		// Setting variables to be passed to the view 
		$vars = array();
		$vars['current_page'] = "index";
		$vars['page_title'] = "People Fund";
		// Set the image to be used when sharing this page on Facebook
		$vars['fb_image'] = 'logo';
		
		// If there are error messages in the session pass them to the view
		if(!empty($_SESSION['error'])){
			$vars['errors'][] = $_SESSION['error'];
			unset($_SESSION['error']);
		}
		
		// If there is success message in the session pass it to the view
		if(!empty($_SESSION['success'])){
			$vars['success'] = $_SESSION['success'];
			unset($_SESSION['success']);
		}
		
		// Get editors pick projects 
		$this->projects->limit = 3; // We are getting 3 editors pick projects - the first will be shown on the top of the home page and the other two in the editors pick section
		$vars['picks_projects'] = $this->projects->get_picks_projects_home();
		// Keep the ids of the selected projects and mark them as reserved, so they won't be used on the other queries
		foreach($vars['picks_projects'] as $project) $this->projects->reservedIds[] = $project->idproject;	
		
		// Get the most funded projects
		$this->projects->limit = 2;
		$vars['most_funded'] = $this->projects->get_funded_projects_home();
		// Keep the ids of the selected projects and mark them as reserved, so they won't be used on the other queries
		foreach($vars['most_funded'] as $project) $this->projects->reservedIds[] = $project->idproject;
		
		// Get the most recent projects
		$this->projects->limit = 2;
		$vars['recent_projects'] = $this->projects->get_recent_projects_home();
		// Keep the ids of the selected projects and mark them as reserved, so they won't be used on the other queries
		foreach($vars['recent_projects'] as $project) $this->projects->reservedIds[] = $project->idproject;	
		
		// Get the most liked projects
		$this->projects->limit = 2;
		$vars['most_liked'] = $this->projects->get_liked_projects_home();
		
		// Get the categories
		$vars['categories'] = $this->categories->get_categories();
		
		// Display the view
		$this->view('index', $vars);
	}
}

/* End of file home.php */ 
/* Location: ./application/controllers/home.php */