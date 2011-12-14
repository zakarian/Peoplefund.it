<?php if(!isset($ajax)) { ?><div class="site-width"><?php } ?>
	<form class="global_form" method="post" action="">
		<h1>Sign up</h1>
		<p>Registering to be part of peoplefund doesn’t take long. Start by completing this short form.</p>
		<?php
		if(!empty($errors)){
			foreach($errors AS $error){
				echo '<span class="global_error">'.$error.'</span>';
			}
			echo '<div class="clear10"></div>';
		}
		?>
		<script>
			$(function() {
				<?php if(!empty($errors)){ ?>
					parent.$.prettyPhoto.refresh(<?php echo SIGNUP_HEIGHT ?> + <?php echo (int)count($errors) ?>*25);
				<?php } else { ?>
					parent.$.prettyPhoto.refresh(<?php echo SIGNUP_HEIGHT ?>);
				<?php } ?>
			});
		</script>
		<fieldset>
			<label for="register_username">Username</label>
			<input autocomplete="off" class="query" id="register_username" type="text" name="username" value="<?php echo h(st(@$post['username'])) ?>" placeholder="your username ..." />
		</fieldset>
		<fieldset>
			<label for="register_email">Email</label>
			<input autocomplete="off" class="query" id="register_email" type="text" name="email" value="<?php echo h(st(@$post['email'])) ?>" placeholder="your email address ..." />
		</fieldset>
		<fieldset>
			<label for="register_password">Password</label>
			<input autocomplete="off" class="query" id="register_password" type="password" name="password" value="<?php echo h(st(@$post['password'])) ?>" placeholder="password" />
		</fieldset>	
		<fieldset>
			<label for="register_password_repeat">Password</label>
			<input autocomplete="off" class="query" id="register_password_repeat" type="password" name="password_repeat" value="<?php echo h(st(@$post['password_repeat'])) ?>" placeholder="password" />
		</fieldset>	
		<fieldset>
			<label>or</label>
			<a href="javascript:;" id="fb-login"><img border="0" alt="Connect with Facebook" src="/img/site/facebook.gif"></a>
		</fieldset>
		<fieldset>
			<label>or</label>
			<a href="/connect/to/service:energyshare/<?php if(isset($ajax)) echo 'ajax/' ?>" id="es-login">Connect with energyshare</a>
		</fieldset>
		<input class="button" type="submit" name="submit" value="Sign Up" />
		<div class="clear"></div>
	</form>
	
<?php if(!isset($ajax)) { ?></div><?php } ?>