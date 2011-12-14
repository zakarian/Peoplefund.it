<?php
	if(isset($project->skills) && $project->skills)
		$addValue = unserialize($project->skills);
	else if(isset($post['skills']) && $post['skills'])
		$addValue = unserialize($post['skills']);
	else
		$addValue = '';		
?>
<fieldset class="fieldset-skills">
	<label for="skills">
		<?php if(isset($errors['skills']) && $errors['skills']) { ?><b style="color: red;">*</b><?php } ?>
		Skills
	</label>
	<div class="clear5"></div>
	<small class="label">
		If your also interested in seeking support for your project through people's skills, please list them here?<?php /* You can find out more about how this works <a href="" title="">here</a>. */ ?>
	</small>
	<div class="clear10"></div>
	<div id="skills">
		<?php if($addValue) {
			foreach($addValue as $k => $skill){ ?>
			<div id="<?php echo (time() + $k) ?>">
				<input type="text" class="query focus-style" style="width: 300px; float: left;" name="skills[]" value="<?php echo $skill ?>">
				&nbsp;<a href="javascript:;" style="text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;" title='Remove' onClick="removeSkills('<?php echo (time() + $k) ?>');">&nbsp;<img src="/img/site/delete_16.png" alt="" /></a>
				<div class="clear"></div>
			</div>
			<div class="clear10"></div>	
		<?php } } ?>
	</div>	
	<input autocomplete="off" class="rounded" id="add_skills" type="text" name="skills_temp" value="" maxlength="30" />
	<input class="verify" name="button" type="button" value="ADD" onClick="addSkills();" />
	<div class="clear10"></div>
</fieldset>	
<script>
	function addSkills(){
		var skills = $("#add_skills").val();
		if(skills == '') return;
		
		var html;
		html = "<div id='"+Number(new Date())+"'>";
		html += "<input type='text' class='query focus-style' style='width: 300px; float: left;' name='skills[]' readonly='readonly' value='"+skills+"'>";
		html += "&nbsp;<a href='javascript:;' style='text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;' title='Remove' onClick=\"removeSkills('"+Number(new Date())+"');\">&nbsp;<img src='/img/site/delete_16.png'></a><div class='clear10'></div>	";
		html += "</div>";
		
		$("#skills").append(html);
		$("#add_skills").val('');
	}
	
	function removeSkills(skill){
		$('#'+skill).remove();
	}
</script>