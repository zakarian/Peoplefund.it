<?php
ob_start ("ob_gzhandler");
ob_start("compress");
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60*60*4;
header("Expires: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s",time() + $offset) . " GMT");
function compress($buffer) {
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);  
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);  
    $buffer = str_replace('{ ', '{', $buffer);
    $buffer = str_replace(' }', '}', $buffer);
    $buffer = str_replace('; ', ';', $buffer);
    $buffer = str_replace(', ', ',', $buffer);
    $buffer = str_replace(' {', '{', $buffer);
    $buffer = str_replace('} ', '}', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(' ,', ',', $buffer);
    $buffer = str_replace(' ;', ';', $buffer);
	return $buffer;
}

	$files = array(
		'css/site/styles.css',
		'css/site/jquery.alerts.public.css',
		'css/site/swfupload.css',
		'css/site/popup.css'
	);
	foreach($files as $file) {
			if(file_exists($file))
				include($file);
	}

?>