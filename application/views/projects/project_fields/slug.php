<?php	
	if(isset($project->slug) && $project->slug)
		$addValue = h(st($project->slug));
	else if(isset($post['slug']) && $post['slug'])
		$addValue = h(st($post['slug']));
	else
		$addValue = '';		
?>
<fieldset>
	<label for="insert_slug">
		<?php if(isset($errors['slug']) && $errors['slug']) { ?><b style="color: red;">*</b><?php } ?>
		Choose your customised URL
	</label>
	<div class="clear10"></div>
	<span class="label">http://www.peoplefund.it/</span>
	<input autocomplete="off" class="small focus-style" id="slug" type="text" name="slug" value="<?php echo $addValue ?>" />
	<div class="clear"></div>
</fieldset>	