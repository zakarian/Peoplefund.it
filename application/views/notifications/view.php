<div class="site-width">
	<section class="view-profile">
		<?php $tab = 'notifications'; ?>
		<?php include('../application/views/user/public_menu.php'); ?>
		<div class="clear"></div>
		<div class="box">
			<section class="view-inbox">
				<form class="read_message" method="post" action="">
					<h1><b>Read</b> notification</h1>
					<fieldset>
						<label style="width: 100px;" for="receiver">Event type</label>
						<b>						
						<?php if ($data['event_type'] == 'status_change' && $project->status == 'closed') : ?>
								<?php if (isset($project_successful) && ($project_successful == 'true')) : ?>
									The project was successfully completed
								<?php else : ?>
									The project ended unsuccessfully
								<?php endif; ?>
						<?php else : ?>
							<?php echo ucfirst(strtolower($data['object_type'])); ?>
							<?php echo strtolower(str_replace('_', ' ', $data['event_type'])); ?> 
						<?php endif; ?>
						</b>
					</fieldset>
					<fieldset>
						<label style="width: 100px;" for="sent_time">Occurred at</label>	
						<?php echo h(st(date("d/m/y H:i", strtotime($data['event_time'])))); ?>
					</fieldset>
					<fieldset>
						<label style="width: 100px;" for="send_title">Project name</label>	
						<?php
							$attrUrl = '';
							if($data['event_type'] == 'status_change')
								$attrUrl = '';
							else if($data['event_type'] == 'comment')
								$attrUrl = 'comments/';	
							else if($data['event_type'] == 'update')
								$attrUrl = 'comments/';	
						?>
						<a href="/<?php echo h(st(@$data['object_slug'])); ?>/<?php echo h(st($attrUrl)) ?>" target="_blank"><?php echo h(st($data['object_title'])); ?></a>
					</fieldset>
					<?php if($data['object_text']) { ?>
						<fieldset>
							<div class="box"><?php echo nl2br(@$data['object_text']) ?></div>
						</fieldset>
					<?php } ?>
					<div class="clear"></div>
				</form>
			</section>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</section>
</div>