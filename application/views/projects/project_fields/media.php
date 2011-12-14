<?php	
	
	$vzaar_idvideo = (isset($project->vzaar_idvideo) && $project->vzaar_idvideo) ? $project->vzaar_idvideo : @$post['vzaar_idvideo'];
	$vzaar_processed = (isset($project->vzaar_processed) && $project->vzaar_processed) ? $project->vzaar_processed : @$post['vzaar_processed'];
	$slug = (isset($project->slug) && $project->slug) ? $project->slug : @$post['slug'];
	$embed = (isset($project->embed) && $project->embed) ? $project->embed : @$post['embed'];
	$ext = (isset($project->ext) && $project->ext) ? $project->ext : @$post['ext'];
	$idproject = (isset($project->idproject) && $project->idproject) ? $project->idproject : @$post['idproject'];
	$_post_image = isset($post['image']) && $post['image'] ? $post['image'] : '';
			
?>
<fieldset>
	<label for="" style="width: auto;">Upload a video</label>
	<div class="clear10"></div>
	<small class="label">
		Please link to (ideally) or upload a short video pitching your project. All projects on peoplefund.it must upload a video because projects are so much more likely to get funded with a video than without one - and we don't want you to waste your time! We suggest you upload your video on a video hosting site such as youtube because that means more people can see it. But you can upload it below if you prefer.
	</small>
	<div class="clear10"></div>
	<?php  
		if(!empty($vzaar_idvideo) || !empty($_SESSION['vzaar_idvideo'])) {
			if(empty($vzaar_processed) || $vzaar_processed == 0) { 
	?>
			<div id="preview"><b>Your video is uploaded and will be available soon</b></div>
	<?php } else { ?>
			<div class="vzaar_media_player">
				<object width="400" height="250" type="application/x-shockwave-flash" data="http://view.vzaar.com/<?php echo $vzaar_idvideo ?>.flashplayer">
					<param name="movie" value="http://view.vzaar.com/<?php echo $vzaar_idvideo ?>.flashplayer">	
					<param name="allowScriptAccess" value="always">
					<param name="allowFullScreen" value="true">
					<param name="wmode" value="transparent">
					<param name="flashvars" value="border=none">
					<embed src="http://view.vzaar.com/<?php echo $vzaar_idvideo ?>.flashplayer" type="application/x-shockwave-flash" wmode="transparent" width="400" height="250" allowScriptAccess="always" allowFullScreen="true" flashvars="border=none">
					<video width="400" height="250" src="http://view.vzaar.com/<?php echo $vzaar_idvideo ?>.mobile" poster="http://view.vzaar.com/<?php echo $vzaar_idvideo ?>.image" controls onclick="this.play();"></video>
				</object>
			</div>
			<a href="/<?php echo $slug ?>/removeVideo/"><b>Remove</b></a>
	<?php } 
		} else { ?>
			<div id="preview">
				<?php if(!empty($embed)){ ?>
					<?php echo $embed ?><br>
					<a class="link" href="javascript:;" onClick="removeResource();"><b>Remove</b></a>
				<?php } else if(!empty($ext)){ ?>
					<img width="320" src="/uploads/projects/<?php echo $idproject ?>.<?php echo $ext ?>"><br>
					<a class="link" href="javascript:;" onClick="removeResource();"><b>Remove</b></a>
				<?php } else if(!empty($_post_image)){ ?>
					<img width="320" src="/uploads/projects/<?php echo $_post_image ?>"><br>
					<a class="link" href="javascript:;" onClick="removeResource();"><b>Remove</b></a>
				<?php } ?>
			</div>			
			<script type="text/javascript">
				var vzaar_signature = <?php echo(json_encode(Vzaar::getUploadSignature())) ?>;
				var swfu;
				var s3Response = {};
				var idproject = '<?php echo ($idproject) ? $idproject : -1 ?>';
									
				$(function(){
					var settings = {
						flash_url : '/js/site/swfupload/swfupload.swf',
						upload_url : 'http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/',
						post_params: {
							'content-type' : 'binary/octet-stream',
							'acl' : vzaar_signature["vzaar-api"].acl,
							'policy' :  vzaar_signature["vzaar-api"].policy,
							'AWSAccessKeyId' :  vzaar_signature["vzaar-api"].accesskeyid,
							'signature' :  vzaar_signature["vzaar-api"].signature,
							'success_action_status' : '201',
							'key' :  vzaar_signature["vzaar-api"].key
						},
						use_query_string: false,
						file_post_name: 'File',
						file_size_limit: 0,
						file_types: '*.avi; *.wmv; *.mp4; *.mpeg; *.flv',
						file_types_description: "All Files",
						file_upload_limit: 1,
						file_queue_limit: 0,
						custom_settings : {
							progressTarget: 'fsUploadProgress',
							cancelButtonId: 'btnCancel'
						},
						debug: false,
						button_image_url: '/img/site/swfupload.png',
						button_width: '154',
						button_height: '22',
						button_placeholder_id: 'spanButtonPlaceHolder',
						file_queued_handler : fileQueued,
						file_queue_error_handler : fileQueueError,
						file_dialog_complete_handler : fileDialogComplete,
										
						upload_start_handler: function uploadStart(file){
							var ext = file.type.toUpperCase();
							swfu.removePostParam('idproject');
							swfu.setUploadURL('http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/');
							swfu.setUploadURL('http://'+vzaar_signature["vzaar-api"].bucket+'.s3.amazonaws.com/');
						},	
						upload_progress_handler : uploadProgress,
						upload_error_handler : function uploadError(file, errorCode, message){
							$('#status').html(message);
						},
						upload_success_handler : function uploadSuccess(file, serverData) {
							document.getElementById('files-upload-progress').style.display = 'none';
							var ext = file.type.toUpperCase();
							swfu.setFileUploadLimit('1');
							s3Response = $(serverData);
							var arrKey = s3Response.find('key').html().split('/');
							var guid = arrKey[arrKey.length-2];
							$('#status').html(guid);
							$.post('/projects/process_video/', {
								guid: guid,
								title: 'S3_Upload Automated Sample',
								description: '',
								idproject: idproject
							}, function(data){
								$('.progressWrapper').remove();
								$('#uploader_div').hide();
								$('#preview').html('<b>Your video is uploaded and will be available soon</b>');
							});
						}
					};
					swfu = new SWFUpload(settings);
				});			
				function removeResource(){
					$.post('/projects/remove_resource/<?php echo $idproject ?>', function(data) {
						$('#preview').html('');
						$('#image').val('');
					});
				}
				$(document).ready( function() {
					$( '#group-external-picture' ).focus( function() {
						$(this).val('');
					});
					$('#oembed-submit-button').click( function() {
						if($('#group-external-picture').val().trim == '') return true;
						$.post('/projects/insert_oembed/', { url: $( '#group-external-picture').val(), idproject: idproject }, function(data) { 
							if(data.success) {
								$('#image').val(data.file);
								$('#embed').val(data.oembed.embed);
								$('#preview').html('');
								$('#preview').html(data.oembed.embed+'<br>');
								$("#preview").append("<a class='link' href='javascript:;' onClick='removeResource();'><b>Remove</b></a>");
							} else 
								alert(data.msg);
						}, 'json');
						return false;
					});
				});
			</script>
			<div class="clear"></div>
			<div class="fieldset flash hidden" id="fsUploadProgress"></div>
			<div id="divStatus" style="display: none;"></div>
			
			<div id="files-upload-progress" class="hidden">
				<div class="clear10"></div>
				<input type="text" id="upload-progress-loading" class="frounded" readonly="readonly">
				<div class="working left"><span></span></div>
				<div class="clear10"></div>
			</div>
			<div class="clear20"></div>
							
			<label for="insert_title">Link to URL</label>
			<div class="clear5"></div>
							
			<small class="label">
				Top tip: it's best to set up an account with a popular video hosting website such as Youtube, and upload your film there <br>as more people will see it!
			</small>
			<div class="clear10"></div>
							
			<div>
				<input autocomplete="off" id="group-external-picture" type="text" name="url" class="extrounded"  value="" />
				<input id="oembed-submit-button" class="extadd" name="button" type="button" value="ADD" />
				<div class="clear20"></div>
			</div>
							
			<span style="line-height: 22px; padding: 0 10px 0 0;" class="block left">OR</span>
			<div id="uploader_div">
				<span style="position: relative; z-index: 0; float: left; margin-right: 20px;" id="spanButtonPlaceHolder">Browse...</span>
				<input id="btnCancel" type="hidden" value="Cancel" onclick="swfu.cancelQueue();" disabled="disabled" />
			</div>	
			<div class="clear"></div>
		<?php } ?>
	<div class="clear15"></div>
	<span style="color: #999999;">Do not navigate away until file fully uploaded</span>
	<div class="clear5"></div>
</fieldset>	