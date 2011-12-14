<?php 
	$active_tab = 'project';
	include('view_header.php'); 
?>	
				<div class="box">
						<?php include('../application/views/templates/project_media.php') ?>
						<div class="clear"></div>
						<div class="box">
							<div class="share">
								<span class="title">Share this project</span><div class="clear"></div>

								<div class="icons">
									<a target="_blank" class="facebook" href="http://www.facebook.com/sharer.php?u=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>" title="Facebook">Facebook<span></span></a> 
									<a target="_blank" class="google" href="https://m.google.com/app/plus/x/?v=compose&content=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>" onclick="window.open('https://m.google.com/app/plus/x/?v=compose&content=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>','gplusshare','width=450,height=300,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-150)+'');return false;" title="Google+">Google+<span></span></a> 
									<a target="_blank" class="twitter" href="http://twitter.com/home?status=<?php echo urlencode('I\'ve just found this great project on @peoplefundit '.site_url(h(st($project->slug))).' Have a look!') ?>/" title="Twitter">Twitter<span></span></a> 
									
									<a target="_blank" class="linkedin" href="http://www.linkedin.com/shareArticle?mini=true&url=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>&title=<?php echo $project->title?>&summary=<?php echo $project->title?>&source=http%3A%2F%2Fwww.peoplefund.it%2F" title="LinkedIn">LinkedIn<span></span></a>   
									<a target="_blank" class="tumbler" href="http://tumblr.com/share?v=2&u=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>%2F&t=<?php echo $project->title?>&" title="Tumblr">Tumblr<span></span></a> 
									<a target="_blank" class="email latest" href="http://www.addtoany.com/email?linkurl=http%3A%2F%2Fwww.peoplefund.it%2F<?php echo $project->slug?>&linkname=<?php echo $project->title?>" title="Email">Email<span></span></a> 
									
								</div>

								<div class="item widget">
									<div class="left overflow">GET THE WIDGEt</div>
									<div class="clear"></div>
									<a href="/<?php echo $project->slug; ?>/widget/" title="GET THE WIDGEt" rel="prettyPhoto" width="464" height="485">GET THE WIDGET</a>
									<span>embed on<br /> your<br /> website</span>
									<div class="clear10"></div>
								</div>
								
								<div class="item link">
									<div class="left overflow">Share this link</div>
									<div class="clear"></div>
									
									<script type="text/javascript" src="/js/common/clipboard.js"></script>

									<span></span>
									<input type="text" id="url-to-group" name="People Fund IT" value="<?php echo site_url(h(st($project->slug))) ?>/" class="query" />

									<div class="clear"></div>
									<div class="flash-copy">
										<div id="clip-holder"><small><a href="#" title="copy to clipboard" id="clip-button">post to clipboard</a></small></div>
									</div>
									<script type="text/javascript" src="/js/common/clipboard.js"></script>
									<script type="text/javascript">
										$( document ).ready( function() {
											var clip = new ZeroClipboard.Client();

											clip.setText('');
											clip.setHandCursor( true );
											clip.setCSSEffects( true );

											clip.addEventListener( 'mouseDown', function(client) { 
												clip.setText( $( '#url-to-group' ).val( ) );
											});

											clip.glue( 'clip-button', 'clip-holder' );
											
											<?php if(isset($_GET['limited'])) { ?>jAlert('Unfortunately the reward you have selected is now fully booked. Please review the other rewards for this project.');<?php } ?>
										});
									</script>							
									
									<div class="clear"></div>
								</div>

								<div class="clear"></div>
							</div>
						</div>
					<?php if($project->editors_pick == 1 && $project->celebrity_backed == 1 && $project->celebrity_title) { ?>	
						<div class="clear"></div>
						<div style="margin-top: 0;" class="box">
							<div class="celebrity_backed">	
								<img src="/uploads/celebrities/<?php echo $project->celebrity_image ?>" alt="" height="68" width="75">
								<span class="celebrity_title"><?php echo $project->celebrity_title ?> Project Pick</span>
								<span class="celebrity_quote">"<?php echo $project->celebrity_quote ?>"</span>
								<span class="pick"></span>
							</div>
							<div class="clear"></div>
						</div>
					<?php } ?>	
					<?php if($project->outcome) { ?>
						<div class="clear"></div>
						<div><p><?php echo $project->outcome ?></p><br></div>
					<?php } ?>	
					<?php if($project->about) { ?>
						<div class="clear"></div>
						<article class="normal-page"><?php echo $project->about ?></article>
					<?php } ?>
				</div>
			</div>
			<div class="project_meta">
				<?php include("sidebar.php"); ?>
			</div>
			
			<div class="clear"></div>
		</div>
		<div class="clear"></div>	
	</section>
</div>

<?php include('liked.php') ?>