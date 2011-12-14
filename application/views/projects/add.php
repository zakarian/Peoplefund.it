<div class="site-width">
	<div class="generic-form overflow left">
		<div class="steps">
			• <b>step 1</b> checklist &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span>• <b>Step 2</b> project</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 3</b> account &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 4</b> submit
		</div>
		<div class="inner-form left">
			<form class="global_form_2" method="post" action="/projects/add/">
				<h2>Add your project</h2>
				
				<?php if(!empty($errors)){ 
						foreach($errors AS $error){
						?>
							<div class="global-error">- <?php echo $error;?></div>
						<?php 					}
					echo '<div class="clear20"></div>';
				} ?>

				<?php include('../application/views/projects/project_fields/title.php') ?>
				<?php include('../application/views/projects/project_fields/location.php') ?>
				<?php include('../application/views/projects/project_fields/slug.php') ?>
				<?php include('../application/views/projects/project_fields/website.php') ?>
				<?php include('../application/views/projects/project_fields/category.php') ?>
				<?php include('../application/views/projects/project_fields/media.php') ?>
				<?php include('../application/views/projects/project_fields/aim.php') ?>
				<?php include('../application/views/projects/project_fields/about.php') ?>
				<?php include('../application/views/projects/project_fields/pledges.php') ?>
				<?php include('../application/views/projects/project_fields/period.php') ?>
				<?php include('../application/views/projects/project_fields/amount.php') ?>
				<?php // include('../application/views/projects/project_fields/gocardless.php') ?>
				<?php // include('../application/views/projects/project_fields/paypal_email.php') ?>
				<?php include('../application/views/projects/project_fields/pledge_more.php') ?>
				<?php include('../application/views/projects/project_fields/helpers.php') ?>
				<?php include('../application/views/projects/project_fields/time.php') ?>
				<?php include('../application/views/projects/project_fields/skills.php') ?>
				<?php include('../application/views/projects/project_fields/buttons.php') ?>
				<div class="clear"></div>
			</form>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<script>
	var changed = false;

	$(document).ready(function(){
		$( '.global_form_2 .button' ).click( function() {
			changed = false;
		});

		$( '.global_form_2 input, .global_form_2 textarea' ).change( function() {
			changed = true;
		});
	});

	window.onbeforeunload = warnUser;

	function warnUser() {
		if(changed) {
			return "If you leave this page you will loose the information you have entered. Would you like to save your project first?";
		}
	}
</script>
