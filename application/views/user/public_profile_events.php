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
				<?php foreach($event->categories AS $category){ ?>
					<a class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
						<?php echo h(st($category->title)) ?>
					</a>
					<?php echo (($i+1)%4 == 0) ? '<span class="clear5"></span>' : '' ?>
				<?php } ?>
			<?php } ?>
			<span class="clear5"></span>
			
			<a class="location-small" href="/projects/search/string:<?php echo $event->location_preview?>" title="">
				<?php 				echo $event->location_preview;
				if(!empty($event->county_name)){
					?>, <?php echo $event->county_name;
				}
				?>
			</a>
		</div>
		<div class="right-column">
			<div class="type">
				<div class="image">
					<a href="/users/<?php echo $user->slug?>/" title="<?php echo $user->username?>">	
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
				<p>
					<?php echo $event->text?>
				</p>
			</article>
		</div>
		<div class="clear"></div>
	</div>
<?php } ?>