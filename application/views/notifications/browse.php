<div class="site-width">
	<section class="view-profile">
		<?php $tab = 'notifications'; ?>
		<?php include('../application/views/user/public_menu.php'); ?>
		<div class="clear"></div>
		<div class="box">
			<section class="view-inbox">
				<?php if (!empty($_SESSION['info']['new_notifications'])){ ?>
					<span class="title">You have <b><?php echo $_SESSION['info']['new_notifications']; ?> new</b> alert<?php if ($_SESSION['info']['new_notifications'] != 1){ echo 's'; } ?></span>
				<?php } ?>
				<ul class="tabs">
					<li class="tab"><a class="active" title="Alerts" href="/notifications/">Alerts</a></li>
					<?php /*<li class="tab"><a title="Settings" href="/notifications/settings/">Settings</a></li>*/ ?>
				</ul>
				<div class="clear"></div>
				<div class="listing">
					<?php if(!empty($notifications)){ ?>

						<form action="/notifications/delete_many/" method="post" id="del_all_form">
						
							<div class="message_listing">
								<?php 
									
									foreach($notifications as $notification){  ?>
									<?php //print_r($notification) ?>
									<div class="row message_<?php if (empty($notification->read_on)) echo 'new'; else echo 'old'; ?>_row">
										<div class="alert_<?php if (empty($notification->read_on)) echo 'new'; else echo 'old'; ?>"></div>
										<div class="date">
											<?php echo date("d M Y", strtotime($notification->event_time)); ?>
										</div>
										<div class="from">
											<?php if(!$notification->from_member_id) { ?>
												<?php if($notification->from_member_id === '0') : ?>
												peoplefund.it team
												<?php else : ?>
												<i>removed user</i>
												<?php endif; ?>	
											<?php } else { ?>
												<a href="/user/<?php echo h(st($notification->from_member_slug)); ?>/" title="<?php echo h(st($notification->from_member_username)); ?>"><?php echo h(st($notification->from_member_username)); ?></a>
											<?php } ?>
										</div>
										<div style="width: 145px;" class="from">
											<?php if (strtolower($notification->event_type) == 'comment') : ?>
											commented on project
											<?php elseif (strtolower($notification->event_type) == 'update') : ?>
											posted project update
											<?php elseif (strtolower($notification->event_type) == 'status_change') : ?>
											project status change
											<?php endif; ?>
											
										</div>
										<div style="width: 200px;" class="from">
											<?php
												$attrUrl = '';
												if($notification->event_type == 'status_change')
													$attrUrl = '';
												else if($notification->event_type == 'comment')
													$attrUrl = 'comments/';	
												else if($notification->event_type == 'update')
													$attrUrl = 'comments/';	
											?>
											<a href="/<?php echo h(st($notification->object_slug)); ?>/<?php echo h(st($attrUrl)) ?>" title="<?php echo h(st($notification->object_title)); ?>"><?php echo h(st($notification->object_title)); ?></a>
										</div>
										<?php if($notification->object_text) { ?>
											<div style="width: 265px;" class="view">
												<a href="/notifications/read/<?php echo h(st($notification->id)); ?>/" title="<?php echo st($notification->object_title); ?>"><?php echo st($notification->object_text); ?></a>
											</div>
										<?php } else { ?>
											<div style="width: 265px;" class="view">
												<a href="/notifications/read/<?php echo h(st($notification->id)); ?>/" title="<?php echo h(st($notification->object_title)); ?>"><i>- details -</i></a>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						</form>
						
					<?php } else { ?>
						<div class="empty">No notifications</div>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</section>
		</div>
	</section>
</div>

<script>
	$('#delete_all_msg').bind('click', function(){
		$('.message_listing :checkbox').attr('checked', $('#delete_all_msg').attr('checked'));
	});
	$('#btn_delete_many').bind('click', function(){
		$('#del_all_form').submit();
	})
</script>