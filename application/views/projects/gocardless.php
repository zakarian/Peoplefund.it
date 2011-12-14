<div class="site-width">
	<div class="generic-form overflow left">
		<div class="steps">
			• <b>step 1</b> checklist &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>Step 2</b> project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span>• <b>step 3</b> account</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 4</b> submit
		</div>
		<div class="inner-form left">
			<form class="global_form_2" method="post" action="">
				<h2>GoCardless account set up</h2>
				<p>You need to set up an account now in order to receive money from backers. We will not publish your project until 
				you have an account, but it should take you less than 5 minutes to set up.</p>
				<p>Your account will be set up with GoCardless. They offer a secure, quick system with great rates - so more of every
				pledge goes directly to you. They use a one-off direct debit payment system, and the pledges will only be collected
				from you backers account if you raise 100+% of your funding target.</p>
				
				<?php if(!empty($errors)){ 
						foreach($errors AS $error){
						?>
							<div class="global-error">- <?php echo $error;?></div>
						<?php 					}
					echo '<div class="clear20"></div>';
				} ?>

				<?php if(!$project->merchant_id) { ?>
				<div>	
					<label for="merchant_id" style="width: auto;">
						<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
						<span id="gocardless-no"></span>
						<div id="gocardless-yes" class="hidden">
							You have successfully linked your GoCardless account
							<div class="clear10"></div>

							<input class="button left" name="submit" type="submit" value="Submit" style=""> 
							<input class="button left edit-later" name="save" type="submit" value="Save &amp; edit later">
						</div>
					</label>
					<div id="gocardless-no-block">
						<div class="clear10"></div>
						<input id="select-gocardless-account" class="button left" name="gocardless" type="button" value="Set up an account to receive funds"> 
						<input onclick="window.location = '/projects/my/'; return false;" class="button left edit-later" name="save" type="submit" value="Save &amp; edit later">
					</div>
					<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
					<input id="access_token" type="hidden" name="access_token" value="<?php echo (int) $project->access_token ?>">
				</div>	
				<?php } else { ?>
				<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
				<div>	
					<label for="merchant_id" style="width: auto;">
						<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
						You have successfully linked your GoCardless account
					</label>
					<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
					
					<input class="button left" name="submit" type="submit" value="Finish" style="">
					<input class="button left edit-later" name="save" type="submit" value="Save &amp; edit later">
				</div>	
				<?php } ?> 

				<div class="clear"></div>
			</form>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php

	$gocardlessMerchantTitle 	= '';	
	$gocardlessFirstName 		= '';	
	$gocardlessLastName 		= '';	
	$gocardlessEmail 			= '';	
	
	if(isset($project->title) && $project->title) $gocardlessMerchantTitle = h(st($project->title));
		
	$name					= explode(' ', !empty($_SESSION['user']['name']) ? $_SESSION['user']['name'] : $_SESSION['user']['username']);
	$gocardlessFirstName 	= array_shift($name);
	$gocardlessLastName 	= join(' ', $name);
	$gocardlessEmail		= $_SESSION['user']['email']; 
	
?>
<form id="add-gocardless-account" method="post" action="<?php echo $gocardless['oauth_authorize_url'] ?>" class="hidden">
	<div>
		<input type="hidden" name="client_id" value="<?php echo $gocardless['app_id'] ?>">
		<input type="hidden" name="redirect_uri" value="<?php echo $gocardless['redirect_uri'] ?>">
		<input type="hidden" name="response_type" value="<?php echo $gocardless['response_type'] ?>">
		<input type="hidden" name="scope" value="<?php echo $gocardless['scope'] ?>">
		<input type="hidden" name="merchant[name]" value="<?php echo $gocardlessMerchantTitle ?>">
		<input type="hidden" name="merchant[user][email]" value="<?php echo $gocardlessEmail ?>">
		<input type="hidden" name="merchant[user][first_name]" value="<?php echo $gocardlessFirstName ?>">
		<input type="hidden" name="merchant[user][last_name]" value="<?php echo $gocardlessLastName ?>">
	</div>
</form>

