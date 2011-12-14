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
				<?php if(!empty($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
					Projects that you've started
				<?php } else { ?>
					Projects that user started
				<?php } ?>
			</span>
			<?php if(!empty($started)){ ?>
				<a class="see_all" href="/user/<?php echo h(st($user->slug)) ?>/projects/started/" title="SEE ALL">SEE ALL &rsaquo;</a>	
			<?php } ?>
			<?php /* if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
				<a class="see-all" href="/projects/checklist/" title="Start a project">Start a project</a>
			<?php } */ ?>
			<div class="clear"></div>
		</div>
		<?php if(!empty($started)){ ?>
			<?php foreach($started as $k => $project){ ?>
				<?php $delItem = 4; ?>
				<?php $attrItem = ''; ?>
				<?php 
						if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']) {
							$haveStatusBadge = TRUE;
						}
					?>
				<?php include('../application/views/templates/project.php') ?>
			<?php } ?>
		<?php } else { ?>
			<center>No projects found</center>
		<?php } ?>
		<div class="clear10"></div>
		
		<?php if(isset($backed)){ ?>
			<div class="box-items">
				<span class="title left">
					<?php if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
						Projects that you've backed
					<?php } else { ?>
						Projects that user backed
					<?php } ?>
				</span>
				<?php if(!empty($backed)){ ?>
					<a class="see_all" href="/user/<?php echo h(st($user->slug)) ?>/projects/backed/" title="SEE ALL">SEE ALL &rsaquo;</a>
				<?php } ?>
				<?php if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
					<a class="see-all" href="/projects/" title="Search for a project to back">Search for a project to back</a>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<?php if(!empty($backed)){ ?>
				<?php foreach($backed as $k => $project){ ?>
					<?php $delItem = 4; ?>
					<?php $attrItem = ''; ?>
					<?php 
						if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']) {
							$haveStatusBadge = FALSE;
							$haveStatusBadgeBacked = TRUE;
						}
					?>
					<?php include('../application/views/templates/project.php') ?>
				<?php } ?>
			<?php } else { ?>
				<center>No projects found</center>
			<?php } ?>
			<div class="clear10"></div>
		<?php } ?>
		
		<?php if(isset($watched)){ ?>
			<div class="box-items">
				<span class="title left">
					<?php if(!empty($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
						Projects that you're watching
					<?php } else { ?>
						Projects that user is watching
					<?php } ?>
				</span>
				<?php if(!empty($watched)){ ?>
					<a class="see_all" href="/user/<?php echo h(st($user->slug)) ?>/projects/watched/" title="SEE ALL">SEE ALL &rsaquo;</a>	
				<?php } ?>
				<?php if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
					<a class="see-all" href="/projects/" title="Search for a project to watch">Search for a project to watch</a>
				<?php } ?>
				<div class="clear"></div>
			</div>
			<?php if(!empty($watched)){ ?>
				<?php foreach($watched as $k => $project){ ?>
					<?php $delItem = 4; ?>
					<?php $attrItem = ''; ?>
					<?php 
						if(isset($_SESSION['user']['iduser']) && $user->iduser == $_SESSION['user']['iduser']) {
							$haveStatusBadge = TRUE;
						}
					?>
					<?php include('../application/views/templates/project.php') ?>
				<?php } ?>
			<?php } else { ?>
				<center>No projects found</center>
			<?php } ?>
			<div class="clear10"></div>
		<?php } ?>
	
		
	</div>
</section>