<?php	
	if(isset($project->outcome) && $project->outcome)
		$addValue = $project->outcome;
	else if(isset($post['outcome']) && $post['outcome'])
		$addValue = $post['outcome'];
	else
		$addValue = '';		
?>
<fieldset>
	<label for="insert_outcome">
		<?php if(isset($errors['outcome']) && $errors['outcome']) { ?><b style="color: red;">*</b><?php } ?>
		Aim
	</label>
	<div class="clear10"></div>
	<small class="label">
		Please explain what your project aims to do in no more than 100 words. To make it clear you could start 'We aim to…' or 'We will…'. Top tip: projects that have clear, tangible, distinctive aims are far, far more likely to get funded. Only the first 97 characters will show up in the search results – so make sure they grab people's interest!
	</small>
	<div class="clear10"></div>
	<small class="label">(<span id="outcome_counter">100</span> words remaining)</small>
	<div class="clear10"></div>
	<textarea style="width: 711px; padding: 10px; height: 50px; border: 1px solid silver;" id="outcome" name="outcome" onKeyUp="limitTextAreaWords('outcome', 'outcome_counter', 100);" onKeyDown="limitTextAreaWords('outcome', 'outcome_counter', 100);"><?php echo $addValue ?></textarea>
	<div class="clear10"></div>
</fieldset>	