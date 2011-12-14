<!-- Upload via AJAX -->
<div style="display: none;">
	<script src="/js/jquery/jquery.form.js"></script>	
	
	<form id="upload-form-js" method="POST" action="/user/add_image/" enctype="multipart/form-data">
			<input type="file" id="browse-file" name="File" style="display: none; position: absolute; top: -9999px; height: 0px; left: -9999px; z-index: 1;">
			<input id="fake-file" style="display: none;" value="">		
			<input type="hidden"  name="iduser" value="<?php echo $post['iduser']; ?>">
	</form>
</div>

<form method="POST" action="" class="mainForm">
	<fieldset>
		<dl>
			<?php if(!empty($errors)): ?>
					<dd class="error">
						<strong>Errors:</strong><br />
						<?php foreach($errors as $e): ?>
									&nbsp;&nbsp;<img src="<?php echo site_url('img/bullet3.gif'); ?>" alt="" />&nbsp;<?php echo ucfirst($e) ?><br />
						<?php endforeach; ?>
					</dd>
			<?php endif; ?>

			<dd>
				<h3>User details</h3>
			</dd>
			<dd>
				<label for="username" class="long">Username</label>
				<input type="text" id="username" name="username" class="long validate" value="<?php echo @$post['username']?>" />
			</dd>
			<dd>
				<label for="fbid" class="long">Facebook ID</label>
				<input type="text" id="fbid" name="fbid" class="long" value="<?php echo @$post['fbid']?>"/>
			</dd>
			<dd>
				<label for="esid" class="long">Energyshare ID</label>
				<input type="text" id="esid" name="esid" class="long" value="<?php echo @$post['esid']?>"/>
			</dd>
			<dd>
				<label for="name" class="long">Name</label>
				<input type="text" id="name" name="name" class="long" value="<?php echo @$post['name']?>"/>
			</dd>
			<dd>
				<label for="slug" class="long">Slug</label>
				<input type="text" id="slug" name="slug" class="long" value="<?php echo @$post['slug']?>"/>
			</dd>
			<dd>
				<label for="bio" class="long">Bio</label>
				<textarea id="bio" name="bio" class="long"><?php echo @$post['bio']?></textarea>
			</dd>
			<dd>
				<label for="postcode" class="long">Postcode</label>
				<input type="text" id="postcode" name="postcode" class="long" value="<?php echo @$post['postcode']?>"/>
			</dd>
			<dd>
				<label for="location" class="long">Location</label>
				<input type="text" id="location" name="location" class="long" value="<?php echo @$post['location']?>"/>
			</dd>
			<dd>
				<label for="town_name" class="long">Town Name</label>
				<input type="text" id="town_name" name="town_name" class="long" value="<?php echo @$post['town_name']?>"/>
			</dd>
			<dd>
				<label for="county_name" class="long">County Name</label>
				<input type="text" id="county_name" name="county_name" class="long" value="<?php echo @$post['county_name']?>"/>
			</dd>
			<dd>
				<label for="location_preview" class="long">Location Preview</label>
				<input type="text" id="location_preview" name="location_preview" class="long" value="<?php echo @$post['location_preview']?>"/>
			</dd>
			<dd>
				<label for="websites" class="long">Websites</label>
				<textarea id="websites" name="websites" class="long"><?php echo @$post['websites']?></textarea>
				<label for="example_websites" class="long">Example </label>
				<span id="example_websites" class="long">www.example1.org|www.example2.org|www.example3.org</span>
			</dd>
			<dd>
				<label for="alerts_own" class="long">Alerts Own</label>
				<select name="alerts_own" id="alerts_own" class="long">
					<option value="instant">Instant</option>
					<option value="daily">Daily</option>
					<option value="weekly">Weekly</option>
					<option value="monthly">Monthly</option>
					<option value="never">Never</option>
				</select>
			</dd>
			<dd>
				<label for="alerts_backing" class="long">Alerts Backing</label>
				<select name="alerts_backing" id="alerts_backing" class="long">
					<option value="instant">Instant</option>
					<option value="daily">Daily</option>
					<option value="weekly">Weekly</option>
					<option value="monthly">Monthly</option>
					<option value="never">Never</option>
				</select>
			</dd>
			<dd>
				<label for="alerts_watch" class="long">Alerts Watch</label>
				<select name="alerts_watch" id="alerts_watch" class="long">
					<option value="instant">Instant</option>
					<option value="daily">Daily</option>
					<option value="weekly">Weekly</option>
					<option value="monthly">Monthly</option>
					<option value="never">Never</option>
				</select>
			</dd>
			
			<dd>
				<label for="hash" class="long">Hash</label>
				<input type="text" id="hash" name="hash" class="long" value="<?php echo @$post['hash']?>"/>
			</dd>
			<dd>
				<label for="password" class="long">Password</label>
				<input type="password" id="password" name="password" class="long <?php if(@$action != "edit"){ ?>validate<?php } ?>" value=""/>
			</dd>
			<dd>
				<label for="password_repeat" class="long"><span>Password confirm</span></label>
				<input type="password" id="password_repeat" name="password_repeat" class="long <?php if(@$action != "edit"){ ?>validate<?php } ?>" value="" />
			</dd>
			<dd>
				<label for="email" class="long">Email</label>
				<input type="email" id="email" name="email" class="long validate" value="<?php echo @$post['email']?>"/>
			</dd>
			<dd>
				<label for="interests" class="long">Interests</label>
				<?php $post['interests'] = ((isset($post['interests']) && $post['interests'])) ? unserialize($post['interests']) : array() ?>
				<div style="width: 20%; float: left;">
					<div class="clear10"></div>
					<?php foreach($categories as $k=>$category) { ?>
						<?php if($k%2 == 0) continue; $checked = (in_array($category->idcategory, $post['interests'])) ? TRUE : FALSE; ?>
						<input<?php echo ($checked) ? ' checked' : '' ?> type="checkbox" name="interests[]" class="" value="<?php echo h(st($category->idcategory)) ?>"> <?php echo h(st($category->title)) ?>
						<div class="clear5"></div>
					<?php } ?>
				</div>
				<div style="width: 20%; float: left;">
					<div class="clear10"></div>
					<?php foreach($categories as $k=>$category) { ?>
						<?php if($k%2 <> 0) continue; $checked = (in_array($category->idcategory, $post['interests'])) ? TRUE : FALSE; ?>
						<input<?php echo ($checked) ? ' checked' : '' ?> type="checkbox" name="interests[]" class="" value="<?php echo h(st($category->idcategory)) ?>"> <?php echo h(st($category->title)) ?>
						<div class="clear5"></div>
					<?php } ?>
				</div>
			</dd>
			<dd>
				<label for="confirmed" class="long">Activated</label>
				<input type="radio" name="confirmed" value="1" <?php if(@$post['confirmed'] == "1"){ echo "CHECKED"; }?>> Yes
				&nbsp;
				<input type="radio" name="confirmed" value="0" <?php if(@$post['confirmed'] == "0"){ echo "CHECKED"; }?>> No
			</dd>
			<dd>
				<label for="newsletter" class="long">Newsletter</label>
				<input type="radio" name="newsletter" value="1" <?php if(@$post['newsletter'] == "1"){ echo "CHECKED"; }?>> Yes
				&nbsp;
				<input type="radio" name="newsletter" value="0" <?php if(@$post['newsletter'] == "0"){ echo "CHECKED"; }?>> No
			</dd>
			<dd>
				<label for="type" class="long">Administrator</label>
				<input type="radio" name="type" value="user" <?php if(@$post['type'] == "user"){ echo "CHECKED"; }?>> User
				&nbsp;
				<input type="radio" name="type" value="admin" <?php if(@$post['type'] == "admin"){ echo "CHECKED"; }?>> Admin
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				<select name="active" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
					<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
				</select>
			</dd>
			
			<?php if(@$page != "add"){ ?>
				<dd>
					<label for="image" class="long">Image</label>
					
					<link href="/css/site/swfupload.css" rel="stylesheet" type="text/css" />

					
					<!-- Image uploader -->
					<script type="text/javascript">
						// Remove image when adding project
						function remove_image(){
							$.post('/administration/users/remove_image/<?php echo @$post['iduser']; ?>', function(data) {
								$("#preview").html("");
							});
						}
					</script>

					<div id="uploader_div">
						
							
						<input id="file-upload-button" type="button" value="Upload" class="button" style="float: none;">
						
						<input id="browse-button" type="button"  value="Browse Computer" class="button" style="float: none;">
						<script>
								$('#browse-button').click(function(){
									$('#browse-file').click();
								});
							
								$('#browse-file').change( function() {
									$('#fake-file').val( $( this ).val() );
									$('#file-upload-button').click();
									
									return false;
								});
							
								$('#file-upload-button').click(function(){
									$('form#upload-form-js').submit();
								});
						
								$('form#upload-form-js' ).submit( function() {
									if( $( '#fake-file' ).val().trim() == '' ) {
										$( '#error-message' ).show();
										return false;
									}
									$( '#error-message' ).hide();
									
									var postFormOptions = {					
											success : function( data ){
												if( data.success ) {
													var new_image_url = data.html;
													var new_html = '<img width="150" src="/uploads/users/'+new_image_url+'">';
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
						</script>					
					
					</div>
					
					<!-- <div class="fieldset flash" id="fsUploadProgress"></div>
					-->
					
					<div id="preview" style="padding-left: 205px; padding-top: 10px;">
						<?php if(!empty($post['ext'])){ ?>
							<img width="150" src="/uploads/users/<?php echo $post['iduser'];?>.<?php echo $post['ext'];?>">
							<br><a href="javascript:;" onClick="remove_image();">Remove image</a>
						<?php } ?>
					</div>
					
					
				</dd>
				
				<dd class="submitDD">
					<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
					<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
					<input type="hidden" name="post_check" value="1">
				</dd>
			</dl>
		<?php } ?>
	</fieldset>
</form>