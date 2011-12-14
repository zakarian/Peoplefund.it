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
					<li class="tab"><a title="Alerts" href="/notifications/">Alerts</a></li>
					<?php /*<li class="tab"><a title="Settings" href="/notifications/settings/">Settings</a></li>*/ ?>	
				</ul>
				<div class="clear"></div>
				<div class="listing">
						<?php if(!empty($notifications)){ ?>
						<form action="/notifications/update/" method="POST">
							<div class="notifications">
								<?php foreach($notifications as $k=>$notify){ ?>
									<div class="item<?php echo ($k%2 == 0) ? ' odd' : ' add' ?>">
										<?/*<div class="object_type"><?php echo h(st($notify->object_type)) ?></div>*/?>
										<div class="object_role"><?php echo h(st($notify->object_role)) ?></div>
										<div class="object_title"><?php echo h(st($notify->object_title)) ?></div>
										<?/*<div class="object_description"><?php echo h(st($notify->object_description)) ?></div>*/?>
										<div class="event_type"><?php echo h(ucfirst(str_replace('_', ' ', $notify->event_type))) ?></div>	
										<div class="notification_type">
											<?php echo ucfirst($notify->notification_type); ?>
											<?php /*
											<select name="events[<?php echo $notify->id?>]" id="events_<?php echo $notify->id?>">
												<option value="never"<?php if ($notify->notification_type == 'never'){?> selected="selected"<?php } ?>>Never</option>
												<option value="instant"<?php if ($notify->notification_type == 'instant'){?> selected="selected"<?php } ?>>Instant</option>
												<option value="daily"<?php if ($notify->notification_type == 'daily'){?> selected="selected"<?php } ?>>Daily</option>
												<option value="weekly"<?php if ($notify->notification_type == 'weekly'){?> selected="selected"<?php } ?>>Weekly</option>
												<option value="monthly"<?php if ($notify->notification_type == 'monthly'){?> selected="selected"<?php } ?>>Monthly</option>
											</select>
											*/ ?>
										</div>
											<?php /*
											<div class="notification_delete"><a href="/notifications/delete/<?php echo $notify->id?>">Delete</a></div>
											*/ ?>
										<div class="clear"></div>
									</div>
								<?php } ?>
									<?php /*
									<input type="submit" class="submit" value="Save Changes" name="submit" />	
									*/ ?>
								<div class="clear"></div>
							</div>
						</form>		
						<?php } else { ?>
							<i>Nothing to configure yet</i>
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


