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
				<h3>Category details</h3>
			</dd>
			<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$category->title?>"/>
			</dd>
			<dd>
				<label for="slug" class="long">Slug</label>
				<input type="text" id="slug" name="slug" class="long validate" value="<?php echo @$category->slug?>"/>
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				
					<select name="status" class="long validate">
						<option value="" DISABLED>- Please select -</option>
						<option value="1" <?php if(@$category->status == "active"){ ?>SELECTED<?php } ?>>Active</option>
						<option value="0" <?php if(@$category->status == "inactive"){ ?>SELECTED<?php } ?>>Inactive</option>
					</select>

			</dd>
			<dd>
				<label for="desc" class="long">Description</label>
				<textarea name="desc" id="desc" style="width: 630px; height: 300px;"><?php echo @$category->desc?></textarea>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>