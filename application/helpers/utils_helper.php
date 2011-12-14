<?php

function remove_shown_projects($ids = array()) {
	if($ids)
		return ' p.idproject != ' . implode(' AND p.idproject != ', $ids) . ' AND ';
			
	return '';
}
	
//String truncate
function trunc($text, $characters) {
	if(strlen($text) > $characters)
		return substr($text, 0, $characters) . '...';
	else
		return $text;
}
	
//Check for admin session
function is_admin() {
	if(
		isset($_SESSION['admin']->iduser) 
		&& !empty($_SESSION['admin']->iduser) 
		&& $_SESSION['admin']->type = 'admin'
		&& $_SESSION['active']->type = 1
		&& $_SESSION['confirmed']->type = 1
	) return TRUE;
			
	return FALSE;
}
	
//Check for member session
function is_logged() {
	if(
		isset($_SESSION['user']['iduser']) 
	) return TRUE;
		
	return FALSE;
}
	
//Prepare link and return href
function prepare_link($content, $href = TRUE){
	if(!$content) return FALSE;
		
	$content = h($content);

	if($href){
		$secure = FALSE;
			
		if(preg_match('/^https/', $content)) $secure = 'yes';
		else if(preg_match('/^http/', $content)) $secure = 'no';
		else $secure = 'not_defined';
			
		if($secure == 'yes'){
			
			if(preg_match('/www./', $content)) {
				$content = str_replace("www.", "", $content);
				$content = str_replace("https://", "", $content);
				$content = "https://www.".$content;
			} else {
				$content = str_replace("www.", "", $content);
				$content = str_replace("https://", "", $content);
				$content = "https://".$content;
			}
				
		} else if($secure == 'no') {
		
			if(preg_match('/www./', $content)) {
				$content = str_replace("www.", "", $content);
				$content = str_replace("http://", "", $content);
				$content = "http://www.".$content;
			} else {
				$content = str_replace("www.", "", $content);
				$content = str_replace("http://", "", $content);
				$content = "http://".$content;
			}
				
		} else {
			
			$content = "http://".$content;
				
		}
			
	} else {
		$content = str_replace("www.", "", $content);
		$content = str_replace("http://", "", $content);
		$content = str_replace("https://", "", $content);
		$content = "www.".$content;
	}

	if(!preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i", $content)) return FALSE;
	return $content;
}
	
function url_2_link($str) {
	$pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
	return preg_replace($pattern,"\\1<a href=\"\\2\\3\" onclick=\"this.target='_blank';\">\\2\\3</a>\\4",$str);
}

// Check email
function check_email($email){				
	if((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) OR (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,10}|[0-9]{1,10})(\]?)$/', $email)))
		return TRUE;
	else return FALSE;
}
	
// Encode URL string
function encode_string($input, $padding = FALSE) {
	
	$map = array(
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
		'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
		'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
		'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
		'='
	);

    if(empty($input)) return "";
    $input = str_split($input);
    $binaryString = "";
    for($i = 0; $i < count($input); $i++) {
        $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
    }
    $fiveBitBinaryArray = str_split($binaryString, 5);
    $base32 = "";
    $i=0;
    while($i < count($fiveBitBinaryArray)) {   
        $base32 .= $map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
        $i++;
    }
    if($padding && ($x = strlen($binaryString) % 40) != 0) {
        if($x == 8) $base32 .= str_repeat($map[32], 6);
        else if($x == 16) $base32 .= str_repeat($map[32], 4);
        else if($x == 24) $base32 .= str_repeat($map[32], 3);
        else if($x == 32) $base32 .= $map[32];
    }
    return $base32;
}
   
