		<?php include('../application/views/templates/search.php') ?>
		<section class="view-projects">			
			<div class="site-width">
				<?php if(!$searching) { ?>
					<div class="column-two left">
						<h1>
							<span class="block left"><?php echo homepage_editor_pick ?></span>
							<a class="see-all" href="/projects/our_picks/" title="See all">See all</a>
							<span class="clear"></span>
						</h1>
						<?php foreach($picks_projects as $k => $project){ ?>
							<?php $delItem = 2; ?>
							<?php $attrItem = ''; ?>
							<?php include('../application/views/templates/project.php') ?>
						<?php } ?>
					</div>
					<div class="column-two right">
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
					<div class="clear10"></div>
					<div class="column-two left">
						<h1>
							<span class="block left">Ending soon</span>
							<a class="see-all" href="/projects/ending_soon/" title="See all">See all</a>
							<span class="clear"></span>
						</h1>
						<?php foreach($ending_soon as $k => $project){ ?>
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
				<?php } else { ?>
				<div class="column-two" style="width: 100%;">
					<?php /* if(!empty($keyword)){ ?>
						<h1>KEYWORD SEARCH: <b><?php echo strtoupper(h(st(stripslashes($keyword))))?></b></h1>
					<?php } else if(!empty($category)){ ?>
						<h1>CATEGORY SEARCH: <b><?php echo strtoupper(h(st(stripslashes($category))))?></b></h1>
					<?php } else if(!empty($string)){ ?>
						<h1>POSTCODE/LOCATION SEARCH: <b><?php echo strtoupper(h(st(stripslashes(@$string))))?></b></h1>
					<?php } else if(!empty($current_page_h1)){ ?>
						<h1><?php echo h(st($current_page_h1)) ?></h1>
					<?php } else { */ ?>
						<h1><b>SEARCH RESULTS</b></h1>
					<?php // } ?>
					 
					<?php if(!empty($projects)){
						foreach($projects as $k => $project){ ?>
							<?php $delItem = 4; ?>
							<?php $attrItem = ' style="margin-bottom: 5px;"'; ?>
							<?php include('../application/views/templates/project.php') ?>
						<?php } ?>
					</div>
					<div class="clear"></div>
				
			<?php } else {
					?><br><center>No projects found</center><br><?php 				}
			}
			?>