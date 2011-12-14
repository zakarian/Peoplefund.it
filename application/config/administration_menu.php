<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Define the Administration Menu's . Custom config for Administration Controller
|--------------------------------------------------------------------------
|
| Scheme:   $config['administration_menu'][ module_name (lowercase) ]['name'] -> Display Name
|           $config['administration_menu'][ module_name (lowercase) ]['url'] -> Display Link
|           $config['administration_menu'][ module_name (lowercase) ]['buttons'][ button_text_1 (case_insensitive) ] -> Buton 1
|           $config['administration_menu'][ module_name (lowercase) ]['buttons'][ button_text_2 (case_insensitive) ] -> Buton 2
|           $config['administration_menu'][ module_name (lowercase) ]['sub'][ submodule_name ]['name'] -> Display Name
|           $config['administration_menu'][ module_name (lowercase) ]['sub'][ submodule_name ]['url'] -> Display Link
*/

// Pages section
$config['administration_menu']['pages']['name']                        			= 'Pages';
$config['administration_menu']['pages']['url']                         			= '/administration/pages/';
$config['administration_menu']['pages']['buttons']['Add Section']          		= '/administration/pages/add_section';
$config['administration_menu']['pages']['buttons']['Add Page']          		= '/administration/pages/add';

// Assets section
$config['administration_menu']['assets']['name']                        		= 'Assets';
$config['administration_menu']['assets']['url']                         		= '/administration/assets/';
$config['administration_menu']['assets']['buttons']['Add Multiple Files']       = 'javascript:;" onClick="showMultipleFiles();';
$config['administration_menu']['assets']['buttons']['Add File']          		= 'javascript:;" onClick="showAddFile();';
$config['administration_menu']['assets']['buttons']['Add Folder']          		= 'javascript:;" onClick="showCreateFolder();';

// Projects section
$config['administration_menu']['projects']['name']                              = 'Projects';
$config['administration_menu']['projects']['sub'] = array(
	'projects'		=> array(
		'name'			=> 'Projects',
		'url'			=> '/administration/projects/',
		'buttons'		=> array(),
	),
	'categories'		=> array(
		'name'			=> 'Categories',
		'url'			=> '/administration/categories/',
		'buttons'		=> array('Add new' => '/administration/categories/add'),
	),
	'updates'		=> array(
		'name'			=> 'Updates',
		'url'			=> '/administration/projects/updates/',
		'buttons'		=> array(),
	),
	'comments'		=> array(
		'name'			=> 'Comments',
		'url'			=> '/administration/projects/comments/',
		'buttons'		=> array(),
	),
	'reports'		=> array(
		'name'			=> 'Reports',
		'url'			=> '/administration/projects/reports/',
		'buttons'		=> array(),
	)
);
$config['administration_menu']['projects']['url']                               = '/administration/projects/';

// Menu section
//$config['administration_menu']['menu']['name']                        			= 'Menu';
//$config['administration_menu']['menu']['url']                         			= '/administration/menu/';
//$config['administration_menu']['menu']['buttons']['Add Menu']          			= '/administration/menu/add';

// Users section
$config['administration_menu']['users']['name']                                 = 'Members';
$config['administration_menu']['users']['url']                                  = '/administration/users/';
$config['administration_menu']['users']['buttons']['Add New']                   = '/administration/users/add';
$config['administration_menu']['users']['buttons']['CSV Export']                = $_SERVER['REQUEST_URI'].'?csv';

$config['administration_menu']['users']['sub'] = array(
	'project_owners'		=> array(
		'name'			=> 'Project owners',
		'url'			=> '/administration/users/project_owners/',
		'buttons'		=> array(),
	),
	'project_backers'	=> array(
		'name'			=> 'Project backers',
		'url'			=> '/administration/users/project_backers/',
		'buttons'		=> array(),
	),
	'projecthelpers'	=> array(
		'name'			=> 'Project helpers',
		'url'			=> '/administration/users/project_helpers/',
		'buttons'		=> array(),
	)
);


// Pledges section
$config['administration_menu']['pledges']['name']                               = 'Pledges';
$config['administration_menu']['pledges']['url']                                = '/administration/pledges/';

// Inmail section
$config['administration_menu']['inmail']['name']                         		= 'Messages';
$config['administration_menu']['inmail']['url']                          		= '/administration/inmail/';

// Administrators section
$config['administration_menu']['administrators']['name']                        = 'Administrators';
$config['administration_menu']['administrators']['url']                         = '/administration/administrators/';
$config['administration_menu']['administrators']['buttons']['Add New']          = '/administration/administrators/add';


// Config section
$config['administration_menu']['configuration']['name']                         = 'Settings';
$config['administration_menu']['configuration']['url']                          = '/administration/configuration/';
$config['administration_menu']['configuration']['sub'] = array(
	'partners'	=> array(
		'name'			=> 'Partners',
		'url'			=> '/administration/partners/',
		'buttons'		=> array('Add New' => '/administration/partners/add'),
	),
	'texts'	=> array(
		'name'			=> 'Site texts',
		'url'			=> '/administration/texts/',
		'buttons'		=> array('Add New' => '/administration/texts/add'),
	),
	'emails'		=> array(
		'name'			=> 'Emails',
		'url'			=> '/administration/emails/',
		'buttons'		=> array('Add New' => '/administration/emails/add'),
	)
);

// Questions and answers section
/*
$config['administration_menu']['questions']['name']                         = 'Q&A';
$config['administration_menu']['questions']['url']                          = '/administration/questions/';
$config['administration_menu']['questions']['sub'] = array(	
	'questions'	=> array(
		'name'			=> 'Questions',
		'url'			=> '/administration/questions/',
		'buttons'		=> array()
	),
	'answers'	=> array(
		'name'			=> 'Answers',
		'url'			=> '/administration/answers/',
		'buttons'		=> array()
	),
	'qacategories'		=> array(
		'name'			=> 'Categories',
		'url'			=> '/administration/qacategories/',
		'buttons'		=> array('Add new' => '/administration/qacategories/add')
	),
);
*/