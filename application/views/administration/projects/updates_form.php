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
				<h3>Update details</h3>
			</dd>
			<?php /*<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$post['title']?>"/>
			</dd>*/ ?>
			<dd>
				<label for="text" class="long">Text</label>
				<textarea name="text" id="text" class="mceEditor" style="width: 630px; height: 200px;"><?php echo @$post['text']?></textarea>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>