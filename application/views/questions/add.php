<div class="site-width">
	<div class="generic-form">
		<form  method="post" action="/questions/add/">
			<h2>Add your question</h2>
			<?php if(!empty($errors)){ 
						foreach($errors AS $error){
						?>
							<div class="global-error">- <?php echo $error;?></div>
						<?php 					}
					echo '<div class="clear20"></div>';
				} ?>
			<textarea name="text" rows="10" cols="50" id="text"></textarea>
			<br />
			<label for="category_id">Select Category</label>
			<select name="category_id" id="category_id">
					<option value="">Please select</option>
				<?php if(!empty($qa_categories)) foreach($qa_categories as $category) { ?>
					<option value="<?php echo $category->id; ?>"><?php echo $category->title; ?></option>
				<?php } ?>
			</select>
			<br />
			<input type="submit" class="button" value="ASK QUESTION">
		</form>
	</div>
</div>