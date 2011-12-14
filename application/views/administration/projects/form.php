<form method="POST" action="" class="mainForm"  enctype="multipart/form-data">
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
				<h3>Project details</h3>
			</dd>
			<dd>
				<label for="username" class="long">Username</label>
				<input type="text" id="username" name="username" class="long validate" value="<?php echo @$post['username']?>" <?php if(@$action == "edit"){ ?>DISABLED<?php } ?>/>
			</dd>
			<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$post['title']?>"/>
			</dd>
			<dd>
				<label for="slug" class="long">Slug</label>
				<input type="text" id="slug" name="slug" class="long validate" value="<?php echo @$post['slug']?>"/>
			</dd>
			<dd>
				<label for="amount" class="long">Amount</label>
				<input type="text" id="amount" name="amount" class="long validate" value="<?php echo @$post['amount']?>"/>
			</dd>
			
			<dd>
				<label for="period" class="long">Period (Days)</label>
				<input type="text" id="period" name="period" class="long validate" value="<?php echo @$post['period']?>"/>
			</dd>
			
			<dd>
				<label for="postcode" class="long">Postcode</label>
				<input type="text" id="postcode" name="postcode" class="long validate" value="<?php echo @$post['postcode']?>"/>
			</dd>
			
			<dd>
				<label for="date_created" class="long">Created on</label>
				<input type="text" id="date_created" name="date_created" class="long" value="<?php echo @$post['date_created']?>" DISABLED/>
			</dd>
			
			<dd>
				<label for="date_modified" class="long">Modified on</label>
				<input type="text" id="date_modified" name="date_modified" class="long" value="<?php echo @$post['date_modified']?>" DISABLED/>
			</dd>
			
			<dd>
				<label for="hostname" class="long">Hostname</label>
				<input type="text" id="hostname" name="hostname" class="long validate" value="<?php echo @$post['hostname']?>"/>
			</dd>
			
			<dd>
				<label for="status" class="long">Status</label>
				<select name="status" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="temp" <?php if(@$post['status'] == "temp"){ ?>SELECTED<?php } ?>>Temp</option>
					<option value="open" <?php if(@$post['status'] == "open"){ ?>SELECTED<?php } ?>>Open</option>
					<option value="closed" <?php if(@$post['status'] == "closed"){ ?>SELECTED<?php } ?>>Closed</option>
				</select>
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				<select name="active" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
					<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
				</select>
			</dd>
			<dd>
				<label for="approved" class="long">Approved</label>
				<select name="approved" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['approved'] == "1"){ ?>SELECTED<?php } ?>>Yes</option>
					<option value="0" <?php if(@$post['approved'] == "0"){ ?>SELECTED<?php } ?>>No</option>
				</select>
			</dd>
			<dd>
				<label for="editors_pick" class="long">Editors pick</label>
				<select name="editors_pick" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['editors_pick'] == "1"){ ?>SELECTED<?php } ?>>Yes</option>
					<option value="0" <?php if(@$post['editors_pick'] == "0"){ ?>SELECTED<?php } ?>>No</option>
				</select>
			</dd>
			<dd>
				<label for="outcome" class="long">Outcome</label>
				<textarea name="outcome" id="outcome" style="width: 630px; height: 200px; border: 1px solid silver"><?php echo @$post['outcome']?></textarea>
			</dd>
			<dd>
				<label class="long">Categories</label>
				<div style="width: 630px; float: Left;">
				<?php
					if(!empty($categories)){
						foreach($categories AS $category){ ?>
								<div style="float: left; width: 180px; padding-top: 5px;">
									<input type="checkbox" class="categories_checkbox" name="categories[<?php echo $category->idcategory?>]" <?php if(!empty($selected_categories[$category->idcategory])){ echo "CHECKED"; }?> value="<?php echo $category->idcategory?>"> <?php echo $category->title; ?>
								</div>
								<?php 
								if(!empty($category->subcategories)){
									foreach($category->subcategories AS $subcategory){
										?>
											<div style="float: left; width: 180px; padding-top: 5px;">
												<input type="checkbox" class="categories_checkbox" name="categories[<?php echo $subcategory->idcategory?>]" <?php if(!empty($selected_categories[$subcategory->idcategory])){ echo "CHECKED"; }?> value="<?php echo $subcategory->idcategory?>"> <?php echo $subcategory->title; ?>
											</div>
										<?php
									}
								}
						} 
					} else {
						?> 
							<option value="0">None</option>
						<?php
					}
				?>
				</div>
			</dd>
			<dd>
				<label for="about" class="long">About</label>
				<textarea name="about" id="about" class="mceEditor" style="width: 630px; height: 200px;"><?php echo @$post['about']?></textarea>
			</dd>
			<dd>
				<label for="about" class="long">Helpers accepted</label>
				<input type="radio" name="helpers" value="1" <?php if(@$post['helpers'] == "1"){ ?>CHECKED<?php } ?>> Yes<br>
				<input type="radio" name="helpers" value="0" <?php if(@$post['helpers'] == "0"){ ?>CHECKED<?php } ?>> No
			</dd>
			<dd>
				<label class="long">Celebrity backed project</label>
				<div style="width: 630px; float: Left;">
					<div style="float: left; width: 80px; padding-top: 5px;">
						<input type="radio" class="celebrity_backed_yes" name="celebrity_backed" <?php if(@$post['celebrity_backed'] == 1){ echo "CHECKED"; }?> value="1"> Yes
					</div>
					<div style="float: left; width: 80px; padding-top: 5px;">
						<input type="radio" class="celebrity_backed_no" name="celebrity_backed" <?php if(@$post['celebrity_backed'] == 0){ echo "CHECKED"; }?> value="0"> No
					</div>
				</div>
			</dd>
			<script>
				$(document).ready(function(){
					$('.celebrity_backed_yes').click(function(){
						$('#show-celebrity-options').show();
						$(this).attr('checked', true);
					});
					$('.celebrity_backed_no').click(function(){
						$('#show-celebrity-options').hide();
						$(this).attr('checked', true);
					});
				});
				
				
			</script>
			<div<?php if(@$post['celebrity_backed'] == 0){ echo ' class="hidden"'; }?> id="show-celebrity-options">
				<dd>
					<label for="celebrity_title" class="long">Celebrity title</label>
					<input type="text" id="celebrity_title" name="celebrity_title" class="long" value="<?php echo @$post['celebrity_title']?>"/>
				</dd>
				<dd>
					<label for="celebrity_quote" class="long">Celebrity quote</label>
					<textarea name="celebrity_quote" id="celebrity_quote" class="long" style="width: 630px; height: 200px;"><?php echo @$post['celebrity_quote']?></textarea>
				</dd>
				<dd>
					<label for="celebrity_image" class="long">Celebrity image</label>
					<input type="file" id="celebrity_image" name="celebrity_image" class="long" value=""/>
					<img class="preview" style="margin: 20px 0 0 200px ;" src="/uploads/celebrities/<?php echo @$post['celebrity_image']?>" height="68" width="75">
				</dd>				
			</div>
			<dd>
				<label for="spend-time" class="long">Time</label>
				<input type="text" id="spend-time" name="time" class="long validate" value="<?php echo  @$post['time'] ?>"/>
			</dd>
			<dd>
				<label for="spend-time" class="long">Skills</label><div class="clear3"></div>
				<?php 
					if(isset($post['skills']) && $post['skills']) {
						$post['skills'] = unserialize($post['skills']);
						foreach($post['skills'] as $skill) { ?>
							<label class="long">&nbsp;</label>
							<input type="text" id="skills" name="skills[]" class="long validate" value="<?php echo  $skill ?>"/>
							<div class="clear3"></div>
						<?php }
					}
				?>
			</dd>
			<dd>
				<label for="video" class="long">Video / Image</label>
				
				<link href="/css/site/swfupload.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="/js/site/swfupload/swfupload.js"></script>
					<script type="text/javascript" src="/js/site/swfupload/swfupload.queue.js"></script>
					<script type="text/javascript" src="/js/site/swfupload/fileprogress.js"></script>
					<script type="text/javascript" src="/js/site/swfupload/handlers.js"></script>
					
					<script type="text/javascript">
					
						// Vars for main objects/values
						var vzaar_signature = <?php echo(json_encode(Vzaar::getUploadSignature())); ?>;
						var swfu;
						var s3Response = {};
						var idproject = "<?php echo @$post['idproject']; ?>";
						
						// When the page is loaded
						$(function(){
						
							// Initialize settings array
							var settings = {
							
								// Main parameters
								flash_url : "/js/site/swfupload/swfupload.swf",
								upload_url : 'http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/',
								post_params: {
									"content-type" : "binary/octet-stream",
									"acl" : vzaar_signature["vzaar-api"].acl,
									"policy" :  vzaar_signature["vzaar-api"].policy,
									"AWSAccessKeyId" :  vzaar_signature["vzaar-api"].accesskeyid,
									"signature" :  vzaar_signature["vzaar-api"].signature,
									"success_action_status" : "201",
									"key" :  vzaar_signature["vzaar-api"].key
								},
								use_query_string: false,
								file_post_name: 'File',
								file_size_limit : 0,
								file_types : "*.png; *.jpg; *.jpeg; *.png; *.avi; *.wmv; *.mp4; *.mpeg; *.flv",
								file_types_description : "All Files",
								file_upload_limit : 1/*number of files*/,
								file_queue_limit : 0,
								custom_settings : {
									progressTarget : "fsUploadProgress",
									cancelButtonId : "btnCancel"
								},
								debug: false,

								// Button settings
								button_image_url: "/img/buttons/swfupload-admin.png",
								button_width: "80",
								button_height: "29",
								button_placeholder_id: "spanButtonPlaceHolder",
								button_text: '<span class="uploadButton">Browse...</span>',
								button_text_style: ".uploadButton { font-size: 14; color: #FFFFFF; }",
								button_text_left_padding: 12,
								button_text_top_padding: 3,

								// Events
								file_queued_handler : fileQueued,
								file_queue_error_handler : fileQueueError,
								file_dialog_complete_handler : fileDialogComplete,
								
								// On start - check if we are uploading image or video
								upload_start_handler : function uploadStart(file){
								
									// Cast extension to upper case
									var ext = file.type.toUpperCase();
									
									// If uploading image
									if(ext == ".JPG" || ext == ".PNG" || ext == ".JPEG" || ext == "GIF"){
										swfu.addPostParam("idproject", idproject);
										swfu.setUploadURL("/projects/add_image/");
										
									// If uploading video
									} else {
										swfu.removePostParam("idproject");
										swfu.setUploadURL('http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/');
									}
								},
								upload_progress_handler : uploadProgress,
								
								// On http error
								upload_error_handler : function uploadError(file, errorCode, message){
									$('#status').html(message);
								},
								
								// On success
								upload_success_handler : function uploadSuccess(file, serverData) {
								
									// Cast extension to upper
									var ext = file.type.toUpperCase();
									
									// If uploading video
									if(ext != ".JPG" && ext != ".PNG" && ext != ".JPEG" && ext != "GIF"){
									
										// Set file count limit to unlimited
										swfu.setFileUploadLimit("1");
									
										// Get GUID
										s3Response = $(serverData);
										var arrKey = s3Response.find('key').html().split('/');
										var guid = arrKey[arrKey.length-2];

										$('#status').html(guid);

										//calling Process Video service
										$.post('/projects/processVideo/', {
											guid: guid,
											title: 'S3_Upload Automated Sample',
											description: '',
											idproject: idproject
										}, function(data){
											
											// Remove progress bar
											$(".progressWrapper").remove();
											$("#uploader_div").hide();
											
											// Show video
											$("#preview").html("The video is uploaded and will be available soon");
										});
									
									// If uploading image
									} else {
									
										// Remove progress bar
										$(".progressWrapper").remove();
										
										// Set limit to unlimited
										swfu.setFileUploadLimit("0");
										
										// Add image to the hidden field
										$("#image").val(serverData);
											
										// Show preview
										$("#preview").html("");
										$("#preview").html("<img src='/uploads/projects/"+serverData+"?rand="+Math.random()+"' width='150'><br>");
										
										// If editing project - show remove link
										if(idproject != ""){
											$("#preview").append("<a href='javascript:;' onClick='removeResource();'>Remove</a>");
										
										// If adding project - just remove the image from the field/preview
										} else {
											$("#preview").append("<a href='javascript:;' onClick='removeResource();'>Remove</a>");
										}
									}
								}
							};

							swfu = new SWFUpload(settings);
						});
					</script>

					<div id="divStatus" style="display: none;" style="margin-left: 202px;"></div>
					<div id="uploader_div" style="margin-left: 202px;">
						<span id="spanButtonPlaceHolder">Browse...</span>
						<input id="btnCancel" type="button" value="Cancel" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
					</div>
					<div class="fieldset flash" id="fsUploadProgress" style="margin-left: 202px;"></div>
					
				<?php // If the user has video
				if(!empty($post['vzaar_idvideo'])){
				
					// If it's not processed yet
					if($post['vzaar_processed'] == "0"){ ?>
					
						<div id="preview" style="margin-left: 202px; padding-top: 7px; font-size: 11px; color: green;">The video is uploaded and will be available soon</div>
						
					<?php // If the video is processed
					} else { ?>
							
						<!-- VZAAR START -->
						<div class="vzaar_media_player" id="preview" style="margin-left: 202px; padding-top: 20px;">
							<object id="video" width="448" height="280" type="application/x-shockwave-flash" data="http://view.vzaar.com/<?php echo $post['vzaar_idvideo']?>.flashplayer">
								<param name="movie" value="http://view.vzaar.com/<?php echo $post['vzaar_idvideo']?>.flashplayer">	
								<param name="allowScriptAccess" value="always">
								<param name="allowFullScreen" value="true">
								<param name="wmode" value="transparent">
								<param name="flashvars" value="border=none">
								<embed src="http://view.vzaar.com/<?php echo $post['vzaar_idvideo']?>.flashplayer" type="application/x-shockwave-flash" wmode="transparent" width="448" height="280" allowScriptAccess="always" allowFullScreen="true" flashvars="border=none">
								<video width="448" height="280" src="http://view.vzaar.com/<?php echo $post['vzaar_idvideo']?>.mobile" poster="http://view.vzaar.com/<?php echo $post['vzaar_idvideo']?>.image" controls onclick="this.play();"></video>
							</object>
							<br>
							<a href="javascript:;" onClick="removeResource();">Remove</a>
						</div>
						<!-- VZAAR END -->

						

					<?php } ?>
				
				<?php // If the user don't have video
				} else { ?>
					
					<div id="preview" style="padding-top: 7px; font-size: 11px; color: green; padding-left: 205px;">
						<?php if(!empty($post['ext'])){ ?>
							<img width="150" src="/uploads/projects/<?php echo $post['idproject'];?>.<?php echo $post['ext'];?>"><br>
							<a href="javascript:;" onClick="removeResource();">Remove</a>
						<?php } ?>
					</div>
				<?php } ?>
				
				<script>
					// Remove image when adding project
					function removeResource(){ 
						$.post('/administration/projects/remove_resource/<?php echo @$post['idproject']; ?>', function(data) {
							$("#preview").html("");
						});
					}
				</script>
			</dd>
			<dd class="for_type">
				<label class="long">Project rewards</label>
				<label class="short" style="width: auto;">
					<table id="listing_confirmed_" cellpadding="0" cellspacing="0" class="resultsTable" style="width: 700px; background: #fff;">
						<thead>
							<tr>
								<td>Amount</td>
								<td>Description</td>
								<td>Limited</td>
								<td>Number</td>
								<td>Used</td>
								<td>Action</td>
							</tr>
						</thead>
						<tbody>

						<?php foreach(@$amounts AS $amount){ ?>
							<tr class="<?php echo $amount->idamount?>" id="<?php echo $amount->idamount?>">
								<td><?php echo number_format($amount->amount)?></td>
								<td><?php echo $amount->description?></td>
								<td><?php echo ucfirst($amount->limited)?></td>
								<td><?php if($amount->limited == "yes"){ echo $amount->number; } else { echo "-"; }?></td>
								<td><?php if($amount->used){ echo "Yes"; } else { echo "No"; } ?></td>
								<td class="icons">
									<?php if(!$amount->used){ ?>
										<a href="#" class="delete_amount" title="Delete">Delete</a>
									<?php } ?>
									<a title="Edit" class="edit" href="/administration/projects/edit_amount/<?php echo $amount->idamount?>/<?php echo $amount->idproject?>/">Edit</a>

								</td>
							</tr>
						<?php } ?>

						</tbody>
					</table>
					<a href="/administration/projects/add_amount/<?php echo $post['idproject']?>/" style="float: right; padding-top: 5px; font-size: 13px;">Add amount</a>
				</label>
				
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
				<input type="hidden" name="post_check" value="1">
			</dd>
		</dl>
	</fieldset>
</form>