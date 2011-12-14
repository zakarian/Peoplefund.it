<?php	
	if(isset($project->amount) && $project->amount)
		$addValue = h(st($project->amount));
	else if(isset($post['amount']) && $post['amount'])
		$addValue = h(st($post['amount']));
	else
		$addValue = '';		
		
	if(isset($project->status) && $project->status)
		$status = h(st($project->status));
	else
		$status = 'temp';	
?>
<?php if((isset($status) && $status == 'temp')){ ?>
	<fieldset>	
		<label for="insert_amount" style="width: auto;">	
			<?php if(isset($errors['amount']) && $errors['amount']) { ?><b style="color: red;">*</b><?php } ?>
			FUNDING TARGET IN &pound; (YOU CAN ONLY ENTER NUMBERS. THE MAXIMUM FUNDING TARGET YOU CAN ENTER IS &pound;50,000 AND THE MINIMUM IS &pound;1000, HOWEVER YOU CAN CHOOSE TO ENABLE PEOPLE TO KEEP PLEDGING BEYOND THIS TARGET BELOW.)
		</label>
		<div class="clear10"></div>
		<input id="insert_amount" class="query focus-style" type="text" name="amount" value="<?php echo $addValue ?>" maxlength="6" />
	</fieldset>
<?php } else { ?>	
	<fieldset>
		<label for="" style="width: auto;">
			Target: &pound;<?php echo ($addValue) ?>
		</label>
		<div class="clear"></div>
	</fieldset>
<?php } ?>