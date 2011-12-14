<?php	
	if(isset($project->paypal_email) && $project->paypal_email)
		$addValue = h(st($project->paypal_email));
	else if(isset($post['paypal_email']) && $post['paypal_email'])
		$addValue = h(st($post['paypal_email']));
	else
		$addValue = '';		
?>
<fieldset>	
	<label for="paypal_email" style="width: auto;">
		<?php if(isset($errors['paypal_email']) && $errors['paypal_email']) { ?><b style="color: red;">*</b><?php } ?>
		Paypal account email
	</label>
	<div class="clear10"></div>
	<input id="paypal_email" class="query focus-style" type="text" name="paypal_email" value="<?php echo $addValue ?>">
</fieldset>	