	<center>
		<br />
		<br />
		<br />
		<?php if (isset($message) && !empty($message)){ ?>
		<h1><?php echo $message; ?></h1>
		<?php } ?>
		
		<div class="clear"></div>
		<br />
		<br />
		
		<a href="<?php echo isset($_SESSION['_refer']) ? $_SESSION['_refer'] : '/' ?>" target="_top" style="font-size: 15px; color: #555555; text-decoration: none;">Close and reload</a>
	
		<script>
			parent.window.location = <?php echo isset($_SESSION['_refer']) ? '"/' . $_SESSION['_refer'] . '/"' : 'parent.window.location.href' ?>;

			$(window.parent).find('.pp_close').bind("click", function(){
				parent.window.location = <?php echo isset($_SESSION['__refer']) ? '"/' . $_SESSION['_refer'] . '/"' : 'parent.window.location.href' ?>;
			});
		</script>
		<?php if(isset($_SESSION['_refer'])) isset($_SESSION['_refer']); ?>
	</center>