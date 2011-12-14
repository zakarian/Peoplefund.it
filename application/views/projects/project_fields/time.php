<?php	
	if(isset($project->time) && $project->time)
		$addValue = h(st($project->time));
	else if(isset($post['time']) && $post['time'])
		$addValue = h(st($post['time']));
	else
		$addValue = '';		
?>
<fieldset class="fieldset-time">
	<label for="time">
		<?php if(isset($errors['time']) && $errors['time']) { ?><b style="color: red;">*</b><?php } ?>
		Time
	</label>
	<div class="clear5"></div>
	<small class="label">
		If your also interested in seeking support for your project through people's time, how many hours in total are you seeking?<?php /* You can find out more about how this works <a href="" title="">here</a>. */ ?>
	</small>
	<div class="clear5"></div>
	<input type="text" name="time" id="time" value="<?php echo $addValue ?>" autocomplete="off" class="query focus-style" maxlength="4" />
	<div class="clear10"></div>
</fieldset>	