		<section class="search-projects">		
			<div class="site-width">	
				<div class="results">
					
					<?php
					if (!empty($projects)){
					
						if (isset($show_map) && $show_map){ ?>
							<div id="map_canvas" style="width: 685px; height: 500px;"></div>
							<div class="clear10"></div>
						<?php }
					
						$counter = 0;
						$items_count = count($projects);
						foreach($projects as $i => $project){
							$counter++;
					?>
						<div class="item<?php // if ($counter == $items_count){ echo ' latest'; } ?>">
							<div class="left">
								<h1 class="project_title">
									<?php if ($project->title){ ?>
										<a href="/<?php echo h(st($project->slug)) ?>/" title="<?php echo h(st($project->title));?>">
											<?php echo trunc(h(st($project->title)), 18) ?>
										</a>
									<?php } ?> 
									<?php if (!empty($project->username)){ ?>
										<span>By 
											<a href="/user/<?php echo h(st($project->username)); ?>/" title="<?php echo h(st($project->username)); ?>">
												<?php echo trunc(h(st($project->username)), 18) ?>
											</a>
										</span>
									<?php } ?>
								</h1>
								<span class="project_category">
									Categories: 
									<?php foreach($project->categories AS $category){ ?>
										<a href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
											<strong><?php echo h(st($category->title)) ?></strong>
										</a>
									<?php } ?>
								</span>
							</div>
							<div class="right">
								<?php if (isset($searchSql['string']) && !empty($searchSql['string'])){ ?>
									<?php if(isset($project->distance)){ ?>
										<span class="distance">Distance from <?php echo h(st($searchSql['string'])); ?>: <?php echo round($project->distance, 2) ?> Miles</span>
									<?php } ?>
								<?php } ?>
							</div>
							<div class="clear"></div>
							<a href="/<?php echo h(st($project->slug)) ?>/" title="<?php echo h(st($project->title));?>">
								<?php 
									$project->thumb = DEFAULT_PROJECT_THUMB;
									if(!empty($project->ext)){ ?>
										<img width="160" height="110" src="/uploads/projects/<?php echo $project->idproject?>_160x110.jpg" alt="<?php echo h(st($project->title));?>" />
									<?php } else { ?>
										<img width="160" height="110" src="<?php echo $project->thumb?>" alt="<?php echo h(st($project->title));?>" />
									<?php }
								?>
							</a>
							<div class="stats">
								<span class="unit">SO FAR: <span>&pound;<?php echo h(st($project->amount_pledged)) ?></span></span>
								<div class="bar">
									<div style="width: <?php echo h(st($project->pledged_percent)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_percent)) ?>%&nbsp;</div>
								</div>
								<span class="from-to">
									<?php /*<span class="from">&pound;0</span>*/ ?>              
									<span class="to">&pound;<?php echo h(st($project->amount)) ?></span> 
									<span class="clear5"></span>
								</span>
								<span class="unit">days Remaining: <span><?php echo h(st($project->days_left)) ?></span></span>
								<div class="bar">
									<div style="width: <?php echo h(st($project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_days)) ?>%&nbsp;</div>
								</div>
								<span class="from-to">
									<?php /*<span class="from">0</span>*/ ?>  		               
									<span class="to"><?php echo h(st($project->period)) ?> days</span> 
									<span class="clear"></span>
								</span>
							</div>
							<p><?php echo trunc(h(st($project->outcome)), 97) ?></p>
							<div class="clear"></div>
						</div>
					<?php }
					} else { ?>
						<br><center>No projects found</center>
					<?php } ?>
					<div class="clear"></div>
				</div>
				<?php include('../application/views/templates/widget-most-projects.php') ?>
				<div class="clear"></div>
			</div>
		</section>