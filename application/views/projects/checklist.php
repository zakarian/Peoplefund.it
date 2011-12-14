<div class="site-width">
	<div class="generic-form overflow left">
		<div class="steps">
			<span>• <b>step 1</b> checklist</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>Step 2</b> project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 3</b> account &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 4</b> submit
		</div>
		<div class="inner-form left">
			<form class="global_form_2" method="post" action="/projects/checklist/">
			
				<?php if (!empty($errors)) : ?>
					<?php foreach($errors as $error) : ?>
						<span class="global_error"><?php echo $error; ?></span> 
					<?php endforeach; ?>
					<div class="clear10"></div>
				<?php endif; ?>
				
				<?php echo @$page[0]->body ?>

				<input type="hidden" name="conditions" value="1" class="" />
				<input type="hidden" name="privacy" value="1" class="" />
				
				<div class="clear"></div>
				<input class="button left" name="submit" type="submit" value="Accept">
				<div class="clear"></div>
			</form>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>