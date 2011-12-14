<?php	
	if(isset($project->helpers) && $project->helpers)
		$addValue = h(st($project->helpers));
	else if(isset($post['helpers']) && $post['helpers'])
		$addValue = h(st($post['helpers']));
	else
		$addValue = '';		
?>
<fieldset>
	<label class="small focus-style" for="" style="width: auto;">Would you like to ask people if they would also like to support your project with their time and / or skills after they have pledged to fund your project?</label>
	<div class="clear10"></div>
	<input class="helpers-yes" type="radio" name="helpers" value="1"<?php if($addValue == 1) echo ' checked="checked"' ?>> Yes
	&nbsp;&nbsp;<input class="helpers-no" type="radio" name="helpers" value="0"<?php if($addValue == 0) echo ' checked="checked"' ?>> No
</fieldset>
<script>
	$(document).ready(function() { 
		$('.helpers-yes').click(function() {
			$('.fieldset-time').show();
			$('.fieldset-skills').show();
			$(this).attr('checked', true);
		});
		$('.helpers-no').click(function() {
			$('.fieldset-time').hide();
			$('.fieldset-skills').hide();
			$(this).attr('checked', true);
		});
		<?php if($addValue == 1) { ?>
			$('.fieldset-time').show();
			$('.fieldset-skills').show();
		<?php } else { ?>
			$('.fieldset-time').hide();
			$('.fieldset-skills').hide();
		<?php } ?>
	});
</script>