	<div class="site-width">
		<section class="generic-page">
			<?php if(isset($error) && $error){ ?>
				<?php echo $error ?><br />
			<?php } else { ?>
				There was a problem with your pledge, please contact administrators for more details!<br />
			<?php } ?>
			<br />
			<a title="Back to homepage" href="/">Back to homepage</a>
		</section>
		<?php include('../application/views/templates/widget-most-projects.php') ?>
		<div class="clear"></div>
	</div>