<script>
	$(document).ready(function(){
		$('.global_form_2 div.pledge select.pledge').change(function(){
			if($(this).val() == 'yes') 
				$(this).parent().find('.n_reward_l').show();
			else
				$(this).parent().find('.n_reward_l').hide();
		});
		$('#select-gocardless-account').click( function() {
			$('#add-gocardless-account').get(0).setAttribute('target', 'gocardless');
			
			window.open('about:blank', 'gocardless', 'scrollbars=yes,menubar=no,height=600,width=1024,resizable=yes,toolbar=no,status=no');
			
			$('#add-gocardless-account').submit();

			return false;
		});
	});
	function addAmount(){
		var cnt = parseInt($("#amountsCount").val());
		if(cnt > 8) return false;
		cnt = cnt + 1;
		$("#amountsCount").val(cnt);
		
		var html = '';
		html += '<div class="pledge focus-style" id="amount_'+cnt+'" style="margin-top: 10px;">';
		html += '<span class="title">REWARD </span>';
		html += '<div class="clear10"></div>';
		html += '<div style="line-height: normal;" class="label">Pledge amount <small>(You can only enter numbers)</small></div><input maxlength="6" class="pledge focus-style" name="amounts['+cnt+']" value="" /><div class="clear10"></div>';
		html += '<div style="line-height: normal;" class="label">Are there a limited number<br /> of this reward available?</div>';
		html += '<select name="amounts_limited['+cnt+']" class="pledge focus-style">';
		html += '<option value="no">No</option>';
		html += '<option value="yes">Yes</option>';
		html += '</select>';
		html += '<div class="n_reward_l">';
		html += '<div style="width: 70px;" class="label">Number</div>';
		html += '<input class="pledge_small focus-style" name="amounts_numbers['+cnt+'] "value="" />';
		html += '</div>';
		html += '<div class="clear10"></div>';
		html += '<div class="label">Description of reward</div>';
		html += '<textarea class="pledge focus-style" name="amounts_descriptions['+cnt+']"></textarea>';
		html += '<a href="javascript:;" onClick="removeAmount('+cnt+');" style="margin-left: 210px; padding-top: 5px; float: left;">Remove reward</a>';
		html += '<div class="clear10"></div>';
		html += '</div>';
			
		$("#amount_options_container").append(html);
		
		$('.global_form_2 div.pledge select.pledge').change(function(){
			if($(this).val() == 'yes') 
				$(this).parent().find('.n_reward_l').show();
			else
				$(this).parent().find('.n_reward_l').hide();
		});
	}
	
	function removeAmount(id){
		$("#amount_"+id).remove();
	}
	function removeAddedAmount(id){
		$("#added_amount_"+id).hide();
		$("#added_delete_"+id).val("1");
	}

	// Check if postcode is valid
	function checkPostcode(){
		$.post('/user/get_location_by_postcode/', {postcode: $("#postcode").val()}, function(data) {
			if($.trim(data) != ","){
				$("#postcode_message").html("<font color='green'>The postcode is OK</font>");
				$("#postcode_location").html($.trim(data));
			} else {
				$("#postcode_message").html("<font color='red'>The postcode is not found</font>");
				$("#postcode_location").html("");
			}
		});
	}

	// Add website
	function add_website(){
		var site = $("#add_site").val();
		if(site == "http://") return;
		
		if (site.substr(0, 7) != 'http://' && site.substr(0, 8) != 'https://'){
			site = 'http://' + site;
		}		
		
		var html;
		html = "<div id='"+Number(new Date())+"' style='margin-top: 10px;'>";
		html += "<input type='text' class='query' style='width: 300px; float: left;' name='websites[]' value='"+site+"'>";
		html += "&nbsp;<a href='javascript:;' style='text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;' title='Remove' onClick=\"removeWebsite('"+Number(new Date())+"');\">&nbsp;<img src='/img/site/delete_16.png'></a><br clear='all'>";
		html += "</div>";
		
		$("#websites").append(html);
		$("#add_site").val("http://");
	}
	
	// Remove website
	function removeWebsite(site){
		$("#"+site).remove();
	}
	
	// Edit field
	function editField(field, show){
		if(show == "show"){
			$("#"+field+"_preview").hide();
			$("#"+field+"_edit").show();
		} else {
		
			if(field == "slug"){
				$("#"+field+"_preview").html("<small>"+ "http://peoplefund.it/"+$("#"+field).val() +"</small>");
			} else if(field == "period"){
				var weeks = "";
				if($("#"+field).val() != 1){
					weeks = "s";
				}
				$("#"+field+"_preview").html("<small>"+ $("#"+field).val() +" Week"+weeks+"</small>");
			} else if(field == "pledge_more"){
				if($("#pledge_more_yes").is(":checked")){
					$("#"+field+"_preview").html("<small>Yes</small>");
				} else {
					$("#"+field+"_preview").html("<small>No</small>");
				}
			} else if(field == "helpers"){
				if($("#helpers_yes").is(":checked")){
					$("#"+field+"_preview").html("<small>Yes</small>");
				} else {
					$("#"+field+"_preview").html("<small>No</small>");
				}
			} else if(field == "category"){
				
				var checkBoxes = $(".categories_checkbox");
				var checked = new Array();

				$.each(checkBoxes, function() {
					if ($(this).attr('checked')){
						checked.push(this.value);
					}
				});
				
				$.post('/projects/get_categories_names_by_ids/', {ids: checked}, function(data) {
					if($.trim(data)){
						$("#"+field+"_preview").html("<small>"+data+"</small>");
					}
				});
	
			
			} else {
				$("#"+field+"_preview").html("<small>"+ $("#"+field).val() +"</small>");
			}
			
			$("#"+field+"_preview").show();
			$("#"+field+"_edit").hide();
		}
	}
	
	// Add some vars
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