<?php
// Include our custom abstract controller
require_once(APPPATH.'core/base_controller.php');

/** 
* Map controller 
* Helps to handle google maps functionality. It is used by maps.js to get data about searched projects
* 
* @package PeopleFund 
* @category Administration 
* @author MTR Design 
* @link http://peoplefund.it 
*/
class Map extends Base_Controller {

	/** 
	* Search projects according to the url parameters and print the data about them as JSON 
	* Used by map.js
	* @access public 
	*/
	public function index()
	{
		// Load Projects model
		$this->load->model('Projects_model', 'projects');

		// Get the url parameters to $uri array
		$uri = $this->uri->segment_array();
		// Order projects by date
		$vars['filter_type'] = 'latest';
		// Declare array to be passed to the model
		$vars['searchSql'] = array();
		// Declare array to send the result as JSON
		$projects = array();
		
		// Loop through url segments to take the search options
		foreach($uri as $segment)
		{
			// If search by keyword - used to search for keyword in project title, about, outcome and slug
			if(strstr($segment, 'keyword'))
			{
				// Take the keyword from the segment
				$vars['searchSql']['keywords'] = str_replace('keyword:', '', $segment);
				// Escape and decode
				$vars['searchSql']['keywords'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['keywords']))));
			}
			// If search by category			
			if(strstr($segment, 'category'))
			{
				// Take the category from the segment				
				$vars['searchSql']['category'] = urldecode(str_replace('category:', '', $segment));
				// Escape and decode				
				$vars['searchSql']['category'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['category']))));
			}
			// If search by string			
			if(strstr($segment, 'string'))
			{
				// Take the search string from the segment				
				$vars['searchSql']['string'] = urldecode(str_replace('string:', '', $segment));
				// Escape and decode				
				$vars['searchSql']['string'] = mysql_real_escape_string(h(st(urldecode($vars['searchSql']['string']))));
			}
		}
		
		// Check if there are search conditions and if so get the projets 
		if(isset($vars['searchSql']))
			$projects = $this->projects->public_search($vars['filter_type'], $vars['searchSql'], 0);
		
		// Print the searched projects as JSON - to be used by maps.js
		echo json_encode($projects);
	}
	
	/**
	* Call the projects page translating the POST search request to url parameters
	* 
	* @access public 
	*/
	public function search()
	{
		// Call the project page to show searched projects
		redirect("/projects/index/search:".urlencode($_POST['string'])."/");
	}
}

/* End of file map.php */
/* Location: ./application/controllers/map.php */