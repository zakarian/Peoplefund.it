<div class="site-width">
	<div class="generic-form">
		<form class="global_form_1" method="post" action="/user/new_password/<?php echo $id_encoded; ?>/<?php echo $hash; ?>/">
			<h1>Set up your new password</h1>
			<?php
			if($error != 'ok'){
				echo '<span class="global_error">'.$error.'</span>';
				echo '<div class="clear10"></div>';
			}
			?>
			<fieldset>
				<label for="update_password">New Password</label>
				<input autocomplete="off" class="query focus-style" id="update_password" type="password" name="password" value="" />
			</fieldset>	
			<fieldset>
				<label for="update_password_repeat">Confirm New password</label>
				<input autocomplete="off" class="query focus-style" id="update_password_repeat" type="password" name="password_repeat" value="" />
			</fieldset>	
			<input class="button" name="submit" type="submit" value="Save new password">
			<div class="clear"></div>
		</form>
	</div>
</div>