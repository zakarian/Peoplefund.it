<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Administration - Peoplefund</title>

	<!-- META data -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="copyright" content="" />
	<meta name="description" content="CMS-MVC Administrator section" />
	<meta name="keywords" content="" />
	<meta http-equiv="pragma" content="no-cache" />
	<!-- end of META data -->

	<!-- CSS Administration main styles -->
	<link type="text/css" rel="stylesheet" href="<?php echo site_url('css/admin/main.css'); ?>" />

	<!--[if IE]> <link rel="stylesheet" href="<?php echo site_url('css/admin/main.ie.css'); ?>" type="text/css" /> <![endif]-->
	<!--[if IE 6]> <link rel="stylesheet" href="<?php echo site_url('css/admin/main.old.ie.css'); ?>" type="text/css" /> <![endif]-->

	<!-- end of CSS Administration styles -->

	<!-- CSS Administration jquery styles -->
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/jquery/jquery.alerts.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/jquery/jquery.calendar.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/jquery/jquery.lightbox.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/jquery/jquery.autocomplete.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/jquery/jquery-ui-1.8.13.custom.css'); ?>">
	<!-- end of CSS Administration jquery styles -->

	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery-1.5.1.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.dom.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery-ui-1.8.13.custom.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/interface.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.drag.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.jform.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.alerts.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.corners.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.checkbox.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.lightbox.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.maskedinput.js'); ?>"></script>  
	<script type="text/javascript" src="<?php echo site_url('js/admin/ajax.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/admin/admin.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo site_url('js/common/functions.js'); ?>"></script>

	<?php if(!empty($tinyMCE)){ ?>
			<script type="text/javascript" src="<?php echo site_url('js/jquery/jquery.tinymce.js');?>"></script>
			<script type="text/javascript">
				$(document).ready(function() {
					$('textarea.mceEditor').tinymce({
						script_url : '<?php echo site_url('js/tiny_mce/tiny_mce.js'); ?>',
						theme : 'advanced',
						skin : 'o2k7',
						skin_variant : "silver",
						plugins: "safari, advimage, table, widgets, contextmenu, media, paste, inlinepopups, fullscreen",
						file_browser_callback : "filebrowserCallback",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align	: "left",
						theme_advanced_buttons1: "undo, redo, |, bold, italic, underline, |, justifyleft, justifycenter, justifyright, , styleselect, formatselect, , removeformat, charmap, visualaid, |, fullscreen",
						theme_advanced_buttons2: "bullist, numlist, |, link, unlink, anchor, |, image, media, |, hr, table, code",
						theme_advanced_buttons3: "",
						theme_advanced_buttons2_add: "|, pastetext, pasteword",  
						content_css: "/css/admin/mce.css",
						external_link_list_url : "/administration/list/" + '/pos:main',
						convert_urls: false,
						relative_urls : false,
						extended_valid_elements: "small[class],code,label[for|class],form[name|id|action|method|enctype|accept-charset|onsubmit|onreset|target|style|class|summary],input[id|name|type|value|size|maxlength|checked|accept|src|width|height|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|required|style|class|summary],textarea[id|name|rows|cols|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|required|style|class|summary],option[name|id|value|selected|style|class|summary],select[id|name|type|value|size|maxlength|checked|accept|src|width|height|disabled|readonly|tabindex|accesskey|onfocus|onblur|onchange|onselect|onclick|length|options|selectedIndex|required|style|class|summary]",
						paste_create_paragraphs : false, paste_create_linebreaks : false, paste_use_dialog : true, paste_auto_cleanup_on_paste : true, paste_convert_middot_lists : false, paste_unindented_list_class : "unindentedList", paste_convert_headers_to_strong : true, paste_remove_styles : false
					});
				});
				
				function popup(where, type) {

					$(".assets").html('<fieldset><img src="/img/buttonClose.png" alt="Close" title="Close" border="0" class="xcancel" onclick="$(\'.assets\').fadeOut();" /><iframe src="/administration/assets/docs/'+where+'/'+type+'" width="700" height="400" frameborder="0" scrolling="auto"></iframe></fieldset>');
					
					$('.assets fieldset').corner();

					$('.assets').fadeIn();
				}
				
				function filebrowserCallback( where, url, type, win ) {
					switch( type ) {
						case 'image': 
							type = 'images';
							break;
						case 'flash': 
							break;
						case 'quick': 
							type = 'videos';
							break;
						default: 
							type = '';
							break;
					}

					$(".assets").html('<fieldset><img src="/img/buttonClose.png" alt="Close" title="Close" border="0" class="xcancel" onclick="$(\'.assets\').fadeOut();" /><iframe src="/administration/assets/docs/'+where+'/'+type+'" width="700" height="400" frameborder="0" scrolling="auto"></iframe></fieldset>').focus();
					
					$(".assets").unbind( 'focus' );
					$(".assets").focus( function() {
						$(this).css({'z-index': ( parseFloat( maxZIndex() ) + 10 )});
					});
					
					$('.assets fieldset').corner();

					$('.assets').focus().show();
				}
			</script>
	<?php } ?>

</head>
<body>
	<div id="loading">loading</div>

	<div id="top">
		<h1>ADMINISTRATION</h1>
		<a href="<?php echo site_url('administration/logout'); ?>">LOGOUT</a>
	</div>

	<div id="wrap">

<?php require('navigation_menu.php') ?>