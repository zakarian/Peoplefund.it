<!-- Upload via AJAX -->
<div style="display: none;">
	<script src="/js/jquery/jquery.form.js"></script>	
	
	<form id="upload-form-js" method="POST" action="/administration/partners/add_image/" enctype="multipart/form-data">
			<input type="file" id="browse-file" name="File" style="display: none; position: absolute; top: -9999px; height: 0px; left: -9999px; z-index: 1;">
			<input id="fake-file" style="display: none;" value="">		
			<input type="hidden"  name="idpartner" value="<?php echo (!empty($post['idpartner'])) ? $post['idpartner'] : "0" ; ?>">
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
				<h3>Partner details</h3>
			</dd>
			<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$post['title']?>" />
			</dd>
			<dd>
				<label for="url" class="long">URL</label>
				<input type="text" id="url" name="url" class="long" value="<?php echo @$post['url']?>" />
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				<select name="active" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
					<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
				</select>
			</dd>
			
			<?php if(@$action == "edit"){ ?>
				<dd>
					<label for="image" class="long">Image</label>
					
					<script>
						// Remove image when adding project
						function remove_image(){
							$.post('/administration/partners/remove_image/<?php echo @$post['idpartner']; ?>', function(data) {
								$("#preview").html("");
							});
						}
					</script>

					<div id="uploader_div">
						<input id="file-upload-button" type="button" value="Upload" class="button" style="float: none; display: none;">
						
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
													var new_html = '<img width="150" src="'+new_image_url+'">';
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
						<?php if(!empty($post['image'])){ ?>
							<img width="150" src="<?php echo  $post['image']; ?>?=<?php echo rand() ?>">
							<br><a href="javascript:;" onClick="remove_image();">Remove image</a>
						<?php } ?>
					</div>
					
					
				</dd>
				<?php } ?>
				<dd class="submitDD">
					<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
					<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
					<input type="hidden" name="post_check" value="1">
				</dd>
			</dl>
		
	</fieldset>
</form>