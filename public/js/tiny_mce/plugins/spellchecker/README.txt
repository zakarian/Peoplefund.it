	// How to include

	// Add some vars
	var local_ed;
	var spellTimer;
	
	$(document).ready( function() {

		$('textarea.mceEditor').tinymce({
			script_url : '/share/jscripts/site/tiny_mce/tiny_mce.js',
 
			theme : 'advanced',
			skin : 'o2k7',
			skin_variant : "silver",
			
			// Spell checker rpc
			spellchecker_rpc_url : "<?php WEBROOT ?>/share/jscripts/tiny_mce/plugins/spellchecker/rpc.php",
			// -- end
 
			// Include spellchecker plugin..
			plugins: "safari, advimage, table, widgets, contextmenu, paste, inlinepopups, emotions, ccSimpleUploader, embed, media, spellchecker",
 
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align	: "left",
			theme_advanced_buttons1: "bold, italic, underline, forecolor, |, justifyleft, justifycenter, justifyright, |, bullist, numlist, |, link, unlink, |, image, embed, |, removeformat, emotions, spellchecker",
			theme_advanced_buttons2: "",
			theme_advanced_buttons3: "",
			content_css: "/share/styles/admin/front-forums.css",
			relative_urls : false,
			file_browser_callback: "ccSimpleUploader",
			valid_elements: "small,iframe[*],object[*],embed[*],+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/-b[class|style],-em/-i[class|style],-u[class|style],#p[id|style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],#td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],-div[id|dir|class|align|style],-span[id|style|class|align],-h4[id|style|dir|class|align],dd[id|class|title|style|dir|lang],dl[id|class|title|style|dir|lang],dt[id|class|title|style|dir|lang]",
			paste_create_paragraphs : true, paste_create_linebreaks : false, paste_use_dialog : true, paste_auto_cleanup_on_paste : true, paste_convert_middot_lists : true, paste_unindented_list_class : "unindentedList", paste_convert_headers_to_strong : true, paste_remove_styles : true, paste_remove_spans: true,
			
			// If you want to check automaticly you need this code
			setup: function(ed) {
				ed.onKeyUp.add(function(ed, e) {
					// console.debug('Key up event: ' + e.keyCode);

					clearTimeout(spellTimer);
					local_ed = ed;

					// Run 1.5 sec after the user stop writing...
					spellTimer = setTimeout("local_ed.execCommand('mceSpellCheckSilent')", 1500);
				});
			}
		});
	});