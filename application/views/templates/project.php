						<div class="item<?php echo ((($k+1)%$delItem == 0) ? ' latest' : '') ?>"<?php if(isset($attrItem)) echo $attrItem ?>>
							<h2>
								<span class="clip"></span>
								<span class="category">
									<?php echo project_banner_widget_categories ?><span class="clear5"></span>
									<?php foreach($project->categories AS $category){ ?>
										<a class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
											<?php echo h(st($category->title)) ?>
										</a>
									<?php } ?>
								</span>
								<span class="clear"></span>
							</h2>
							<h3><a href="/<?php echo h(st($project->slug))?>/" title="<?php echo h(st($project->title)); ?>"><?php echo trunc(h(st($project->title)), 47) ?></a></h3>
							<a class="project-thumb" href="/<?php echo h(st($project->slug))?>/" title="<?php echo h(st($project->title)); ?>">
								<?php 
									if(!empty($project->ext) && file_exists('uploads/projects/'.$project->idproject.'_211x130.jpg')) 
										$project->thumb = '/uploads/projects/'.$project->idproject.'_211x130.jpg';
									else
										$project->thumb = DEFAULT_PROJECT_THUMB;
								 ?>
								<img width="211" height="130" src="<?php echo $project->thumb ?>" alt="<?php echo h(st($project->title));?>" />
								<?php if(isset($haveStatusBadge) && $haveStatusBadge) { ?>
									<span class="status-badge">	
										<?php 
											if($project->status == 'temp') 
												echo 'Status: unpublished';
											elseif($project->status == 'moderated') 
												echo 'Status: awaiting  approval';
											elseif($project->status == 'open' && $project->days_left < 1) 
												echo 'Status: target not reached';
											elseif($project->status == 'open' && $project->pledged_percent < 100) 
												echo 'Status: now funding';
											elseif($project->status == 'open' && $project->pledged_percent == 100) 
												echo 'Status: funded!';
										?>
									</span>
								<?php } elseif(isset($haveStatusBadgeBacked) && $haveStatusBadgeBacked) { ?>
									<?php 
										if($project->status == 'open' && $project->days_left < 1) 
											echo '<span class="status-badge">Target not met</span>';
										elseif($project->status == 'open' && $project->pledged_percent == 100) 
											echo '<span class="status-badge">Fully funded!</span>';
									?>
								<?php } elseif($project->editors_pick == 1) { ?>
									<?php 
										echo '<span class="editor-badge">';
										if($project->celebrity_backed == 1 && $project->celebrity_title) 
											echo '<span class="celebrity_title">'.$project->celebrity_title.'</span>';
										echo '<span class="pick"></span></span>';
									?>
								<?php } ?>
							</a>
							<p><?php echo trunc(h(st($project->outcome)), 97) ?></p>
							<div class="stats">
								<span class="unit"><?php echo project_banner_widget_so_far ?> <span>&pound;<?php echo number_format($project->amount_pledged)?></span></span>
								<div class="bar">
									<div style="width: <?php echo $project->pledged_percent < 100 ? $project->pledged_percent : 100 ?>%;" class="fill">&nbsp;<?php echo $project->pledged_percent < 100 ? $project->pledged_percent : 100 ?>%&nbsp;</div>
								</div>
								<span class="from-to">
									<?php /*<span class="from">&pound;0</span>*/ ?>	               
									<span class="to">&pound;<?php echo number_format($project->amount)?></span> 
									<span class="clear5"></span>
								</span>
								<span style="font-size: 13px;" class="unit"><?php echo $project->days_left ?> DAY<?php if($project->period > 1) echo 'S' ?> REMAINING  OF <?php echo $project->period ?></span>
								<?php /*<div class="bar">
									<div style="width: <?php echo h(st($project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_days)) ?>%&nbsp;</div>
								</div>
								<span class="from-to">
									<span class="from">0</span>	       	               
									<span class="to"><?php echo h(st($project->period)) ?> day<?php if($project->period > 1) echo 's' ?></span> 
									<span class="clear"></span>
								</span>*/ ?>
							</div>
						</div>
						<?php echo ((($k+1)%$delItem == 0) ? '<div class="clear"></div>' : '') ?>