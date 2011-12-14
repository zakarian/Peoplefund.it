	<div class="site-width">
		<section class="generic-page">
			<div class="white-box">
				<form id="add-pledge" class="global_form_1" method="post" action="/processor/send_preauthorization/">
					<input type="hidden" name="amount" value="<?php echo h(st($amount)) ?>">
					<input type="hidden" name="idproject" value="<?php echo h(st($idproject)) ?>">
					<input type="hidden" name="idamount" value="<?php echo h(st($idamount)) ?>">
					
					<p><?php echo h(st($user_data['username'])) ?>, you’ve chosen to pledge &pound;<?php echo h(st($amount)) ?>! If you like to help the project out even more by pledging more for this reward, please enter the amount here: &pound; <input style="float: none; width: 80px; margin: 5px 0 0 0;" type="text" name="new_amount" maxlength="4" class="query" value="" /></p>
					<p><input id="_checkbox_state_reward_" type="checkbox" name="state[reward]" value="no"> No reward thanks, I'll just pledge.</p>
					<p><input id="_checkbox_state_public_" type="checkbox" name="state[public]" value="no"> Pledge anonymously (N.b. your name will still be visible to the project owner)</p>
					<p><input style="margin-top: 6px;" class="button left" id="submit" type="button" value="Pledge" /> on GoCardless through a one-off <a href="/help/faqs/#1" title="More about one-off direct debits" onclick="this.target='_blank';">direct debit</a> which will only be taken from your account if the project raises 100% of their target.</p>
					
					<input class="button left hidden" id="hidden-submit" type="submit" value="Pledge" />
				</form>
				<div class="clear"></div>
				</div>

			<script type="text/javascript">
			var gocardlessWindow;

			$(document).ready(function(){
				$('#submit').click( function() {
					if(!gocardlessWindow || gocardlessWindow.closed) {
						gocardlessWindow = window.open('about:blank', 'gocardless', 'scrollbars=yes,menubar=no,height=600,width=1024,resizable=yes,toolbar=no,status=no');
					} else
						gocardlessWindow.focus();
					
					while(gocardlessWindow) {
						$('#add-pledge').get(0).setAttribute('target', 'gocardless');
						$('#hidden-submit').trigger('click');
						
						break;
					}
				});
			});
			</script>
		</section>
		<?php include('../application/views/templates/widget-most-projects.php') ?>
		<div class="clear"></div>
	</div>