// Dencode URL string
function decode_string($input) {
	
$flippedMap = array(
		'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4', 'F'=>'5', 'G'=>'6', 'H'=>'7',
		'I'=>'8', 'J'=>'9', 'K'=>'10', 'L'=>'11', 'M'=>'12', 'N'=>'13', 'O'=>'14', 'P'=>'15',
		'Q'=>'16', 'R'=>'17', 'S'=>'18', 'T'=>'19', 'U'=>'20', 'V'=>'21', 'W'=>'22', 'X'=>'23',
		'Y'=>'24', 'Z'=>'25', '2'=>'26', '3'=>'27', '4'=>'28', '5'=>'29', '6'=>'30', '7'=>'31'
	);
		
	$map = array(
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
		'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
		'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
		'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
		'='
	);
		
    if(empty($input)) return;
    $paddingCharCount = substr_count($input, $map[32]);
    $allowedValues = array(6,4,3,1,0);
    if(!in_array($paddingCharCount, $allowedValues)) return FALSE;
    for($i=0; $i<4; $i++){
        if($paddingCharCount == $allowedValues[$i] &&
            substr($input, -($allowedValues[$i])) != str_repeat($map[32], $allowedValues[$i])) return FALSE;
    }
    $input = str_replace('=','', $input);
    $input = str_split($input);
    $binaryString = "";
    for($i=0; $i < count($input); $i = $i+8) {
        $x = "";
        if(!in_array($input[$i], $map)) return FALSE;
        for($j=0; $j < 8; $j++) {
            @$x .= str_pad(base_convert($flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
        }
        $eightBits = str_split($x, 8);
        for($z = 0; $z < count($eightBits); $z++) {
            $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) OR ord($y) == 48 ) ? $y:"";
		}
    }
    return $binaryString;
}
	
// Makes url slugs
function slugify($t) {
	$t = str_replace('а', 'a', $t);	$t = str_replace('б', 'b', $t);	$t = str_replace('в', 'v', $t);	$t = str_replace('г', 'g', $t);	$t = str_replace('д', 'd', $t);	$t = str_replace('е', 'e', $t);	$t = str_replace('ж', 'j', $t);	$t = str_replace('з', 'z', $t);	$t = str_replace('и', 'i', $t);	$t = str_replace('й', 'i', $t);	$t = str_replace('к', 'k', $t);	$t = str_replace('л', 'l', $t);	$t = str_replace('м', 'm', $t);	$t = str_replace('н', 'n', $t);	$t = str_replace('о', 'o', $t);	$t = str_replace('п', 'p', $t);	$t = str_replace('р', 'r', $t);	$t = str_replace('с', 's', $t);	$t = str_replace('т', 't', $t);	$t = str_replace('у', 'u', $t);	$t = str_replace('ф', 'f', $t);	$t = str_replace('х', 'h', $t);	$t = str_replace('ц', 'c', $t);	$t = str_replace('ш', 'sh', $t);	$t = str_replace('щ', 'sht', $t);	$t = str_replace('ь', 'i', $t);	$t = str_replace('ъ', 'y', $t);	$t = str_replace('ю', 'yu', $t);	$t = str_replace('я', 'ya', $t);
	$t = str_replace('А', 'a', $t);	$t = str_replace('Б', 'b', $t);	$t = str_replace('В', 'v', $t);	$t = str_replace('Г', 'g', $t);	$t = str_replace('Д', 'd', $t);	$t = str_replace('Е', 'e', $t);	$t = str_replace('Ж', 'j', $t);	$t = str_replace('З', 'z', $t);	$t = str_replace('И', 'i', $t);	$t = str_replace('Й', 'i', $t);	$t = str_replace('К', 'k', $t);	$t = str_replace('Л', 'l', $t);	$t = str_replace('М', 'm', $t);	$t = str_replace('Н', 'n', $t);	$t = str_replace('О', 'o', $t);	$t = str_replace('П', 'p', $t);	$t = str_replace('Р', 'r', $t);	$t = str_replace('С', 's', $t);	$t = str_replace('Т', 't', $t);	$t = str_replace('У', 'u', $t);	$t = str_replace('Ф', 'f', $t);	$t = str_replace('Х', 'h', $t);	$t = str_replace('Ц', 'c', $t);	$t = str_replace('Ш', 'sh', $t);	$t = str_replace('Щ', 'sht', $t);	$t = str_replace('Ь', 'i', $t);	$t = str_replace('Ъ', 'y', $t);	$t = str_replace('Ю', 'yu', $t);	$t = str_replace('Я', 'ya', $t);
	$t = preg_replace('/[^a-zA-Z0-9\ _\-\.]/', "", $t);
	$t = preg_replace('/\s/', "-", $t);
	$t = preg_replace('/-+/', "-", $t);
	$t = preg_replace('/_+/', "_", $t);
	$t = strtolower($t);
	return $t;
}
	
