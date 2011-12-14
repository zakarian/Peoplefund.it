<div class="site-width">
	<section class="view-profile">
		<?php $tab = 'messages'; ?>
		<?php include('../application/views/user/public_menu.php'); ?>
		<div class="clear"></div>
		<div class="box">
			<section class="view-inbox">
				<form class="read_message" method="post" action="">
					<?php if($data['idsender'] <> $user_data['iduser']) { ?>
						<fieldset>
							<label for="receiver">From</label>
							<a href="/user/<?php echo h(st(@$data['sender_username'])); ?>/" title="<?php echo h(st(@$data['sender_username'])); ?>"><?php echo h(st(@$data['sender_username'])); ?></a>
						</fieldset>
					<?php } else { ?>
						<fieldset>
							<label for="receiver">To</label>
							<a href="/user/<?php echo h(st(@$data['receiver_username'])); ?>/" title="<?php echo h(st(@$data['receiver_username'])); ?>"><?php echo h(st(@$data['receiver_username'])); ?></a>
						</fieldset>
					<?php } ?>
					<fieldset>
						<label for="receiver">Title</label>
						<?php echo h(st(@$data['title'])); ?>
					</fieldset>
					<fieldset>
						<label for="sent_time">Sent</label>	
						<?php echo h(st(date("d/m/y H:i", strtotime($data['date_sent'])))); ?>
					</fieldset>
					<fieldset>
						<div class="box"><?php echo nl2br(@$data['text']) ?></div>
					</fieldset>
					<ul class="tabs">
						<?php if($data['idsender'] <> $user_data['iduser']) { ?>
							<li class="button right"><a title="Reply" href="/messages/send/<?php echo $data['idsender']; ?>/reply-<?php echo $data['idmessage']; ?>/" width="550" height="<?php echo SEND_MESSAGE_HEIGHT ?>" rel="prettyPhoto">Reply</a></li>
							<li class="button right"><a title="FORWARD" href="/messages/send/<?php echo $data['idsender']; ?>/forward-<?php echo $data['idmessage']; ?>/" width="550" height="<?php echo SEND_MESSAGE_HEIGHT ?>" rel="prettyPhoto">FORWARD</a></li>
						<?php } ?>
						<li class="button delete"><a title="DELETE" href="/messages/delete/<?php echo $data['idmessage']; ?>/" id="btn_delete_many">DELETE</a></li>
					</ul>
					<div class="clear"></div>
				</form>
			</section>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</section>
</div>