<?php if(!isset($ajax)) { ?><div class="site-width"><?php } ?>
	<?php if(!isset($ajax)) { ?>
		<section class="view-inbox">
			
			<ul class="tabs">
				<li class="tab"><a title="Inbox" href="/messages/inbox/">Inbox</a></li>
				<li class="tab"><a title="Sent" href="/messages/sent/">Sent</a></li>
				<li class="button"><a title="Create New MESSAGE" href="/messages/send/" width="550" height="<?php echo SEND_MESSAGE_HEIGHT ?>" rel="prettyPhoto">Create New MESSAGE</a></li>
			</ul>
			<div class="clear"></div>
			<div class="listing">
	<?php } ?>
		<form class="global_form_messages" method="post" action="">
			<h1><b>New</b> message</h1>
			<?php
				if(!empty($errors)){
					foreach($errors as $error){
						echo '<span class="global_error">'.$error.'</span>';
					}
					echo '<div class="clear10"></div>';
				}
				
				if(!empty($success)){
					echo '<span class="global_success">'.$success.'</span>';
					echo '<div class="clear10"></div>';
				}
			?>
			<?php if(empty($success)){ ?>
				<fieldset>
					<label for="receiver">To</label>
					<input class="query" type="text" name="receiver" id="receiver" value="<?php if(!@$forward) echo h(st(@$post['receiver'])) ?>">
				</fieldset>
				<fieldset>
					<label for="send_title">Title</label>	
					<input class="query" id="send_title" type="text" name="title" value="<?php if(isset($post['title'])) echo h(st(@$post['title'])); else if(@$forward == TRUE) echo 'Fw: '.h(st(@$message['title'])); else if(isset($message['title'])) echo 'Re: '.h(st(@$message['title'])) ?>">
				</fieldset>
				<fieldset>
					<label for="send_message">Message</label>
					<textarea id="send_message" class="query" for="id" name="text" rows="5" cols="80"><?php if(isset($post['text'])) echo h(st(@$post['text'])); else if(isset($message)) echo  "\n\n\n----\non ".date("d/m/y H:i", strtotime(@$message['date_sent']))." from ".st(h(@$message['sender_username']))."\n&rsaquo;&rsaquo; " . h(st(@$message['text'])) . "\n----" ?></textarea>
				</fieldset>
				<input class="button" name="submit" type="submit" value="Send">
					<script>
						new function($) {
							$.fn.setCursorPosition = function(pos) {
								if ($(this).get(0).setSelectionRange) {
									$(this).get(0).setSelectionRange(pos, pos);
								} else if ($(this).get(0).createTextRange) {
									var range = $(this).get(0).createTextRange();
									range.collapse(true);
									range.moveEnd('character', pos);
									range.moveStart('character', pos);
									range.select();
								}
							}
						}(jQuery);

						$(document).ready(function(){
							$('#send_message').focus();
							$('#send_message').setCursorPosition(0);
						});
						$(function() {
							$( "#receiver" ).autocomplete({
								source: "/user/autocomplete/",
								minLength: 2,
								select: function( event, ui ) {}
							});
							
							<?php
								if(!empty($errors)){
							?>
								parent.$.prettyPhoto.refresh(<?php echo SEND_MESSAGE_HEIGHT ?> + <?php echo (int)count($errors) ?>*22);
							<?php } else if(!empty($success)){ ?>
								parent.$.prettyPhoto.refresh(150);
							<?php } ?>
						});
					</script>
			<?php } else { ?>
				<script>
					$(function() {
						parent.$.prettyPhoto.refresh(115);
						$('.button_close').click(function(){
							parent.$.prettyPhoto.close();
						});
					});
				</script>
				<input class="button button_close" name="submit" type="submit" value="Close">
			<?php } ?>
			<div class="clear"></div>
		</form>
		
		<?php if(!isset($ajax)) { ?></div><?php } ?>
		<div class="clear"></div>
	<?php if(!isset($ajax)) { ?></section><?php } ?>
<?php if(!isset($ajax)) { ?></div><?php } ?>