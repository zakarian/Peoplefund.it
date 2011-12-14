	<div class="site-width">
		<section class="generic-page">
			<form class="global_form_1" method="post" action="/projects/add_helper/">
				<h1>Thank you for your pledge! <span style="font-size: 14px;">If you want to help the project even more you can ...</span></h1>
				<div class="view-project">
					<div style="padding: 0 15px;">
						<div class="share">
							<span style="text-transform: none;" class="title">Tell your friends about this project</span><div class="clear"></div>
							<div class="icons">
									<a target="_blank" class="facebook" href="http://www.facebook.com/sharer.php?u=<?php echo site_url(h(st($project->slug))) ?>" title="Facebook">Facebook<span></span></a> 
									
									<a target="_blank" class="google" href="https://m.google.com/app/plus/x/?v=compose&content=<?php echo site_url(h(st($project->slug))) ?>" onclick="window.open('https://m.google.com/app/plus/x/?v=compose&content=<?php echo site_url(h(st($project->slug))) ?>','gplusshare','width=450,height=300,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-150)+'');return false;" title="Google+">Google+<span></span></a>
									
									<a target="_blank" class="twitter" href="http://twitter.com/home?status=<?php echo urlencode('I just pledged to a great project called '.h(st($project->title)).' on peoplefund.it. '.site_url(h(st($project->slug)))) ?>/" title="Twitter">Twitter<span></span></a> 
									
									<a target="_blank" class="linkedin" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo site_url(h(st($project->slug))) ?>&title=<?php echo urlencode('I just pledged to a great project called '.h(st($project->title)).' on peoplefund.it.') ?>&summary=<?php echo h(st($project->title)) ?>&source=<?php echo site_url() ?>" title="LinkedIn">LinkedIn<span></span></a>  
									
									<a target="_blank" class="tumbler" href="http://tumblr.com/share?v=2&u=<?php echo site_url(h(st($project->slug))) ?>&t=<?php echo urlencode('I just pledged to a great project called '.h(st($project->title)).' on peoplefund.it.') ?>&" title="Tumblr">Tumblr<span></span></a> 
							</div>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				
				<?php if($project->helpers && $project->time){ ?>
					<div class="view-project">
						<div style="padding: 0 15px;">
							<div class="share">
								<span style="text-transform: none;" class="title">Offer your time</span><div class="clear"></div>
								<p style="padding-bottom: 0 !important;"><input maxlength="4" type="text" name="helper_hours" id="helper_hours" class="inline-query focus-style" /> hours</p>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				<?php } ?>
				
				<?php if($project->helpers && $project->skills){ ?>
					<div class="view-project">
						<div style="padding: 0 15px;">
							<div class="share">
								<span style="text-transform: none;" class="title">Offer your skills</span><div class="clear"></div>
								<?php $project->skills = @unserialize($project->skills) ?>
								<?php if(is_array($project->skills) && $project->skills) { ?>
									<p><?php echo h(st($project->title)) ?> are looking for these skills:</p>
									<p style="padding-bottom: 0 !important;">
										<?php foreach($project->skills as $skill) { ?>
											<input type="checkbox" name="helper_text[]" class="" value="<?php echo h(st($skill)) ?>" /> <?php echo h(st($skill)) ?>
											<span class="clear5"></span>
										<?php } ?>
									</p>
								<?php } ?>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				<?php } ?>
				
				<?php if($project->helpers && ($project->skills OR $project->time)){ ?>
					<input onclick="return checkThanksForm();" class="button" name="submit" type="submit" value="Submit">
					<input onclick="window.location = '<?php echo site_url(h(st($project->slug))) ?>'; return false;" style="margin-right: 10px;" class="button edit-later" name="submit" type="submit" value="No thanks">
				<?php } else { ?>
					<input onclick="window.location = '/projects/'; return false;" style="margin-right: 10px;" class="button left" name="submit" type="button" value="Find more Projects">
				<?php } ?>
				
				<div class="clear"></div>
			</form>
		</section>
		<?php include('../application/views/templates/widget-most-projects.php') ?>
		<div class="clear"></div>
	</div>
	
	<script>
		/* function checkThanksForm(){
			if($("#helper_hours").val() == ""){
				alert("Please enter time that you want to spare");
				return false
			} else {
				return TRUE;
			}
		}*/
	</script>
