	<?/*<script type="text/javascript">
		$(document).ready( function() {
			var div_gp = $('.generic-page');
			var div_wp = $('.widget-most-projects');
			
			var div_gpm	= parseInt(div_gp.css('margin-top'));
			var div_gph	= parseInt(div_gp.height());
			var div_wph	= parseInt(div_wp.height());
		
			var tmp = 0;
			if( div_gph > tmp ) tmp = div_gph;
			if( div_wph > tmp ) tmp = div_wph;
			
			div_gp.height(parseInt(tmp - div_gpm));
			div_wp.height(tmp);
		});
	</script>*/?>
	<div class="site-width">
		<section class="generic-page<?php echo ($page->slug == '404') ? ' e404' : '' ?>"><?php echo $page->body ?></section>
		<?php include('../application/views/templates/widget-most-projects.php') ?>
		<div class="clear"></div>
	</div>
