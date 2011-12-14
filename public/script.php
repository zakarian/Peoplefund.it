<?php
ob_start ("ob_gzhandler");
header("Content-type: text/javascript; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60*60*4;
header("Expires: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT");

$files = array(
	'js/jquery/jquery-1.5.1.min.js',
	'js/jquery/jquery-ui-1.8.13.custom.min.js',
	'js/jquery/jquery.qtip.min.js',
	'js/site/fb_implement.js',
	'js/jquery/jquery.prettyPhoto.js',
	'js/site/site.js',
	'js/site/jquery.alerts.public.js',
	'js/jquery/marquee.js',
	'js/site/swfupload/fileprogress.js',
	'js/site/swfupload/handlers.js',
	'js/site/swfupload/swfupload.js',
	'js/site/swfupload/swfupload.js',
	'js/site/tiny_mce/jquery.tinymce.js'
);

foreach($files as $file) {
	if(file_exists($file))
		include($file);
}


?>