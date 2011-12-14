<div class="site-width">
	<section class="view-profile">
		<?php include("public_menu.php"); ?>
		<div class="clear"></div>
		<div class="box">
			<div class="sidebar">
				<?php if(!empty($_SESSION['user']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
					<a class="edit-profile" href="/user/profile/" title="Edit profile settings">Edit profile settings</a>
				<?php } ?>
				<?php if($user->websites) { ?>
					<div class="websites">
						<h4>websites</h4>
						<?php 
							$websites = explode("|", $user->websites);
							foreach($websites as $website){
								?>
									<a target="_blank" href="<?php echo str_to_site($website) ?>" title="<?php echo h(st($website)) ?>"><?php echo h(st($website)) ?></a><br />            
								<?php
							}
						?>
						<br />
						<?php if(!empty($_SESSION['user']) && $user->iduser == $_SESSION['user']['iduser']){ ?>
							<a href="/user/profile/" title="Add more websites">Add more websites</a><br />
						<?php } ?>
					</div>
					<div class="clear10"></div>
				<?php } ?>
				<div class="share">
					<h4>Share profile</h4>
					<a target="_blank" class="facebook" href="http://www.facebook.com/sharer/sharer.php?u=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="Facebook">FACEbook<span></span></a> 
					<a target="_blank" class="google" href="https://m.google.com/app/plus/x/?v=compose&content=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="Google+">Google+<span></span></a> 
					<a target="_blank" class="twitter" href="http://twitter.com/home?status=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="Twitter">Twitter<span></span></a> 
					<div class="clear10"></div>
					<a target="_blank" class="linkedin" href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="LinkedIn">LinkedIn<span></span></a>   
					<a target="_blank" class="tumbler" href="http://tumblr.com/share?v=2&u=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="Tumblr">TUMBLR<span></span></a> 
					<a target="_blank" class="email latest" href="http://www.addtoany.com/email?linkurl=<?php echo site_url('user/'.h(st($user->username)).'/') ?>" title="Email">Email<span></span></a> 
					<div class="clear"></div>
				</div>
			</div>
			<div class="data">
				<div class="white-box">
					
					<div class="overflow">
						<h2><?php echo h(st($user->username)) ?></h2>
						<?php /* if($user->location_preview) { ?>
							<a href="" title="" class="location"><?php echo h(st($user->location_preview)) ?></a>
						<?php } */ ?>
						<?php if($user->bio) { ?>
							<div class="about">
								<p><?php echo h(st($user->bio)) ?></p>
							</div>
						<?php } ?>
					</div>
					<div class="profile-picture"> 
						<?php if (!empty($user->ext) && file_exists('uploads/users/'.$user->iduser.'_150x150.jpg')){ ?>
							<img src="/uploads/users/<?php echo $user->iduser; ?>_150x150.jpg" alt="<?php echo h(st($user->username)); ?>" />
						<?php }else{ ?>
							<img src="<?php echo  DEFAULT_USER_LARGE_THUMB ?>" alt="<?php echo h(st($user->username)); ?>" />
						<?php } ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="white-box">
					<h2>Activity</h2>
					
					<div class="activity">
					
						<?php if(!empty($events)){ ?>
							<div id="activity_items">
							
								<?php foreach($events AS $event){ ?>
									<div class="item">
										<div class="left-column">
											<div class="picture">
												<a href="/<?php echo h(st($event->slug));?>/" title="<?php echo h(st($event->title));?>">
													<?php if(!empty($event->ext)){ ?>
														<img src="/uploads/projects/<?php echo $event->idproject?>_211x130.jpg" alt="Project image" />
													<?php } else { ?>
														<img src="<?php echo DEFAULT_PROJECT_THUMB?>" alt="" />
													<?php } ?>
												</a>
											</div>
											<a class="project" href="/<?php echo h(st($event->slug));?>/" title="<?php echo h(st($event->title)) ?>"><?php echo trunc(h(st($event->title)), 47) ?></a>
											
											<?php if(!empty($event->categories)){ ?>
												<?php foreach($event->categories AS $i => $category){ ?>
													<a class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
														<?php echo h(st($category->title)) ?>
													</a>
													<?php echo (($i+1)%4 == 0) ? '<span class="clear5"></span>' : '' ?>
												<?php } ?>
											<?php } ?>
											<span class="clear5"></span>
											
											<a class="location-small" href="/projects/search/string:<?php echo $event->location_preview?>" title="">
												<?php 												echo $event->location_preview;
												if(!empty($event->county_name)){
													?>, <?php echo $event->county_name;
												}
												?>
											</a>
										</div>
										<div class="right-column">
											<div class="type">
												<div class="image">
													<a href="/user/<?php echo $user->slug?>/" title="<?php echo $user->username?>">	
														<?php if (!empty($user->ext) && file_exists('uploads/users/'.$user->iduser.'_40x40.jpg')){ ?>
															<img src="/uploads/users/<?php echo $user->iduser; ?>_40x40.jpg" alt="<?php echo h(st($user->username)); ?>" />
														<?php }else{ ?>
															<img src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo h(st($user->username)); ?>" />
														<?php } ?>
													</a>
												</div>
												<div class="details">
													<a class="fname" href="/user/<?php echo $user->slug?>/" title="<?php echo h(st($user->username))?>"><?php echo trunc(h(st($user->username)), 27) ?></a><div class="clear"></div>
													<?php if($event->type == "update_comment"){ ?>
														<a class="from" href="/<?php echo h(st($event->slug))?>/comments/" title="Commented on a project update">Commented on a <span>project update</span></a>
													<?php } else if($event->type == "comment"){ ?>
														<a class="from" href="/<?php echo h(st($event->slug))?>/comments/" title="Commented project">Commented <span>project</span></a>
													<?php } else if($event->type == "new_project"){ ?>
														<a class="from" href="/<?php echo h(st($event->slug))?>/" title="Started a project">Started a <span>project</span></a>
													<?php } else if($event->type == "back_amount"){ ?>
														<a class="from" href="/<?php echo h(st($event->slug))?>/" title="Backed a project">Backed a <span>project</span></a>
													<?php } else if($event->type == "new_update"){ ?>
														<a class="from" href="/<?php echo h(st($event->slug))?>/comments/" title="Added update">Added new <span>update</span></a>
													<?php } ?>
													<div class="clear"></div>
												</div>
												<div class="date">Started <?php echo date_before($event->date_added)?> ago</div>
												<div class="clear"></div>
											</div>
											<article>
												<p><?php echo $event->text?></p>
											</article>
										</div>
										<div class="clear"></div>
									</div>
								<?php } ?>
							</div>
							
							<a class="older-posts" href="javascript:;" onClick="showMorePosts(<?php echo $user->iduser?>);" title="Older posts">
								<span>Older posts</span>
								<div style="float: right; display: none;" id="loading_events">
									<img src="/img/wall-ajax-loader.gif">
								</div>
							</a>

					<?php } else { ?>
						</div>
						<center>No activity yet</center>
					<?php } ?>
					
					<div class="clear"></div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</section>
</div>

<input type="hidden" id="from" value="5">