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
				<h3>Page details</h3>
			</dd>
			<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo h(st(@$post['title'])) ?>"/>
			</dd>
			<dd>
				<label for="slug" class="long">Slug</label>
				<?php if(@$post['active'] == "2"){ ?>
					<input type="text" id="slug_temp" name="slug_temp" class="long validate" value="<?php echo h(st(@$post['slug'])) ?>" DISABLED/>
					<input type="hidden" name="slug" id="slug" value="<?php echo h(st(@$post['slug'])) ?>">
				<?php } else { ?>
					<input type="text" id="slug" name="slug" class="long validate" value="<?php echo h(st(@$post['slug'])) ?>"/>
				<?php } ?>
			</dd>

<?php if(@$post['is_section'] == 0) { ?>
			<dd>
				<label for="idsection" class="long">Section</label>
				<?php if(@$post['active'] == "2"){ ?>
					<select name="idsection_temp" class="long validate" disabled="disabled">
						<option value="0" selected="selected">None</option>
					</select>
					<input type="hidden" name="idsection" value="0">
				<?php } else { ?>
					<select name="idsection" class="long validate">
						<option value="0">None</option>
						<?php
							if(!empty($sections)){
								foreach($sections AS $section){
									?>
										<option value="<?php echo $section->idpage ?>"<?php if($section->idpage == @$post['idsection']){ echo ' selected="selected"'; }?>><?php echo $section->title?></option>
									<?php
								}
							}
						?>
					</select>
				<?php } ?>
			</dd>
			<dd>
				<h3>SEO details</h3>
			</dd>
			<dd>
				<label for="meta_title" class="long">Meta Title</label>
				<input type="text" id="meta_title" name="meta_title" class="long" value="<?php echo h(st(@$post['meta_title'])) ?>"/>
			</dd>
			<dd>
				<label for="keywords" class="long">Meta Keywords</label>
				<input type="text" id="keywords" name="keywords" class="long" value="<?php echo h(st(@$post['keywords'])) ?>"/>
			</dd>
			<dd>
				<label for="description" class="long">Meta Description</label>
				<input type="text" id="description" name="description" class="long" value="<?php echo h(st(@$post['description'])) ?>"/>
			</dd>
			<dd>
				<h3>Texts</h3>
			</dd>
			<dd>
				<label for="body" class="mce">Main content</label>
				<textarea name="body" id="body" class="mceEditor" style="width: 100%; height: 500px;"><?php echo @$post['body'] ?></textarea>
			</dd>
<?php } ?>
			<dd>
				<h3>Page options</h3>
			</dd>
			<dd>
				<label for="active" class="long">Active</label>
				<?php if(@$post['active'] == 2){ ?>
					<select name="active_temp" class="long validate" disabled="disabled">
						<option value="1" selected="selected">Active</option>
					</select>
					<input type="hidden" name="active" value="2">
				<?php } else { ?>
					<select name="active" class="long validate">
						<option value="" disabled="disabled">- Please select -</option>
						<option value="1"<?php if(@$post['active'] == 1){ ?> selected="selected"<?php } ?>>Active</option>
						<option value="0"<?php if(@$post['active'] == 0){ ?> selected="selected"<?php } ?>>Inactive</option>
					</select>
				<?php } ?>
			</dd>
			<dd>
				<label class="long">Include in header nav</label>
				<select name="in_main" class="long validate">
					<option value="" disabled="disabled">- Please select -</option>
					<option value="1"<?php if(@$post['in_main'] == 1){ ?> selected="selected"<?php } ?>>Active</option>
					<option value="0"<?php if(@$post['in_main'] == 0){ ?> selected="selected"<?php } ?>>Inactive</option>
				</select>
			</dd>
			<dd>
				<label class="long">Include in footer nav</label>
				<select name="in_foot" class="long validate">
					<option value="" disabled="disabled">- Please select -</option>
					<option value="1"<?php if(@$post['in_foot'] == 1){ ?> selected="selected"<?php } ?>>Active</option>	
					<option value="0"<?php if(@$post['in_foot'] == 0){ ?> selected="selected"<?php } ?>>Inactive</option>
				</select>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>