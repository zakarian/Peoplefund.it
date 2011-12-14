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
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$post['title']?>"/>
			</dd>
			<dd>
				<label for="slug" class="long">Slug</label>
				<input type="text" id="slug" name="slug" class="long validate" value="<?php echo @$post['slug']?>"/>
			</dd>
			<dd>
				<label for="idsubcategory" class="long">Subcategory of</label>
				<select name="idsubcategory" class="long validate" <?php if(empty($categories)){ ?> DISABLED <?php } ?>>
					<option value="0" SELECTED>None</option>
					<?php if(!empty($categories)){ ?>
						
						<?php foreach($categories AS $category){
							?>
								<option value="<?php echo $category->idcategory?>" <?php if($category->idcategory == @$post['idsubcategory']){ echo "SELECTED"; } ?>><?php echo $category->title?></option>
							<?php 
						}
					} ?>
					</select>
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
				<label for="description" class="long">Description</label>
				<textarea name="description" id="description" class="mceEditor" style="width: 630px; height: 300px;"><?php echo @$post['description']?></textarea>
			</dd>
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>