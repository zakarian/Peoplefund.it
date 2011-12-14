<?php	
	if(isset($project->period) && $project->period)
		$addValue = h(st($project->period));
	else if(isset($post['period']) && $post['period'])
		$addValue = h(st($post['period']));
	else
		$addValue = '';		
		
	if(isset($project->status) && $project->status)
		$status = h(st($project->status));
	else
		$status = 'temp';	
?>
<?php if((isset($status) && $status == 'temp')){ ?>
	<fieldset>
		<label for="" style="width: auto;">
			<?php if(isset($errors['period']) && $errors['period']) { ?><b style="color: red;">*</b><?php } ?>
			How long do you want to have the project up for?
		</label>
		<div class="clear10"></div>
		<small class="label">
			How long your project will be visible for pledgers<br />
		</small>
		<div class="clear10"></div>
		<span class="label">Weeks</span>
		<select id="insert_period" class="small focus-style" name="period">
			<?php /*<option value="1"<?php if($addValue/7 == 1) echo ' selected="selected"'; ?>>1 Week</option>
			<option value="2"<?php if($addValue/7 == 2) echo ' selected="selected"'; ?>>2 Weeks</option>
			<option value="3"<?php if($addValue/7 == 3) echo ' selected="selected"'; ?>>3 Weeks</option>*/ ?>
			<option value="4"<?php if($addValue/7 == 4) echo ' selected="selected"'; ?>>4 Weeks</option>
			<option value="5"<?php if($addValue/7 == 5) echo ' selected="selected"'; ?>>5 Weeks</option>
			<option value="6"<?php if($addValue/7 == 6) echo ' selected="selected"'; ?>>6 Weeks</option>
			<option value="7"<?php if($addValue/7 == 7) echo ' selected="selected"'; ?>>7 Weeks</option>
			<option value="8"<?php if($addValue/7 == 8) echo ' selected="selected"'; ?>>8 Weeks</option>
			<option value="9"<?php if($addValue/7 == 9) echo ' selected="selected"'; ?>>9 Weeks</option>
			<option value="10"<?php if($addValue/7 == 10) echo ' selected="selected"'; ?>>10 Weeks</option>
		</select>
		<div class="clear"></div>
	</fieldset>
<?php } else { ?>	
	<fieldset>
		<label for="" style="width: auto;">
			Period: <?php echo ($addValue/7) ?> Weeks
		</label>
		<div class="clear"></div>
	</fieldset>
<?php } ?>