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
				<h3>Email details</h3>
			</dd>
			<dd>
				<label for="subject" class="long">Subject</label>
				<input type="text" id="subject" name="subject" class="long validate" value="<?php echo @$post['subject']?>"/>
			</dd>
			
			<dd>
				<label for="active" class="long">Active</label>
				
				<?php if(@$post['active'] == "2"){ ?>
					<select name="active" class="long validate" DISABLED>
						<option value="1" SELECTED>Active</option>
					</select>
					<input type="hidden" name="active" value="2">
				<?php } else { ?>
					<select name="active" class="long validate">
						<option value="" DISABLED>- Please select -</option>
						<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
						<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
					</select>
				<?php } ?>
			</dd>
			<dd>
				<label for="text" class="long">Text</label>
				<textarea name="text" id="text" class="mceEditor" style="width: 630px; height: 300px;"><?php echo @$post['text']?></textarea>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>