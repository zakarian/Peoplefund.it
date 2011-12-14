<?php if(!isset($ajax)) { ?><div class="site-width"><?php } ?>
	<form class="global_form" method="post" action="">
		<h1>Reset your password</h1>
		<p>Please enter your email in the field below and we will send you instructions how to reset your password.</p>
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
					parent.$.prettyPhoto.refresh(<?php echo FORGOTTEN_HEIGHT ?> + <?php echo (int)count($errors) ?>*25);
				<?php } else { ?>
					parent.$.prettyPhoto.refresh(<?php echo FORGOTTEN_HEIGHT ?>);
				<?php } ?>
			});
		</script>
		<fieldset>
			<label for="forgotten_username_email">Email</label>
			<input autocomplete="off" class="query" id="forgotten_username_email" type="text" name="email" value="<?php echo h(st(@$post['email'])) ?>" placeholder="your email address ..." />
		</fieldset>
		<input class="button" type="submit" name="submit" value="Send new password" />
		<div class="clear"></div>
	</form>
	
<?php if(!isset($ajax)) { ?></div><?php } ?>