<?php	
	if(isset($project->pledge_more) && $project->pledge_more)
		$addValue = h(st($project->pledge_more));
	else if(isset($post['pledge_more']) && $post['pledge_more'])
		$addValue = h(st($post['pledge_more']));
	else
		$addValue = '';		
?>
<fieldset>
	<label for="" style="width: auto;">WOULD YOU LIKE TO ENABLE FUNDING TO CONTINUE BEYOND 100% IF YOU REACH THE TARGET WITHIN YOUR FUNDING TIMESCALE? (N.B. IF YOU HAVE A LIMITED NUMBER OF REWARDS AVAILABLE PLEASE MAKE SURE YOU LIMIT THEM IN THE REWARDS AVAILABILITY SECTION)</label>
	<div class="clear10"></div>
	<input type="radio" name="pledge_more" value="1"<?php if($addValue == 1) echo ' checked="checked"' ?>> Yes
	&nbsp;&nbsp;<input type="radio" name="pledge_more" value="0"<?php if($addValue == 0) echo ' checked="checked"' ?>> No
</fieldset>