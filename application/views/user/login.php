<?php if(!isset($ajax)) { ?><div class="site-width"><?php } ?>
	<form class="global_form" method="post" action="">
		
		<fieldset>
			<h1 class="left">Login</h1>
			<div class="signup-login-text">or</div>
			<input style="margin-top: 3px;" onclick="window.location = '/user/sign_up/<?php if(isset($ajax)) echo 'ajax/' ?>'; return false;" class="button left" type="submit" name="submit" value="Sign-up" />
			<div class="clear"></div>
		</fieldset>
		
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
					parent.$.prettyPhoto.refresh(<?php echo LOGIN_HEIGHT ?> + <?php echo (int)count($errors) ?>*28);
				<?php } ?>
				
				// When press enter send button will be used
				$('input.query').keypress(function(e){
					if(e.which == 13){
						$('#send-button').click();
						e.preventDefault();
						return false;
					}
				});
			});
			
			
		</script>
		<fieldset>
			<label for="login_username_email">Email</label>
			<input autocomplete="off" class="query" type="text" id="login_username_email" name="username_email" value="<?php echo h(st(@$post['username_email'])) ?>" placeholder="your email address ..." />
		</fieldset>
		<fieldset>
			<label for="login_password">Password</label>
			<input autocomplete="off" class="query" type="password" id="login_password" name="password" placeholder="password" />
		</fieldset>
		<fieldset>
			<input class="link" type="checkbox" name="autologin" id="autologin" value="1" /> Keep me logged in
		</fieldset>
		<fieldset>
			<label>or</label>
			<a href="javascript:;" id="fb-login"><img border="0" alt="Connect with Facebook" src="/img/site/facebook.gif"></a>
		</fieldset>
		<fieldset>
			<label>or</label>
			<a href="/connect/to/service:energyshare/<?php if(isset($ajax)) echo 'ajax/' ?>" id="es-login">Connect with energyshare</a>
		</fieldset>
		<a class="forgotten" href="/user/forgotten_pass/<?php if(isset($ajax)) echo 'ajax/' ?>" title="Forgotten your password?">Forgotten your password?</a>
		
		<input id="send-button" class="button" type="submit" name="submit" value="Send" />
		<div class="clear"></div>
	</form>
	
<?php if(!isset($ajax)) { ?></div><?php } ?>