// Send email
function send_mail($from, $to, $title, $text, $title_params = "", $text_params = ""){
	
	// Replace title params
	if(!empty($title_params)){
		foreach($title_params AS $k => $v){
			$title = str_replace($k, $v, $title);
		}
	}
	
	// Replace text params
	if(!empty($text_params)){
		foreach($text_params AS $k => $v){
			$text = str_replace($k, $v, $text);
		}
	}

	// Add email headers
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= "From: $from". "\r\n";
	$headers .= "Reply-To: $from" .  "\r\n";
 
	// Add email body and send email
	$message = "<HTML><BODY>".html_entity_decode($text)."</BODY></HTML>";  
	
	// If the message is sent
    if(@mail($to, '=?UTF-8?B?'.base64_encode($title).'?=', nl2br($message), $headers)){ 
		
		// Get email data
		$ci = &get_instance();
		$ci->load->model('Configuration_model', 'configuration');
		$notify_email = (array) reset($ci->configuration->get_configuration(array("name" => "notify_email")));
		$notify_email = reset($notify_email);

		// Send confirmation email
		mail($notify_email, '=?UTF-8?B?'.base64_encode($title).'?=', nl2br($message), $headers);
		
		return TRUE;
			
	// If the message is not sent
	} else {
		return FALSE;
	}
}
	
// Remove unwanted chars
function h($str) {
	return trim(htmlspecialchars($str));
}

// Clear text
function st($text, $args = array()) {
	if (is_array($text)) {
		return $text;
	}
	$text = trim($text);
	$args = join('', $args);
	return strip_tags($text, $args);
}
	
