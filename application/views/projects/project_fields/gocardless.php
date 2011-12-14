<?php	
	if(isset($project->merchant_id) && $project->merchant_id)
		$addValue = h(st($project->merchant_id));
	else if(isset($post['merchant_id']) && $post['merchant_id'])
		$addValue = h(st($post['merchant_id']));
	else
		$addValue = '';		
?>
<?php if(!$addValue) { ?>
<fieldset>	
	<label for="merchant_id" style="width: auto;">
		<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
		<span id="gocardless-no">Associate your project with GoCardless account</span>
		<span id="gocardless-yes" class="hidden">You have successfully linked your GoCardless account</span>
	</label>
	<div id="gocardless-no-block">
		<div class="clear10"></div>
		<input id="select-gocardless-account" class="button left" name="gocardless" type="button" value="Select GoCardless account">
	</div>
	<input id="merchant_id" type="hidden" name="merchant_id" value="">
	<input id="access_token" type="hidden" name="access_token" value="">
</fieldset>	
<?php } else { ?>
<fieldset>	
	<label for="merchant_id" style="width: auto;">
		<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
		You have successfully linked your GoCardless account
	</label>
	<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo $addValue ?>">
</fieldset>	
<?php } ?>