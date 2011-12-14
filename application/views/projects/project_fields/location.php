<?php	
	if(isset($project->postcode) && $project->postcode)
		$addValue = h(st($project->postcode));
	else if(isset($post['postcode']) && $post['postcode'])
		$addValue = h(st($post['postcode']));
	else
		$addValue = '';	

	if(isset($project->location_preview) && $project->location_preview)
		$location_preview = h(st($project->location_preview));
	else
		$location_preview = '';	
		
	if(isset($project->county_name) && $project->county_name)
		$county_name = h(st($project->county_name));
	else
		$county_name = '';	
		
?>
<fieldset>
	<label for="insert_location">
		<?php if(isset($errors['postcode']) && $errors['postcode']) { ?><b style="color: red;">*</b><?php } ?>
		Location
	</label>
	<div class="clear5"></div>
	<small class="label">
		Please enter a postcode where your project will happen so that people can search for projects by postcode.
	</small>
	<div class="clear5"></div>
	<small style="color: #999999;" class="label" id="postcode_location">
		<?php if($location_preview) { 
			echo $location_preview;
			echo ($county_name) ? ', '.$county_name : '';
		} ?>
	</small>
	<div class="clear10"></div>
	<input autocomplete="off" class="rounded" id="postcode" type="text" name="postcode" value="<?php echo $addValue ?>" />
	<input class="verify" name="button" type="button" value="Verify" onClick="checkPostcode();" />
	<?php if($addValue) { ?>
		<script>
			$(function() { checkPostcode(); });
		</script>
	<?php } ?>
	<div class="clear"></div>
	<div id="postcode_message" style="padding-top: 5px;"></div>
	<div class="clear10"></div>
</fieldset>	
<script>
	function checkPostcode(){
		$.post('/user/get_location_by_postcode/', {postcode: $("#postcode").val()}, function(data) {
			if($.trim(data) != ',' || $.trim(data) != ''){
				$("#postcode_message").html("<font color='green'>The postcode is OK</font>");
				$("#postcode_location").html($.trim(data));
			} else {
				$("#postcode_message").html("<font color='red'>The postcode is not found</font>");
				$("#postcode_location").html("");
			}
		});
	}
</script>