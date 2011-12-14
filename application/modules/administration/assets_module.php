<?php 

// Assets class
class Assets_Module extends CI_Module {

	// View dispatcher - adds header, footer and params
	public function view($view, $vars = array()) {
		// Do not show the header and footer if the view is 
		if (empty($vars['popup'])) {
			// If loading tinyMCE - load header with MCE js + options
			if(!empty($vars['tinyMCE'])){
				$this->load->view('administration/header', array("tinyMCE" => TRUE));
				
			// If not loading tinyMCE - load header
			} else {
				$this->load->view('administration/header');
			}
		}
		
		// Load content
		$this->load->view($view, $vars);
		
		if (empty($vars['popup'])) {
			// Load footer
			$this->load->view('administration/footer');
		}
	}
	
	// Index action
	public function index() {

		// Template vars array
		$vars = array();
		
		// If we have message
		if(!empty($_SESSION['message'])){
			$vars['message'] = $_SESSION['message'];
			unset($_SESSION['message']);
		}

		// Display template
		$this->view('administration/assets/browse', $vars);
	}
	
	// Browse action
	public function browse($dir = ""){
		// Form default dir
		$default_dir = SITE_DIR."public/uploads/";
		
		// If not empty dir
		if(!empty($dir)){
			$dir = base64_decode($dir);
			$dir = str_replace("//", "/", $dir);
			
			// If the path is not containing default path - add it
			if(!strstr($dir, $default_dir)){
				$dir = $default_dir . $dir;
			}
		
			// Check if we are going one level up
			if(strstr($dir, "/../")){
				$dir = explode("/", $dir);
				$dir = array_slice($dir, 0, -3);
				$dir = implode("/", $dir)."/";
			}
			
		// If the dir is empty - get default dir
		} else {
		
			// Main directory
			$dir = $default_dir;
		}
		
		$webpath = str_replace(SITE_DIR.'public', '', $dir);

		// Open files dir
		$fd = opendir( $dir );

		// Scan directory for files
		$items = array();
		while($file = readdir($fd)) {

			// Exlude files
			if($file == '.' OR $file == 'trash' OR $file == '.htaccess' OR $file == '.htpasswd') continue;
			
			// Get file ext
			$ext = end(explode(".", $file));
			if(in_array($ext, array("txt", "doc", "rtf", "xls", "csv"))){
				$class = "text";
			}
			else if(in_array($ext, array("rar", "zip", "tar"))){
				$class = "tar";
			}
			else if(in_array($ext, array("pdf"))){
				$class = "pdf";
			}
			else if(in_array($ext, array("jpg", "jpeg", "bmp", "gif", "png"))){
				$class = "image";
			}
			else if(in_array($ext, array("html"))){
				$class = "html";
			} else {
				$class = "misc";
			}
			
			// Don't show ".." option for the index
			if($dir == $default_dir){
				if($file == "..") continue;
			}

			// Add dirs
			if(is_dir($dir.$file)) {
				$folders[]  = array('name' => $file, 'type' => 'dir', 'path' => $dir.$file."/");
			}

			// Add files
			if(!is_dir($dir.$file)) {
				$files[] = array('name' => $file, 'type' => 'file', 'class' => $class, 'size' => ceil(@filesize($dir.$file) * 0.00098), 'path' => $dir.$file."/", 'webpath' => $webpath.$file);
			}
		}
		
		// Regroup arrays
		if(!empty($folders)){
			foreach($folders AS $k => $v){
				$items['files'][] = $v;
			}
		}
		if(!empty($files)){
			foreach($files AS $k => $v){
				$items['files'][] = $v;
			}
		}
		
		// Add current directory path
		$items['current'] = str_replace($default_dir, "/", $dir);
		
		// Return json encoded files
		echo json_encode($items);
	}
	
	
	// Delete file
	public function delete($file = ""){

		// Form path
		$file = base64_decode($file);
		
		// Remoev ending slash
		$file = substr($file, 0, -1);
		
		if(is_dir($file)){
			rmdir($file);
		} else {
			unlink($file);
		}
		
		// Return current path
		$file = explode("/", $file);
		array_pop($file);
		echo implode("/", $file)."/";
	}
	
	// Create folder
	public function create_folder(){
	
		// Form default dir
		$default_dir = SITE_DIR."public/uploads/";
		
		// Get folder name
		$folder = $this->input->post("name");
		$path = $this->input->post("path");
		
		// Form folder path
		$path = $default_dir . $path . $folder . "/";
		$path = str_replace("//", "/", $path);
		
		// Create folder
		if(!is_dir($path)){
			mkdir($path);
			chmod($path, 0777);
		}
		
		// Return current path
		$path = explode("/", $path);
		array_pop($path);
		array_pop($path);
		echo implode("/", $path)."/";
	}
	
	// Add new file
	public function add_file(){

		// Form default dir
		$default_dir = SITE_DIR."public/uploads/";
		
		// Get folder name
		$folder = $this->input->post("name");
		$path = $this->input->post("path");
		
		// Form folder path
		$path = $default_dir . $path . $folder . "/";
		$path = str_replace("//", "/", $path);
		
		// Create folder
		$target_path = $path . basename( $_FILES['file']['name']); 
		move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
		chmod($path, 0777);
			
		// Return current path
		$path = explode("/", $path);
		array_pop($path);
		echo implode("/", $path)."/";
	}
	
	function docs() {
		$vars = array();
		$vars['section_id'] = '/' . $this->uri->segment(5); 
		$vars['input_id'] = $this->uri->segment(4);
		$vars['popup'] = 1;
		$this->view('administration/assets/docs', $vars);
	}
	
	function download($file = "") {
		// Make sure script execution doesn't time out.
		// Set maximum execution time in seconds (0 means no limit).

		set_time_limit(0);
		
		// Check downloader is admin or manager
		if(!empty($file)) {
			$file = SITE_DIR."public".base64_decode($file);

			$this->output_file($file);
		} else
			$this->download_forbidden();
		exit;
	}
	
	function output_file($file) {
		/*
		This function takes a path to a file to output ($file)
		*/
		if(!is_readable($file)) die('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><HTML><HEAD><TITLE>404 Not Found</TITLE></HEAD><BODY><H1>Not Found</H1><P>The requested URL '.$_SERVER['REQUEST_URI'].' was not found on this server.</P></BODY></HTML>');
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		readfile($file);
		exit;
	}	

	function download_not_found() {
		print '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><HTML><HEAD><TITLE>404 Not Found</TITLE></HEAD><BODY><H1>Not Found</H1><P>The requested URL '.$_SERVER['REQUEST_URI'].' was not found on this server.</P></BODY></HTML>';
		
		exit; 
	}

	function download_forbidden() {
		print '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><HTML><HEAD><TITLE>403 Forbidden</TITLE></HEAD><BODY><H1>Forbidden</H1><P>You don\'t have permission to access '.$_SERVER['REQUEST_URI'].' on this server.</P></BODY></HTML>';

		exit; 
	}
}