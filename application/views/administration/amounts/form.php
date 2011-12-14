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
				<h3>Amount details</h3>
			</dd>
			<dd>
				<label for="amount" class="long">Amount</label>
				<input type="text" id="amount" name="amount" class="long validate" value="<?php echo @$post['amount']?>" />
			</dd>
			<dd>
				<label for="title" class="long">Limited</label>
				<select name="limited" style="border: 1px solid #DAE1E7; padding: 3px;">
					<option value="yes" <?php if(@$post['limited'] == "yes"){ echo "SELECTED"; } ?>>Yes&nbsp;</option>
					<option value="no" <?php if(@$post['limited'] == "no"){ echo "SELECTED"; } ?>>No</option>
				</select>
			</dd>
			<dd>
				<label for="number" class="long">Number</label>
				<input type="text" id="number" name="number" class="long" value="<?php echo @$post['number']?>"/>
			</dd>
			<dd>
				<label for="description" class="long">Description</label>
				<textarea name="description" class="validate" id="description" style="width: 630px; height: 100px; border: 1px solid #DAE1E7;"><?php echo @$post['description']?></textarea>
			</dd>
			
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
				<input type="hidden" name="post_check" value="1">
			</dd>
		</dl>
	</fieldset>
</form>