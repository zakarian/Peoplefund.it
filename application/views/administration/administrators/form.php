<form method="POST" action="" class="mainForm">
	<fieldset>
		<dl>
			<?php if(!empty($errors)): ?>
					<dd class="error">
						<strong>Errors:</strong><br />
						<?php foreach($errors as $e): ?>
									&nbsp;&nbsp;<img src="<?php echo site_url('img/bullet3.gif'); ?>" alt="" />&nbsp;<?php echo ucfirst($e) ?><br />
						<?php endforeach; ?>
					</dd>
			<?php endif; ?>

			<dd>
				<h3>Administrator details</h3>
			</dd>
			<dd>
				<label for="username" class="long">Username</label>
				<input type="text" id="username" name="username" class="long validate" value="<?php echo @$post['username']?>" <?php if(@$action == "edit"){ ?>DISABLED<?php } ?>/>
			</dd>
			<dd>
				<label for="password" class="long">Password</label>
				<input type="password" id="password" name="password" class="long <?php if(@$action != "edit"){ ?>validate<?php } ?>" value=""/>
			</dd>
			<dd>
				<label for="password_repeat" class="long"><span>Password confirm</span></label>
				<input type="password" id="password_repeat" name="password_repeat" class="long <?php if(@$action != "edit"){ ?>validate<?php } ?>" value="" />
			</dd>
			<dd>
				<label for="email" class="long">Email</label>
				<input type="email" id="email" name="email" class="long validate" value="<?php echo @$post['email']?>"/>
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				<select name="active" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
					<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
				</select>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
				<input type="hidden" name="post_check" value="1">
			</dd>
		</dl>
	</fieldset>
</form>