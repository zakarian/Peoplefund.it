		<div class="site-width">
			<section class="view-latest-project">	
				<div class="find-project">
					<?php echo homepage_left_banner ?>
				</div>
				<div class="view-projects">
					<?php $top_project = $picks_projects[0]; ?>
					<div class="media">
						<?php include('../application/views/templates/project_media.php') ?>
					</div>	
					<a href="/<?php echo h(st($top_project->slug)) ?>/" title="<?php echo h(st($top_project->title)) ?>" class="overflow-media"></a>					
					<div class="item latest">
						<h2>
							<span class="clip"></span>
							<span class="category"><?php echo project_banner_widget_categories ?><span class="clear5"></span> 
								<?php 
									if(!empty($top_project->categories)){
									foreach($top_project->categories AS $category){ ?>
									<a class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
											<?php echo h(st($category->title)) ?>
										</a>
								<?php } 
								}?>
								 
							</span>
							<span class="clear"></span>
						</h2>
						<h3><a href="/<?php echo h(st(@$top_project->slug)); ?>" title="<?php echo h(st(@$top_project->title)); ?>"><?php echo trunc(h(st($top_project->title)), 19) ?></a></h3>
						<p><?php echo trunc(h(st($top_project->outcome)), 97) ?></p>
						<div class="stats">
							<span class="unit"><?php echo project_banner_widget_so_far ?> <span>&pound;<?php echo number_format(@$top_project->amount_pledged)?></span></span>
							<div class="bar">

								<div style="width: <?php echo @$top_project->pledged_percent?>%;" class="fill">&nbsp;<?php echo @$top_project->pledged_percent?>%&nbsp;</div>
							</div>
							<span class="from-to">
								<?php /*<span class="from">&pound;0</span>*/ ?>	      
								<span class="to">&pound;<?php echo number_format(@$top_project->amount)?></span> 
								<span class="clear5"></span>
							</span>
							<span class="clear10"></span> 
							<span style="font-size: 13px;" class="unit"><?php echo $top_project->days_left ?> DAY<?php if($top_project->period > 1) echo 'S' ?> REMAINING  OF <?php echo $top_project->period ?></span>
							
							<?php /*
							<span class="unit">day<?php if(@$top_project->period > 1) echo 's' ?> <?php echo project_banner_widget_remaining ?> <span><?php echo @$top_project->days_left?></span></span>
							<div class="bar">
								<div style="width: <?php echo h(st(@$top_project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st(@$top_project->pledged_days)) ?>%&nbsp;</div>
							</div>
							<span class="from-to">
								<span class="from">0</span>               
								<span class="to"><?php echo h(st(@$top_project->period)) ?> day<?php if(@$top_project->period > 1) echo 's' ?></span> 
								<span class="clear"></span>
							</span>*/ ?>	
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</section>
		</div>
		
		<?php include('../application/views/templates/search.php') ?>
		
		<section class="view-projects">
			<div class="site-width">
				<div class="column-two left">
					<h1>
						<span class="block left"><?php echo homepage_editor_pick ?></span>
						<a class="see-all" href="/projects/our_picks/" title="See all">See all</a>
						<span class="clear"></span>
					</h1>
					<?php $skip_first_pick = 1; // The first project will be skiped as it is now top project ?>
					<?php foreach($picks_projects as $k => $project){ ?>
						<?php if ($skip_first_pick == 1) { $skip_first_pick = 0; continue; } ?>
						<?php $k -= 1; // The first project is skipped, so the key is adjusted ?>
						<?php $delItem = 2; ?>
						<?php $attrItem = ''; ?>
						<?php include('../application/views/templates/project.php') ?>
					<?php } ?>
				</div>
				<div class="column-two right">
					<h1>
						<span class="block left"><?php echo homepage_most_funded ?></span>
						<a class="see-all" href="/projects/most_funded/" title="See all">See all</a>
						<span class="clear"></span>
					</h1>
					<?php foreach($most_funded as $k => $project){ ?>
						<?php $delItem = 2; ?>
						<?php $attrItem = ''; ?>
						<?php include('../application/views/templates/project.php') ?>
					<?php } ?>
				</div>
				<div class="clear10"></div>
				<div class="column-two left">
					<h1>
						<span class="block left"><?php echo homepage_most_liked ?></span>
						<a class="see-all" href="/projects/most_watched/" title="See all">See all</a>
						<span class="clear"></span>
					</h1>
					<?php foreach($most_liked as $k => $project){ ?>
						<?php $delItem = 2; ?>
						<?php $attrItem = ''; ?>
						<?php include('../application/views/templates/project.php') ?>
					<?php } ?>
				</div>
				<div class="column-two right">
					<h1>
						<span class="block left"><?php echo homepage_most_recent ?></span>
						<a class="see-all" href="/projects/most_recent/" title="See all">See all</a>
						<span class="clear"></span>
					</h1>
					<?php foreach($recent_projects as $k => $project){ ?>
						<?php $delItem = 2; ?>
						<?php $attrItem = ''; ?>
						<?php include('../application/views/templates/project.php') ?>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</div>
		</section>