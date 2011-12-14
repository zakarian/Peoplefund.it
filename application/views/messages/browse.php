<div class="site-width">
	<section class="view-profile">
		<?php $tab = 'messages'; ?>
		<?php include('../application/views/user/public_menu.php'); ?>
		<div class="clear"></div>
		<div class="box">
			<section class="view-inbox">
				<?php if (!empty($_SESSION['info']['new_messages'])){ ?>
					<span class="title">You have <b><?php echo $_SESSION['info']['new_messages']; ?> new</b> message<?php if ($_SESSION['info']['new_messages'] != 1){ echo 's'; } ?></span>
				<?php } ?>
				<ul class="tabs">
					<li class="tab"><a<?php if($direction == 'inbox'){ ?> class="active"<?php } ?> title="Inbox" href="/messages/inbox/">Inbox</a></li>
					<li class="tab"><a<?php if($direction != 'inbox'){ ?> class="active"<?php } ?> title="Sent" href="/messages/sent/">Sent</a></li>
					<li class="button"><a title="Create New MESSAGE" href="/messages/send/" width="550" height="<?php echo SEND_MESSAGE_HEIGHT ?>" rel="prettyPhoto">Create New MESSAGE</a></li>
					<li class="button delete"><input type="checkbox" name="delete_all_msg" id="delete_all_msg"/></li>
					<li class="button delete"><a title="DELETE" href="javascript:;" id="btn_delete_many">DELETE</a></li>
				</ul>
				<div class="clear"></div>
				<div class="listing">
					<?php if(!empty($messages)){ ?>

						<form action="/messages/delete_many/" method="post" id="del_all_form">
						<?php if($direction == 'inbox'){ ?>
							<div class="message_listing">
								<?php foreach($messages as $message){ ?>
									<div class="row message_<?php echo $message->status_receiver; ?>_row">
										<div class="inmail_<?php echo h(st($message->status_receiver)) ?>"></div>
										<div class="from">
											<?php if(!empty($message->sender_username)) { ?>
												<a title="send message to '<?php echo h(st($message->sender_username)); ?>'" href="/messages/send/<?php echo h(st($message->idsender)); ?>/" rel="prettyPhoto" width="550" height="460">		
													<?php echo h(st($message->sender_username)) ?>
												</a>
											<?php } else { ?>
												<i>removed user</i>
											<?php } ?>
										</div>
										<div class="date">
											<?php echo date("d M y", strtotime($message->date_sent)); ?>
										</div>
										<div class="view">
											<a title="Read message" href="/messages/read/<?php echo $message->idmessage; ?>/">
												<?php echo h(st($message->title)) ?>
											</a>
										</div>
										<div class="checkbox">
											<input type="checkbox" name="delete_msg[<?php echo h(st($message->idmessage)) ?>]" value="1">
										</div>
									</div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<div class="message_listing">
								<?php foreach($messages AS $message){  ?>
									<div class="row message_<?php echo $message->status_sender; ?>_row">
										<div class="inmail_<?php echo h(st($message->status_sender)) ?>"></div>
										<div class="from">
											<?php if(!empty($message->receiver_username)) { ?>	
												<?php echo h(st($message->receiver_username)) ?>
											<?php } else { ?>
												<i>removed user</i>
											<?php } ?>
										</div>
										<div class="date">
											<?php echo date("d M y", strtotime($message->date_sent)); ?>
										</div>
										<div class="view">
											<a title="Read message" href="/messages/read/<?php echo $message->idmessage; ?>/">
												<?php echo h(st($message->title)) ?>
											</a>
										</div>
										<div class="checkbox">
											<input type="checkbox" name="delete_msg[<?php echo h(st($message->idmessage)) ?>]" value="1">
										</div>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
						</form>
						
					<?php } else { ?>
						<div class="empty">No messages</div>
					<?php } ?>
			</section>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
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