<?php	
	if(isset($project->title) && $project->title)
		$addValue = $project->title;
	else if(isset($post['title']) && $post['title'])
		$addValue = $post['title'];
	else
		$addValue = '';		
?>
<fieldset>
	<label for="title">
		<?php if(isset($errors['title']) && $errors['title']) { ?><b style="color: red;">*</b><?php } ?>
		The name of your project
	</label>
	<div class="clear10"></div>
	<input type="text" name="title" id="title" value="<?php echo $addValue ?>" autocomplete="off" class="query focus-style" onBlur="fillSlug('title', 'slug')">
	<div class="clear10"></div>
</fieldset>	