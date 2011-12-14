<?php
	
	$this->projects->limit = 1;
	$pick_project = $this->projects->get_picks_projects_home();
	$pick_project = !empty($pick_project) ? $pick_project[0] : array(); 

	$this->projects->limit = 1;
	$recent_project = $this->projects->get_recent_projects_home();
	$recent_project = !empty($recent_project) ? $recent_project[0] : array();
		
	$this->projects->limit = 1;
	$watched_project = $this->projects->get_liked_projects_home();
	$watched_project = !empty($watched_project) ? $watched_project[0] : array(); 

?>
		
<div class="widget-most-projects">
	<div class="box">
		<h5>MOST RECENT PROJECT</h5>
		<div class="item">
			<h1 class="left overflow"><a href="/<?php echo h(st($recent_project->slug))?>/" title="<?php echo h(st($recent_project->title))?>"><?php echo  trunc(h(st($recent_project->title)), 47) ?></a></h1><div class="clear"></div>
			<div class="stats left overflow">
				So Far <span>&pound;<?php echo number_format(@$recent_project->amount_pledged)?></span>  <br />
				Day<?php if(@$recent_project->period > 1) echo 's' ?> Remaining <span><?php echo @$recent_project->days_left?></span>
			</div><div class="clear"></div>
			<p class="left overflow"><?php echo trunc(h(st($recent_project->outcome)), 97) ?></p>
			<div class="clear"></div>
			<a class="more" href="/<?php echo h(st($recent_project->slug))?>/" title="MORE &amp; FUND">MORE &amp; FUND</a>
			<div class="clear"></div>
		</div>
	</div>
	<div class="box">
		<h5>MOST WATCHED PROJECT</h5>
		<div class="item">
			<h1 class="left overflow"><a href="/<?php echo h(st($watched_project->slug))?>/" title="<?php echo h(st($watched_project->title))?>"><?php echo  trunc(h(st($watched_project->title)), 47) ?></a></h1><div class="clear"></div>
			<div class="stats left overflow">
				So Far <span>&pound;<?php echo number_format(@$watched_project->amount_pledged)?></span>  <br />
				Day<?php if(@$watched_project->period > 1) echo 's' ?> Remaining <span><?php echo @$watched_project->days_left?></span>
			</div><div class="clear"></div>
			<p class="left overflow"><?php echo trunc(h(st($watched_project->outcome)), 97) ?></p>
			<div class="clear"></div>
			<a class="more" href="/<?php echo h(st($watched_project->slug))?>/" title="MORE &amp; FUND">MORE &amp; FUND</a>
			<div class="clear"></div>
		</div>
	</div>
	<?php if(!empty($pick_project)) { ?>
	<div class="box latest">
		<h5>OUR PROJECT PICK</h5>
		<div class="item">
			<h1 class="left overflow"><a href="/<?php echo h(st($pick_project->slug))?>/" title="<?php echo h(st($pick_project->title))?>"><?php echo  trunc(h(st($pick_project->title)), 47) ?></a></h1><div class="clear"></div>
			<div class="stats left overflow">
				So Far <span>&pound;<?php echo number_format(@$pick_project->amount_pledged)?></span>  <br />
				Day<?php if(@$pick_project->period > 1) echo 's' ?> Remaining <span><?php echo @$pick_project->days_left?></span>
			</div><div class="clear"></div>
			<p class="left overflow"><?php echo trunc(h(st($pick_project->outcome)), 97) ?></p>
			<div class="clear"></div>
			<a class="more" href="/<?php echo h(st($pick_project->slug))?>/" title="MORE &amp; FUND">MORE &amp; FUND</a>
			<div class="clear"></div>
		</div>
	</div>
	<?php } ?>
</div>