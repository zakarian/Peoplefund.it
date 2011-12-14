<?php if(!isset($ajax)) { ?><div class="site-width">
<?php } else { ?><div style="padding:10px;"><?php } ?>
		<h1>Embed this on to your site</h1>
		<div class="clear10"></div>
		
		<div style="width: 246px; float: left;">
			<script type="text/javascript" src="<?php echo $webroot ?>/<?php echo h(st($project->slug)).'/widget.js/' ?>"></script>
		</div>
	
		<div>
			<h3 style="color: #333; font-size: 17px; padding-bottom: 4px;">Embed code</h3>
			<textarea id="widget-to-group" class="text round" style="width: 184px; margin: 0;"><script type="text/javascript" src="<?php echo $webroot ?>/<?php echo h(st($project->slug)).'/widget.js/' ?>"></script></textarea>
			<div class="clear8"></div>
			<?php /*<div id="clip-holder"><a href="#" title="copy to clipboard" id="clip-button">copy to clipboard</a></div>*/ ?>
		</div>
		<div>
			<script type="text/javascript" src="<?php echo $webroot ?>/js/common/clipboard.js"></script>
			<script type="text/javascript">
				$( document ).ready( function() {
					var clip = new ZeroClipboard.Client();

					clip.setText( '' );
					clip.setHandCursor( true );
					clip.setCSSEffects( true );

					clip.addEventListener( 'mouseDown', function(client) { 
						clip.setText( $( '#widget-to-group' ).val( ) );
					});

					clip.glue( 'clip-button', 'clip-holder' );
				});
			</script>
		</div>
<?php if(!isset($ajax)) { ?></div><?php } else { ?></div><?php } ?>