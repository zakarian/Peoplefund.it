<section class="view-projects">		
	<div class="site-width">
		<?php if(!empty($projects)){ ?>
			<?php foreach($projects as $k=>$project){ ?>	
				<div class="item<?php if(($k+1)%4 == 0){ ?> latest<?php } ?>">
					<h2>
					<span class="clip"></span>
						<span class="category">
							CATEGORIES:
							<?php foreach($project->categories AS $category){ ?>
								<a href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
									<strong><?php echo h(st($category->title)) ?></strong>
								</a>
							<?php } ?>
						</span>
						<span class="clear"></span>
					</h2>
					<h3><a href="/<?php echo h(st($project->slug)) ?>" title="<?php echo h(st($project->title)) ?>"><?php echo h(st($project->title)) ?></a></h3>
					<a href="/<?php echo h(st($project->slug)) ?>" title="<?php echo h(st($project->title)) ?>">
						<?php 
							$project->thumb = DEFAULT_PROJECT_THUMB;
							if(!empty($project->ext)){ ?>
								<img width="211" height="130" src="/uploads/projects/<?php echo $project->idproject?>_211x130.jpg" alt="<?php echo h(st($project->title));?>" />
							<?php } else { ?>
								<img width="211" height="130" src="<?php echo $project->thumb?>" alt="<?php echo h(st($project->title));?>" />
							<?php }
						?>
					</a>
					<p><?php echo h(st(h(st($project->outcome)))) ?></p>
					<div class="stats">
						<span class="unit">SO FAR: <span>&pound;<?php echo h(st(number_format($project->amount_pledged))) ?></span></span>
						<div class="bar">
							<div style="width: <?php echo h(st($project->pledged_percent)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_percent)) ?>%&nbsp;</div>
						</div>
						<span class="from-to">
							<span class="from">&pound;0</span>		               
							<span class="to">&pound;<?php echo h(st(number_format($project->amount))) ?></span> 
							<span class="clear5"></span>
						</span>
						<span class="unit">day<?php if($project->period > 1) echo 's' ?> Remaining: <span><?php echo h(st($project->days_left)) ?></span></span>
						<div class="bar">
							<div style="width: <?php echo h(st($project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_days)) ?>%&nbsp;</div>
						</div>
						<span class="from-to">
							<span class="from">0</span>		               
							<span class="to"><?php echo h(st($project->period)) ?> days</span> 
							<span class="clear"></span>
						</span>
					</div>
				</div>
			<?php } ?>
			<div class="clear"></div>
		<?php } else { ?>
			No projects added yet
		<?php } ?>
	</div>
</section>
	<?php /*	<tr>
						<td>
							<a href="/<?php echo h(st($project->slug?>/"><?php echo h(st($project->title?></a>
						</td>
						<td>
							<?php echo h(st(ucfirst($project->status)?>
						</td>
						<td>
							<a href="/<?php echo h(st($project->slug?>/">View</a>
							<?php if($project->status != "closed"){ ?>
								| <a href="/<?php echo h(st($project->slug?>/edit/">Edit</a>
								| <a href="/<?php echo h(st($project->slug?>/updates/">Updates</a>
								| <a href="/<?php echo h(st($project->slug?>/close/">Close</a>
							<?php } ?>
						</td>
					</tr>*/ ?>