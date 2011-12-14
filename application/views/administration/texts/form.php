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
				<h3>Text details</h3>
			</dd>
			<dd>
				<label for="key" class="long">Key</label>
				<input type="text" id="key" name="key" class="long validate" value="<?php echo h(st(@$post->key)) ?>"<?php echo ($action == 'edit') ? ' disabled="disabled"' : '' ?> />
			</dd>
			<dd>
				<label for="text" class="long">Value</label>
				<textarea style="height: 300px;" class="long validate" name="text" id="text" cols="" rows=""><?php echo @$post->text ?></textarea>
			</dd>
			<dd>
				<label for="url" class="long">URL</label>
				<input type="text" id="url" name="url" class="long validate" value="<?php echo h(st(@$post->url)) ?>"<?php echo ($action == 'edit') ? ' disabled="disabled"' : '' ?> />
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>