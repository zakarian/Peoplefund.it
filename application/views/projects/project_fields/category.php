<?php	
	if(isset($selected_categories) && $selected_categories)
		$addValue = $selected_categories;
	else if(isset($post['categories']) && $post['categories'])
		$addValue = $post['categories'];
	else
		$addValue = '';		
?>
<fieldset>
	<label for="insert_website">
		<?php if(isset($errors['categories']) && $errors['categories']) { ?><b style="color: red;">*</b><?php } ?>
		Category
	</label>
	<div class="clear5"></div>
	<small class="label">
		Please select which categories your project has an innovative solution for.
	</small>
	<div class="clear10"></div>
	<?php
		if(!empty($categories)) {
			foreach($categories as $category){ ?>
				<div style="float: left; width: 180px; padding-top: 5px;">
					<input type="checkbox" name="categories[<?php echo $category->idcategory ?>]"<?php if(!empty($addValue[$category->idcategory])) echo ' checked="checked"' ?> value="<?php echo $category->idcategory ?>"> <?php echo h(st($category->title)) ?>
				</div>
	<?php } } ?>
	<div class="clear10"></div>
</fieldset>	