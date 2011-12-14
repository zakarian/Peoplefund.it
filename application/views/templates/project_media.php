<?php
	if(isset($top_project) && $top_project)
		$project = $top_project;
?>
<div class="vzaar_media_player">
	<?php if(!empty($project->vzaar_idvideo) && $project->vzaar_processed <> 0){ ?>
		<object type="application/x-shockwave-flash" data="https://view.vzaar.com/<?php echo h(st($project->vzaar_idvideo)) ?>.flashplayer">
			<param name="movie" value="https://view.vzaar.com/<?php echo $project->vzaar_idvideo?>.flashplayer">	
			<param name="allowScriptAccess" value="always">
			<param name="allowFullScreen" value="true">
			<param name="wmode" value="transparent">
			<param name="flashvars" value="border=none">
			<embed src="https://view.vzaar.com/<?php echo h(st($project->vzaar_idvideo)) ?>.flashplayer" type="application/x-shockwave-flash" wmode="transparent" allowScriptAccess="always" allowFullScreen="true" flashvars="border=none">
			<video src="https://view.vzaar.com/<?php echo h(st($project->vzaar_idvideo)) ?>.mobile" poster="https://view.vzaar.com/<?php echo h(st($project->vzaar_idvideo)) ?>.image" controls onclick="this.play();"></video>
		</object>
	<?php } else if(!empty($project->embed) && !empty($project->ext)){ 
		$project->embed = str_replace('http://', 'https://',$project->embed);
		echo $project->embed;
	} else if(!empty($project->ext) && file_exists('/uploads/projects/'.$project->idproject.'.'.$project->ext)) { ?>	
		<img src="/uploads/projects/<?php echo h(st($project->idproject)) ?>.<?php echo h(st($project->ext)) ?>" alt="" />
	<?php } else { ?>
		<img src="<?php echo DEFAULT_PROJECT_IMAGE ?>" alt="" />
	<?php } ?>	
</div>