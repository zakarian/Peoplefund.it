<div class="site-width">
	<div class="generic-form">
			
			
		<script src="/js/jquery/jquery.form.js"></script>	
		<form id="upload-form-js" method="POST" action="/user/add_image/" enctype="multipart/form-data">
			<input type="file" id="browse-file" name="File" style="position: absolute; top: -9999px; left: -9999px; z-index: 1;">
			<input type="text" id="fake-file" style="display: none;" value="">
		</form>
			
	
		<form class="global_form_1" method="post" action="">
			<h1>Your profile Settings</h1>
			<?php
			if(!empty($errors)){
				foreach($errors AS $error){
					echo '<span class="global_error">'.$error.'</span>';
				}
				echo '<div class="clear10"></div>';
			}
			if(!empty($success)){
				echo '<span class="global_success">'.$success.'</span>';
				echo '<div class="clear10"></div>';
			}
			?>
			<?php if ($data['confirmed'] == '0'){ ?>
			<fieldset>
				<label for="confirm_profile">Confirm profile</label>
				Your profile is still not confirmed, and you are currently not allowed to add new projects. You can confirm it by resending verification link to your email from 
				<a href="/user/resend_verification/" title="Confirm profile">here</a>
			</fieldset>
			<?php } ?>
			<fieldset>
				<label for="update_name">Full name</label>
				<input autocomplete="off" class="query focus-style" id="update_name" type="text" name="name" value="<?php echo h(st($data['name'])) ?>" />
			</fieldset>	
			<fieldset>
				<label class="small" for="update_username">*Username<small>This will be your display name</small></label>
				<input autocomplete="off" class="query focus-style" id="update_username" type="text" name="username" value="<?php echo h(st($data['username'])) ?>" />
			</fieldset>
			<fieldset>
				<label class="small" for="update_bio">A bio - Explain who you are<small>(<span id="characters_counter">300</span> characters remaining)</small></label>
				<textarea class="query focus-style" id="update_bio" name="bio" onKeyUp="limitTextArea('update_bio', 'characters_counter', 300);" onKeyDown="limitTextArea('update_bio', 'characters_counter', 300);"><?php echo h(st($data['bio'])) ?></textarea>
			</fieldset>	
			<fieldset>
				<label for="">*Profile Picture</label>	
				<div id="preview">
					<?php if(!empty($_SESSION['user']['ext'])){ ?>
						<img width="220" src="/uploads/users/<?php echo h(st($_SESSION['user']['iduser'])) ?>.<?php echo h(st($_SESSION['user']['ext'])) ?>">
						<a class="link" href='javascript:;' onClick='remove_image();'>Remove my image</a>
					<?php } ?>
				</div>
				<div class="clear"></div>					
		
				
				<script type="text/javascript">
					function remove_image(){
						$.post('/user/remove_image/', function(data) {
							$("#preview").html("");
						});
					}
					function fillLocationByPostcode(){
						$.post('/user/get_location_by_postcode/', {postcode: $("#update_postcode").val()}, function(data) {
							$("#update_location").val(data);
						});
					}
					
					function addWebsiteRecord(){
						$.post('/user/add_website/', {website: $("#update_websites").val()}, function(data) {
							$("#update_websites_list").html(data);
						});
					}
					
					function deleteWebsiteRecord(website){
						$.post('/user/delete_website/', {website: website}, function(data) {
							$("#update_websites_list").html(data);
						});
					}
				</script>
			</fieldset>	
			<fieldset>
				<div id="uploader_div">
					<label for="">&nbsp;</label>
						
					<!-- <input id="file-upload-button" type="button" value="Upload" class="button" style="float: none;"> -->
					
					<input id="browse-button" type="button"  value="Browse Computer" class="button" style="float: none;">
					<script type="text/javascript">
						$(document).ready( function() {
							$('#browse-button').click(function(){
								$('#browse-file').click();
							});
						
							$('#browse-file').change( function() {
								$('#fake-file').val( $( this ).val() );
								//$('#file-upload-button').click();
								$('form#upload-form-js').submit();
								return false;
							});
						
							$('#file-upload-button').click(function(){
								$('form#upload-form-js').submit();
							});
					
							$('form#upload-form-js').submit( function() {
								if( $( '#fake-file' ).val().trim() == '' ) {
									$( '#error-message' ).show();
									return false;
								}
								$( '#error-message' ).hide();
								
								var postFormOptions = {					
										success : function( data ){
											if( data.success ) {
												var new_image_url = data.html;
												var new_html = '<img width="220" src="/uploads/users/'+new_image_url+'">';
												new_html += ' <a class="link" href="javascript:;" onClick="remove_image();">Remove my image</a>';
												
												$('#preview').html(new_html);
											} else {
												$('#error-message').html(data.msg).show();
											}
										},
										error : function( data, text, err ) {
											console.log('Error - '+err);
											console.log(text);
										},
										iframe		: true,
										dataType	: 'json'
								}

								$(this).ajaxSubmit( postFormOptions ); 

								return false;
							});
							
							// Safari hack - This browser did not detect onfile select event.
							if($.browser.safari)
								setInterval("$('#fake-file').val($('#browse-file').val());", 250);
						});			
					</script>					
				</div>	
				<?/*<div class="fieldset flash" id="fsUploadProgress"></div>*/?>
			</fieldset>				
			
			<fieldset>
				<label class="small" for="update_email">*EMAIL<small>not publically visible</small></label>
				<input autocomplete="off" class="query focus-style" id="update_email" type="text" name="email" value="<?php echo h(st($data['email'])) ?>" />
			</fieldset>
			<fieldset>
				<?php if((int) $data['fbid'] == 0){ ?>
					<label class="small" for="">CONNECT YOUR ACCOUNT<br /> WITH FACEBOOK</label>
					<a href="javascript:;" id="fb-add-connect"><img border="0" alt="Connect with Facebook" src="/img/site/facebook.gif"></a>
				<?php } else { ?>
					<label class="small" for="">DISCONNECT YOUR ACCOUNT<br /> FROM FACEBOOK</label>
					<a class="link" href="javascript:;" id="fb-disconnect">Disconnect your facebook profile</a>
				<?php } ?>
			</fieldset>
			<fieldset>
				<?php if((int) $data['esid'] == 0){ ?>
					<label class="small" for="">CONNECT YOUR ACCOUNT<br /> WITH ENERGYSHARE</label>
					<a class="link" href="/connect/to/service:energyshare/">Connect your energyshare profile</a>
				<?php } else { ?>
					<label class="small" for="">DISCONNECT YOUR ACCOUNT<br /> FROM ENERGYSHARE</label>
					<a class="link" href="/connect/remove/service:energyshare/">Disconnect your energyshare profile</a>
				<?php } ?>
			</fieldset>
			<fieldset>
				<label for="update_old_password">Old Password</label>
				<input autocomplete="off" class="query focus-style" id="update_old_password" type="password" name="old_password" value="" />
			</fieldset>	
			<fieldset>
				<label for="update_password">New Password</label>
				<input autocomplete="off" class="query focus-style" id="update_password" type="password" name="password" value="" />
			</fieldset>	
			<fieldset>
				<label for="update_password_repeat">Confirm New password</label>
				<input autocomplete="off" class="query focus-style" id="update_password_repeat" type="password" name="password_repeat" value="" />
			</fieldset>	
			<fieldset>
				<label class="small" for="update_slug">URL<small>once set you can not change this again</small></label>
				<input autocomplete="off" class="query focus-style" id="update_slug" type="text" name="slug" value="<?php echo h(st($data['slug'])) ?>" />
			</fieldset>	
			<fieldset>
				<label for="update_postcode">POSTCODE</label>
				<input autocomplete="off" class="query focus-style" id="update_postcode" type="text" name="postcode" value="<?php echo h(st($data['postcode'])) ?>" />
				<input class="check_button" type="button" onClick="fillLocationByPostcode();" value="Verify" />
			</fieldset>	
			<fieldset>
				<label class="small" for="update_location">LOCATION<small>town, county</small></label>
				<input autocomplete="off" class="query focus-style" id="update_location" type="text" name="location" value="<?php echo h(st($data['location'])) ?>" />
			</fieldset>	
			<fieldset>
				<label class="small" for="update_websites">Websites<small>let people know a bit more about you</small></label>
				<input autocomplete="off" class="query focus-style" id="update_websites" type="text" name="websites" value="http://" />
				<input class="check_button" type="button" onClick="javascript:addWebsiteRecord();" value="Add" />
			</fieldset>
			<fieldset>
				<div class="profile-websites-list" id="update_websites_list">
					<?php 
						if (!empty($data['websites_arr']))
							foreach($data['websites_arr'] as $i => $website){ ?>
						<a class="delete_website" href="javascript:deleteWebsiteRecord('<?php echo $website; ?>');">Delete</a> | <a href="<?php echo $website; ?>" target="_blank"><?php echo $website; ?></a><br />
					<?php } ?>
				</div>
				<div class="clear"></div>
			</fieldset>	
			<div class="clear10"></div>
			<h1>Areas of interest</h1>
			<fieldset>
				<label for="">Interests
					<small style="line-height: normal;">please tick which categories your<br />
					are interested in (the site will use<br />
					this to help show you the projects<br />
					you will be most interested in)</small>
				</label>
				<?php $data['interests'] = ((isset($data['interests']) && $data['interests'])) ? unserialize($data['interests']) : array() ?>
				<div class="left" style="width: 20%;">
					<div class="clear10"></div>
					<?php foreach($categories as $k=>$category) { ?>
						<?php if($k%2 == 0) continue; $checked = (in_array($category->idcategory, $data['interests'])) ? TRUE : FALSE; ?>
						<input<?php echo ($checked) ? ' checked' : '' ?> type="checkbox" name="interests[]" class="" value="<?php echo h(st($category->idcategory)) ?>"> <?php echo h(st($category->title)) ?>
						<div class="clear5"></div>
					<?php } ?>
				</div>
				<div class="left" style="width: 20%;">
					<div class="clear10"></div>
					<?php foreach($categories as $k=>$category) { ?>
						<?php if($k%2 <> 0) continue; $checked = (in_array($category->idcategory, $data['interests'])) ? TRUE : FALSE; ?>
						<input<?php echo ($checked) ? ' checked' : '' ?> type="checkbox" name="interests[]" class="" value="<?php echo h(st($category->idcategory)) ?>"> <?php echo h(st($category->title)) ?>
						<div class="clear5"></div>
					<?php } ?>
				</div>
			</fieldset>	
			<div class="clear10"></div>
			<h1>email alerts</h1>
			<fieldset>
				<label for="update_alerts_own">Email Alerts for projects I own</label>
				<select id="update_alerts_own" class="query focus-style" name="alerts_own">
					<option value="instant"<?php if($data['alerts_own'] == "instant"){ ?> selected<?php } ?>>Instant</option>
					<option value="daily"<?php if($data['alerts_own'] == "daily"){ ?> selected<?php } ?>>Daily</option>
					<option value="weekly"<?php if($data['alerts_own'] == "weekly"){ ?> selected<?php } ?>>Weekly</option>
					<option value="monthly"<?php if($data['alerts_own'] == "monthly"){ ?> selected<?php } ?>>Monthly</option>
					<option value="never"<?php if($data['alerts_own'] == "never"){ ?> selected<?php } ?>>Never</option>
				</select>
			</fieldset>	
			<fieldset>
				<label for="update_alerts_backing">Email Alerts for projects I'm backing</label>
				<select id="update_alerts_backing" class="query focus-style" name="alerts_backing">
					<option value="instant"<?php if($data['alerts_backing'] == "instant"){ ?> selected<?php } ?>>Instant</option>
					<option value="daily"<?php if($data['alerts_backing'] == "daily"){ ?> selected<?php } ?>>Daily</option>
					<option value="weekly"<?php if($data['alerts_backing'] == "weekly"){ ?> selected<?php } ?>>Weekly</option>
					<option value="monthly"<?php if($data['alerts_backing'] == "monthly"){ ?> selected<?php } ?>>Monthly</option>
					<option value="never"<?php if($data['alerts_backing'] == "never"){ ?> selected<?php } ?>>Never</option>
				</select>
			</fieldset>	
			<fieldset>
				<label class="small" for="update_alerts_watch">Email Alerts for projects I'm watching</label>
				<select id="update_alerts_watch" class="query focus-style" name="alerts_watch">
					<option value="instant"<?php if($data['alerts_watch'] == "instant"){ ?> selected<?php } ?>>Instant</option>
					<option value="daily"<?php if($data['alerts_watch'] == "daily"){ ?> selected<?php } ?>>Daily</option>
					<option value="weekly"<?php if($data['alerts_watch'] == "weekly"){ ?> selected<?php } ?>>Weekly</option>
					<option value="monthly"<?php if($data['alerts_watch'] == "monthly"){ ?> selected<?php } ?>>Monthly</option>
					<option value="never"<?php if($data['alerts_watch'] == "never"){ ?> selected<?php } ?>>Never</option>
				</select>
			</fieldset>	
			<fieldset>
				<label for="update_newsletter">Receive newsletter</label>
				<select id="update_newsletter" class="query focus-style" name="newsletter">
					<option value="1"<?php if($data['newsletter'] == "1"){ ?> selected<?php } ?>>Yes</option>
					<option value="0"<?php if($data['newsletter'] == "0"){ ?> selected<?php } ?>>No</option>
				</select>
			</fieldset>
			<input class="button" name="submit" type="submit" value="Save profile settings">
			<div class="clear"></div>
		</form>
	</div>
</div>