// Calculate distance between two positions (lat and lng for each one)
function distance_calculator($lat1 = 0, $lng1 = 0, $lat2 = 0, $lng2 = 0, $miles = FALSE) {
	$latDegr1 = $lat1 / 180 * pi();
	$lngDegr1 = $lng1 / 180 * pi();
		
	$latDegr2 = $lat2 / 180 * pi();
	$lngDegr2 = $lng2 / 180 * pi();
		
	$e = acos( sin( $latDegr1 ) * sin( $latDegr2 ) + cos( $latDegr1 ) * cos( $latDegr2 ) * cos( $lngDegr2 - $lngDegr1 ) );
	if( $miles ) return round( $e * 3963.191, 2);
	else return round( $e * 6378.137, 2 );
}
	
	
// Convert date in human readable format
function date_before($date){

	// Start and end date
	$then = strtotime($date);
	$now = time();
		
	// Calculate difference
	$difference = $now - $then;

	// Get periods lengths
	$periods = array(
		"31570560"		=>	array("one"	=>	"year", 		"many"	=>	"years"),
		"2630880"		=>	array("one"	=>	"month", 		"many"	=>	"months"),
		"604800"		=>	array("one"	=>	"week", 		"many"	=>	"weeks"),
		"86400"			=>	array("one"	=>	"day", 			"many"	=>	"days"),
		"3400"			=>	array("one"	=>	"hour", 		"many"	=>	"hours"),
		"60"			=>	array("one"	=>	"minute", 		"many"	=>	"minutes"),
		"1"				=>	array("one"	=>	"second", 		"many"	=>	"seconds")
	);
		
	// Returned string
	$output = "";
		
	// For each field
	foreach($periods AS $seconds => $names_array){
			
		// Get time difference for this field
		$result = $difference / $seconds;
			
		// If the result is more than one
		if($result > 1){
			
			// If the result is less than 2
			if($result < 2){
				//$output .= floor($result)." ".$names_array['one']." ";
				$output[] = floor($result)." ".$names_array['one']." ";
				
			// If the result is more than 2
			} else {
				//$output .= floor($result)." ".$names_array['many']." ";
				$output[] = floor($result)." ".$names_array['many']." ";
			}
				
			// Decrement difference with the taken time
			$difference = $difference - floor($result) * $seconds;
		}
	}
		
	// Check if we have data
	if(!empty($output[0])){
		if(!empty($output[1])){
			$output = $output[0]." and ".$output[1];
		} else {
			$output = $output[0];
		}
	} else {
		$output = "1 second";
	}
		
	// Return difference
	return $output;
}
	
	
// Make project thumb
function make_project_thumb($image){

	// Require PHPTHUMB class
	require_once('phpthumb.class.php');
	$phpThumb = new phpThumb();
		
	// Set thumb options
	$height = 130;
	$width = 211;
	$params = array('aoe' => 1, 'zc' => 1, 'w' => $width, 'h' => $height, 'config_output_format' => 'jpeg');
		
	// Make thumb and save it
	$phpThumb->resetObject();
	$phpThumb->setSourceFilename($image);

	foreach($params as $key => $value) 
		$phpThumb->setParameter($key, $value);
			
	if($phpThumb->GenerateThumbnail()) {
	
		// Get name and ext
		$last = end(explode("/", $image));
		$image_name = reset(explode(".", $last));
		
		$images_thumbs = SITE_DIR."public/uploads/projects/" . $image_name . "_211x130.jpg";
		$phpThumb->RenderToFile($images_thumbs);
	}
		
		
	// Set thumb options
	$height = 110;
	$width = 160;
	$params = array('aoe' => 1, 'zc' => 1, 'w' => $width, 'h' => $height, 'config_output_format' => 'jpeg');
		
	// Make thumb and save it
	$phpThumb->resetObject();
	$phpThumb->setSourceFilename($image);

	foreach($params as $key => $value) 
		$phpThumb->setParameter($key, $value);
			
	if($phpThumb->GenerateThumbnail()) {
	
		// Get name and ext
		$last = end(explode("/", $image));
		$image_name = reset(explode(".", $last));
			
		$images_thumbs = SITE_DIR."public/uploads/projects/" . $image_name . "_160x110.jpg";
		$phpThumb->RenderToFile($images_thumbs);
	}
}
	
	
// String to site convertion
function str_to_site($string){
	$string = str_replace("http://", "", $string);
	$string = str_replace("https://", "", $string);
	$string = str_replace("www.", "", $string);
	return "http://www.".$string;
}
	
	
// Make user thumb
function make_user_thumb($image){

	// Require PHPTHUMB class
	require_once('phpthumb.class.php');
	$phpThumb = new phpThumb();
		
	// Set thumb options
	$height = 40;
	$width = 40;
	$params = array('aoe' => 1, 'zc' => 1, 'w' => $width, 'h' => $height, 'config_output_format' => 'jpeg');
		
	// Make thumb and save it
	$phpThumb->resetObject();
	$phpThumb->setSourceFilename($image);

	foreach($params as $key => $value) 
		$phpThumb->setParameter($key, $value);
			
	if($phpThumb->GenerateThumbnail()) {
		
		// Get name and ext
		$last = end(explode("/", $image));
		$image_name = reset(explode(".", $last));
		
		$images_thumbs = SITE_DIR."public/uploads/users/" . $image_name . "_40x40.jpg";
		$phpThumb->RenderToFile($images_thumbs);
	}
		
		
	// Set thumb options
	$height = 150;
	$width = 217;
	$params = array('aoe' => 1, 'zc' => 1, 'w' => $width, 'h' => $height, 'config_output_format' => 'jpeg');
	
	// Make thumb and save it
	$phpThumb->resetObject();
	$phpThumb->setSourceFilename($image);

	foreach($params as $key => $value) 
		$phpThumb->setParameter($key, $value);
			
	if($phpThumb->GenerateThumbnail()) {
	
		// Get name and ext
		$last = end(explode("/", $image));
		$image_name = reset(explode(".", $last));
			
		$images_thumbs = SITE_DIR."public/uploads/users/" . $image_name . "_217x150.jpg";
		$phpThumb->RenderToFile($images_thumbs);
	}
		
		
	// Set thumb options
	$height = 150;
	$width = 150;
	$params = array('aoe' => 1, 'zc' => 1, 'w' => $width, 'h' => $height, 'config_output_format' => 'jpeg');
	
	// Make thumb and save it
	$phpThumb->resetObject();
	$phpThumb->setSourceFilename($image);

	foreach($params as $key => $value) 
		$phpThumb->setParameter($key, $value);
			
	if($phpThumb->GenerateThumbnail()) {
		
		// Get name and ext
		$last = end(explode("/", $image));
		$image_name = reset(explode(".", $last));
		
		$images_thumbs = SITE_DIR."public/uploads/users/" . $image_name . "_150x150.jpg";
		$phpThumb->RenderToFile($images_thumbs);
	}
}
	
/* End of file utils_helper.php */ 
/* Location: ./application/helpers/utils_helper.php */
?>