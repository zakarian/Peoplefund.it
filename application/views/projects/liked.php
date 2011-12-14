		<?php 	
			$this->projects->limit = 4; 
			$related_projects = $this->projects->get_liked_2_liked_projects_home($project->idproject); 
		?>
		<?php if($related_projects) { ?>
			<section class="view-projects">	
				<div class="site-width">
					<span class="people-liked-this-project">People who watched this project also watched</span>
					<?php foreach($related_projects as $k => $project){ ?>
						<?php $delItem = 4; ?>
						<?php $attrItem = ''; ?>
						<?php include('../application/views/templates/project.php') ?>
					<?php } ?>
				</div>
				<div class="clear"></div>	
			</section>
		<?php } ?>