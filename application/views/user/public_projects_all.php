<div class="site-width">
	<section class="view-profile">
		<?php include("public_menu.php"); ?>
		<div class="clear"></div>
	</section>
</div>

<section class="view-projects" style="padding-top: 0; margin-top: -10px;">		
	<div class="site-width">
		<div class="box-items">
			<span class="title left">
				Projects
			</span>
			<div class="clear"></div>
		</div>
		<?php if(!empty($projects)){ ?>
			<?php foreach($projects as $k => $project){ ?>
				<?php $delItem = 4; ?>
				<?php $attrItem = ' style="margin-bottom: 5px;"'; ?>
				<?php include('../application/views/templates/project.php') ?>
			<?php } ?>
		<?php } else { ?>
			<center>No projects found</center>
		<?php } ?>
		<div class="clear10"></div>
	</div>
</section>