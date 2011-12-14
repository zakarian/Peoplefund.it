<?php	
	if(isset($project->about) && $project->about)
		$addValue = $project->about;
	else if(isset($post['about']) && $post['about'])
		$addValue = $post['about'];
	else
		$addValue = '';		
?>
<fieldset>
	<label for="insert_outcome">
		<?php if(isset($errors['about']) && $errors['about']) { ?><b style="color: red;">*</b><?php } ?>
		About your project
	</label>
	<div class="clear10"></div>
	<small class="label">
		Explain your project - this text will be visible only on the project page
	</small>
	<div class="clear10"></div>
	<div id="about_edit">
		<textarea class="aboutMceEditor" id="about" name="about" style="width: 100%; height: 220px;"><?php echo $addValue ?></textarea>
		<div class="clear10"></div>
	</div>
	<div class="clear10"></div>
	<div class="clear10"></div>
</fieldset>	
<script>
	var local_ed;
	var spellTimer;
	
	$(document).ready(function() {
		
		$('textarea.aboutMceEditor').tinymce({
			script_url : '/js/site/tiny_mce/tiny_mce.js',
			theme : 'advanced',
			skin : 'o2k7',
			skin_variant : "silver",
			//max_chars : 500,
			spellchecker_rpc_url : "/js/tiny_mce/plugins/spellchecker/rpc.php",
			max_chars_indicator : "about_counter",
			plugins: "maxchars, safari, advimage, paste, spellchecker, table, widgets, contextmenu, paste, inlinepopups, emotions, ccSimpleUploader, embed, media",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align	: "left",
			theme_advanced_buttons1: "bold, italic, underline, forecolor, |, formatselect, |, justifyleft, justifycenter, justifyright, |, bullist, numlist, |, link, unlink, |, image, embed, |, removeformat, emotions",
			theme_advanced_buttons2: "",
			theme_advanced_buttons3: "",
			content_css: "/css/site/mce.css?<?php echo  rand() ?>",
			relative_urls : false,
			file_browser_callback: "ccSimpleUploader",
			//valid_elements: "iframe[*],small,object[*],embed[*],+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/-b[class|style],-em/-i[class|style],-u[class|style],#p[id|style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],#td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],-div[id|dir|class|align|style],-span[style|class|align],-h4[id|style|dir|class|align],dd[id|class|title|style|dir|lang],dl[id|class|title|style|dir|lang],dt[id|class|title|style|dir|lang]",
			
			paste_auto_cleanup_on_paste : true,
			paste_create_paragraphs : false, paste_create_linebreaks : false, paste_use_dialog : true, paste_convert_middot_lists : false, paste_unindented_list_class : "unindentedList", paste_convert_headers_to_strong : true, paste_remove_styles : false,
			
			setup: function(ed) {
				ed.onKeyUp.add(function(ed, e) {
					// console.debug('Key up event: ' + e.keyCode);

					clearTimeout(spellTimer);
					local_ed = ed;

					// Run 1.5 sec after the user stop writing...
					spellTimer = setTimeout("local_ed.execCommand('mceSpellCheckSilent')", 1500);
				});
				ed.onPaste.add( function(ed, e, o) {
					ed.execCommand('mcePasteText', true);
					return tinymce.dom.Event.cancel(e);
				});
			}
		});
	});
</script>