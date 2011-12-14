<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		Vladimir Nenov < vladimir.nenov@mtr-design.com >
 * @link		n/a
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Module Class
 *
 * This class object is the super handler for each modules
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Vladimir Nenov < vladimir.nenov@mtr-design.com >
 * @link		n/a
 */
class CI_Module {

    protected $method = ''; 
    protected $parameters = '';
    
    protected $ci = null; //CodeIgniter instance
    
    public function __construct($parameters = array()){
    
        $this->ci =& get_instance();
    
        //Set default method to be 'index'
        if (!empty($parameters)){
            $method = array_shift($parameters);
        }else{
            $method = 'index';
        }
    
        $this->run($method, $parameters);
    }

    
    /**
     * Entry point for each module
     */
    public function run($method, $parameters = array()){
        $this->method = $method;
        $this->parameters = $parameters;
        
        $this->pre_run();
    
        if (method_exists($this, $method)){
			call_user_func_array(array($this, $method), $parameters);
        }else{
            $this->error(); //Handle errors
        }
    
        $this->post_run();
    }

    
    /**
     * Default method
     */
    public function index(){
        //Leave empty in the library
    }
    
    /**
     * Error handler for all non-existing methods
     */
    public function error(){
        show_404($_SERVER['REQUEST_URI']);
		exit;
    }
    
    /**
     * Pseudo-constructor method. Executed every time before the actual method
     */
    public function pre_run(){
        //Leave empty in the library
    }
    
    /**
     * Pseudo-destructor method. Executed after each actual method (if not exited before)
     */
    public function post_run(){
        //Leave empty in the library
    }

	/**
	 * __get
	 *
	 * Allows modules to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
    
}
// END Module class

/* End of file Module.php */
/* Location: ./system/core/Module.php */