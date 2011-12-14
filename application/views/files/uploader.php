<head>
	<title>File Uploader</title>	
	<script type="text/javascript" src="/js/jquery/jquery-1.5.1.min.js" ></script>
	<script type="text/javascript" src="/js/site/tiny_mce/tiny_mce_popup.js" ></script>
	<script type="text/javascript" src="/js/site/tiny_mce/plugins/ccSimpleUploader/editor_plugin.js" ></script>	
	<script type="text/javascript" src="/js/jquery/jquery.file.js" ></script>
	<base target="_self" />
</head>	
<body class="uploader">
<?php
	$action			= isset($_GET["q"]) ? $_GET["q"] : "none";						// no action by default
	$upload_dir		= isset($_GET["d"]) ? $_GET["d"] : "./";						// same directory by default
	$upload_dir		= "/events_reports";									// same directory by default
	$file_type		= isset($_GET["type"]) ? $_GET["type"] : "unknown";				// when called from tinyMCE advimg/advlink this will be set and can be used for filtering..
	$substitute_dir = "/events_reports";											// same substitution directory by default
	$ResultTargetID = isset($_GET["id"]) ? $_GET["id"] : "src";						// (obsolete) - target ID now is provided by the tinyMCE framework	
	
	if(strtolower($_SERVER['REQUEST_METHOD']) != "post"){
		display_upload_form();	
	} else {	
		upload_content_file($upload_dir, $substitute_dir);
	}
?>
</body>
</html>

<?php

// Displays the upload form
function display_upload_form() {
	?>
		<form action="" method="post" enctype="multipart/form-data" onsubmit="" class="form">
			<fieldset>

				<div class="row" style="height:30px;" align="center">
					<label>File:</label>
					<input type="file" name="upload_file">
				</div>

				<div class="row buttons" style="height:30px;" align="center">
					<input type="submit" name="Upload" value="Upload" class="btn btn-submit btn-right submit" onclick="document.getElementById('progress_div').style.visibility='visible';">
				</div>

				<input type="hidden" name="action" value="uploader">
	
			</fieldset>
		</form>

		<div class="progress" id="progress_div" style="visibility: hidden;"></div>
		<script>
			$( function() {
				$("input[type='file']").filestyle({ 
					image: "/img/browse.png",
					imageheight : 22,
					imagewidth : 81,
					width : 240
				});
			});
		</script>

	<?php
}
// Uploads the file to the destination path, and returns a link with link path substituted for destination path
function upload_content_file($DestPath, $DestLinkPath) {

	$StatusMessage = "";
	$ActualFileName = "";	
	
	$file = &$_FILES['upload_file'];

	if( isset( $file['error'] ) && !empty( $file['error'] ) ) {
		switch($file['error']) {
			case 1  :   $file['error'] = 'File exceeds ini limit!';
				break;
			case 3  :   $file['error'] = 'File partially uploaded!';
				break;
			case 4  :   $file['error'] = 'No file selected!';
				break;
			default :   $file['error'] = 'File upload error!';
				break;
		}

		$StatusMessage = $file['error'];
	} else {	

		$s = array( '?', '|', '#', '!', '@', '$', '%', '^', '*', '>', '<', '\\' );
		
		$file['name'] = str_replace( $s, '', $file['name'] );
		
		$s = array('&', ' ', ',');
		
		$file['name'] = str_replace( $s, '-', $file['name'] );

		$type = 'misc';

		preg_match( '/(png|jpeg|jpg|gif)$/i', $file['name'] ) and $type = 'image';

		$size = getimagesize( $file['tmp_name'] );

		if( $type == 'misc' OR !$size ) {
			$StatusMessage = 'The file must be a valid image';

			if( file_exists( $file['tmp_name'] ) )
				unlink( $file['tmp_name'] );
		} else {
			$upload_path = SITE_DIR."public/uploads/projects/";
			
			$file['name'] = time() . '-' . $file['name'];

			$dest = $upload_path . $file['name'];
			
			if( !move_uploaded_file( $file['tmp_name'], $dest ) )
				$StatusMessage = 'failed moving file';
			else {
				$StatusMessage .=  $file['name'] . ' has been successfully uploaded!';
				$ActualFileName = "/uploads/projects/" . $file['name'];
				
				require_once(APPPATH.'/helpers/phpthumb.class.php');

				$phpThumb = new phpThumb();
				$phpThumb->config_allow_src_above_docroot = TRUE;

				$phpThumb->setSourceFilename($dest);
				$phpThumb->setParameter('q', 95);
				$phpThumb->setParameter('w', 500);
				//$phpThumb->setParameter('h', 62);
				//$phpThumb->setParameter('zc', 1);
				$phpThumb->setParameter('config_output_format', 'jpeg');

				$output_filename = APPPATH.$ActualFileName;
				if ($phpThumb->GenerateThumbnail()) {
					$output_size_x = @ImageSX($phpThumb->gdimg_output);
					$output_size_y = @ImageSY($phpThumb->gdimg_output);

					if ($phpThumb->RenderToFile($output_filename)) {
						//$_POST['thumb'] = '/var'.$_POST['cwd'].$upname.'-sm.'.$phpThumb->config_output_format;
					}
				}
			}
		}
	}	

	if( !empty( $StatusMessage ) )
		ShowPopUp( $StatusMessage );																			// show the message to the user	

	CloseWindow( $ResultTargetID, $ActualFileName );
}

function ShowPopUp($PopupText)
{
	echo "<script type=\"text/javascript\" language=\"javascript\">alert (\"$PopupText\");</script>";
}

function CloseWindow($FocusItemID, $FocusItemValue)
{
	?>
		<script language="javascript" type="text/javascript">	
			ClosePluginPopup('<?php echo $FocusItemValue; ?>');
		</script>
	<?php
}
?>

