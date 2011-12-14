<h3 class="project_category"><span></span>
	Categories: <div class="clear5"></div>
	<?php foreach($project->categories AS $category){ ?>
		<a class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>">
			<strong><?php echo h(st($category->title)) ?></strong>
		</a>
	<?php } ?>
</h3>

<section class="view-projects">	
	<div class="item">
		<div class="stats">
			<span class="unit">SO FAR: <span>&pound;<?php echo number_format($project->amount_pledged) ?></span></span>

			<div class="bar">
				<div style="width: <?php echo $project->pledged_percent < 100 ? $project->pledged_percent : 100 ?>%;" class="fill">&nbsp;<?php echo $project->pledged_percent < 100 ? $project->pledged_percent : 100 ?>%&nbsp;</div>
			</div>
			<span class="from-to">
				<?php /*<span class="from">&pound;0</span>*/ ?>	               
				<span class="to">&pound;<?php echo number_format($project->amount) ?></span> 
				<span class="clear5"></span>
			</span>
			<div class="clear15"></div>
			<?php /*<span class="unit">day<?php if($project->period > 1) echo 's' ?> <?php echo project_banner_widget_remaining ?> <span><?php echo $project->days_left?></span></span>
			<div class="bar">
				<div style="width: <?php echo h(st($project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_days)) ?>%&nbsp;</div>
			</div>
			<span class="from-to">
				<span class="from">0</span>	       	               
				<span class="to"><?php echo h(st($project->period)) ?> day<?php if($project->period > 1) echo 's' ?></span> 
				<span class="clear"></span>
			</span>*/ ?>
			<span class="unit"><?php echo $project->days_left ?> DAY<?php if($project->period > 1) echo 'S' ?> REMAINING  OF <?php echo $project->period ?></span>
		</div>
	</div>
</section>
<div class="clear"></div>
		
<?php if(!empty($_SESSION['user']) && $project->iduser <> $_SESSION['user']['iduser'] OR empty($_SESSION['user'])){ ?>
	<?php if (!@$watching_status){ ?>		
		<div class="watch-this-project">
			<?php if (empty($_SESSION['user']['iduser'])){ ?>
				<a href="/user/login/" title="Watch this project" width="450" height="<?php echo LOGIN_HEIGHT ?>" rel="prettyPhoto">Watch this project</a>		
			<?php }else{ ?>
				<a href="/user/watch_project/<?php echo $project->idproject; ?>/" title="WATCH THIS PROJECT">Watch this project</a>
			<?php } ?>
			<div class="clear"></div>
		</div>
	<?php }else{ ?>
		<div class="you-watch-this-project">
			<a href="/user/unwatch_project/<?php echo $project->idproject; ?>/" title="Watch">Watching</a>
			<div class="clear"></div>
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="clear35"></div>
<?php } ?>

<?php if(!empty($_SESSION['user']) && $project->iduser <> $_SESSION['user']['iduser'] OR empty($_SESSION['user'])){ ?>
	<?php if(count($pledges)) { ?>
		<div class="project_pledges_details">

			<span class="key">Backed by</span>
			<span class="value"><?php echo count($pledges) ?> people</span>
			<div class="clear10"></div>
			
			<?php foreach($pledges as $k => $pledge){ 
				if($k == 11) { 
					echo '<a class="view-all" href="/'.$project->slug.'/backers/" title="">See all</a>';
				} else if ($k < 11) {
					if(!$pledge->public) {
						$pledge->user_ext = $pledge->location_preview = $pledge->username = '';
					}
					$i = $k + 1; ?>
					<?php if(!empty($pledge->username)){ ?><a href="/user/<?php echo h(st($pledge->user_slug)) ?>/" title="<?php echo h(st($pledge->username)) ?>"><?php } ?>
					
						<?php if (!empty($pledge->user_ext) && file_exists('uploads/users/'.$pledge->iduser.'_150x150.jpg')) { ?>
							<img <?php if($i % 4 == 0){ ?>class="latest"<?}?> width="52" height="52" src="/uploads/users/<?php echo $pledge->iduser; ?>_150x150.jpg" alt="<?php echo h(st($pledge->username)); ?>" />
						<?php }else{ ?>
							<img <?php if($i % 4 == 0){ ?>class="latest"<?}?> width="52" height="52" src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo h(st($pledge->username)) ?>" />
						<?php } ?>
					
					<?php if(!empty($pledge->username)){ ?></a><?php } ?>
					
					<?php
						if($i % 4 == 0){
							?><div class="clear"></div><?php 						}
					} 
				} 
			?>
			<div class="clear"></div>

		</div>
	<?php } ?>
<?php } ?>

<?php if(!empty($_SESSION['user']) && $project->iduser == $_SESSION['user']['iduser']){ ?>
	<div class="project_pledges_details logged-user">
		<span class="value"><?php echo count(@$backers)?> people</span>
		<span class="key">Have backed this project with<br /> their Money</span>
		<div class="clear10"></div>
		
		<?php if(count(@$backers) > 0){ ?>
			
			<?php foreach($backers AS $k => $backer){ 
				if(!$backer->public) {
					$backer->user_ext = $backer->location_preview = $backer->username = '';
				}
			?>
				<?php if(!empty($backer->username)){ ?><a href="/users/<?php echo $backer->user_slug;?>" title="View <?php echo $backer->username;?> profile">	<?php } ?>
					<?php if (!empty($backer->user_ext) && file_exists('uploads/users/'.$backer->iduser.'_40x40.jpg')) { ?>
						<img width="52" height="52" src="/uploads/users/<?php echo $backer->iduser;?>_40x40.jpg" alt="" />
					<?php } else { ?>
						<img width="52" height="52" src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo $backer->username;?>" />
					<?php } ?>
				<?php if(!empty($backer->username)){ ?></a><?php } ?>
				<?php if($k >= 2) break; ?>
			<?php } ?>
			
			<a class="view-all" href="/<?php echo @$project->slug?>/backers/" title="View all backers">ALL &rsaquo;</a>
			<div class="clear"></div>
		
			<a class="message-to-group" href="/<?php echo @$project->slug?>/backers/" title="Message backers">Message backers</a>
			<div class="clear"></div>
		<?php } ?>
	</div>
	<div class="clear"></div>
	<div class="project_pledges_details logged-user">
		<span class="value"><?php echo count(@$helpers)?> people</span>
		<span class="key">Have backed this project with<br /> their time / skills</span>
		<div class="clear10"></div>
		<?php if(count(@$helpers) > 0){ ?>
			<?php if(count(@$helpers)) { foreach($helpers AS $k => $helper){ ?>
				<a href="/users/<?php echo $helper->user_slug;?>" title="View <?php echo $helper->username;?> profile">	
					<?php if (!empty($helper->user_ext) && file_exists('uploads/users/'.$helper->iduser.'_40x40.jpg')) { ?>
						<img width="52" height="52" src="/uploads/users/<?php echo $helper->iduser;?>_40x40.jpg" alt="" />
					<?php } else { ?>
						<img width="52" height="52" src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo $helper->username;?>" />
					<?php } ?>
				</a>
				<?php if($k >= 2) break; ?>
			<?php } } ?>
				
			<a class="view-all" href="/<?php echo @$project->slug?>/helpers/" title="View all hepers">ALL &rsaquo;</a>
			<div class="clear"></div>
			<a class="message-to-group" href="/<?php echo @$project->slug?>/helpers/" title="Message helpers">Message helpers</a>
			<div class="clear"></div>
		<?php } ?>
	</div>
	<div class="clear"></div>	
	<?php /* if(!empty($amounts)){ ?>
		<div class="rewards-view">
			<span class="title">Rewards</span>
			
			
				<?php foreach($amounts as $i=>$amount){ ?>
					<div class="item<?php echo (($i+1)%2==0) ? ' odd' : '' ?>">
						<span class="pounds">&pound;<?php echo h(st($amount->amount)) ?></span>
						<span class="or-more">or more<?php if($amount->limited == 'yes') { ?>:<?php } ?></span>
						<?php if($amount->limited == 'yes') { ?>
							<div class="clear5"></div>
							<span class="pledges-left">
								<?php echo h(st($amount->number)) ?> pledge<?php echo ($amount->number > 1) ? 's' : '' ?> (<?php echo (int)($amount->number - $amount->pledges) ?> LEFT)
							</span>
						<?php } ?>
					</div>
				<?php } ?>
			
			<div class="clear"></div>	
		</div>
	<?php } */ ?>
<?php } ?>



<?php if(!empty($amounts)){ ?>
	<div class="fund-this-project">
		<?php foreach($amounts as $key => $amount){ ?>
			<div class="item <?php echo $key == 0 ? 'first' : '' ?>">
				<?php if(!empty($_SESSION['user'])){ ?>
					<form method="post" action="/projects/confirm/" id="amount_<?php echo h(st($amount->idamount)) ?>">
						<input type="hidden" name="amount" value="<?php echo h(st($amount->amount)) ?>" />
						<input type="hidden" name="idamount" value="<?php echo h(st($amount->idamount)) ?>" />
						<input type="hidden" name="idproject" value="<?php echo h(st($project->idproject)) ?>" />
					</form>
				<?php } ?>
				<span class="value">&pound;<?php echo $amount->amount?> 
				<?php if($amount->limited == 'yes' && $amount->remaining <= 0) { ?>
					<span class="sold">SOLD OUT!</span>
				<?php } else { ?>
					<?php if(!empty($_SESSION['user'])){ ?><a class="pledge" href="javascript:;" onClick="$('#amount_<?php echo h(st($amount->idamount)) ?>').submit();" title="Pledge <?php echo h(st($amount->amount)) ?>">Pledge</a><?php }else { ?><a class="pledge" href="/user/login/refer:projects+confirm+d:<?php echo h(st($amount->amount)) ?>-<?php echo h(st($amount->idamount)) ?>-<?php echo h(st($project->idproject)) ?>/" width="450" height="<?php echo LOGIN_HEIGHT ?>" rel="prettyPhoto" title="Login first">Pledge</a><?php }  ?>
				<?php } ?>
				</span>
				<span class="desc"><?php echo url_2_link($amount->description)?></span>
				<span class="info"><b><?php echo h(st($amount->pledges)) ?> pledge<?php echo ($amount->pledges == 1) ? '' : 's' ?> so far</b></span>
				<?php if ($amount->limited == 'yes' && $amount->remaining > 0) : ?> 
					<span class="info"><b><?php echo h(st($amount->remaining)) ?> more available</b></span>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
		<?php } ?>  
	</div>
<?php } ?>
<?php if(!empty($_SESSION['user']) && $project->iduser <> $_SESSION['user']['iduser'] OR empty($_SESSION['user'])){ ?>
	<div class="author">
		<div class="box">
			<span class="title left overflow">project belongs to...</span><div class="clear"></div>
			<div class="picture">
				<a href="/user/<?php echo h(st($project->user_slug)) ?>/" title="<?php echo h(st($project->username)) ?>">	
				
					<?php if (!empty($project->user_ext) && file_exists('uploads/users/'.$project->iduser.'_40x40.jpg')){ ?>
						<img width="51" height="51" src="/uploads/users/<?php echo $project->iduser; ?>_40x40.jpg" alt="<?php echo h(st($project->username)); ?>" />
					<?php }else{ ?>
						<img width="51" height="51" src="<?php echo DEFAULT_USER_THUMB?>" alt="<?php echo h(st($project->username)) ?>" />
					<?php } ?>
				</a>
			</div>
			<div class="left">
				<a class="fname" href="/user/<?php echo h(st($project->user_slug)) ?>/" title="><?php echo h(st($project->username)) ?>"><?php echo trunc(h(st($project->username)), 17) ?></a><div class="clear"></div>
				<a class="location-small" href="" title=""><?php echo h(st($project->location_preview)) ?> UK</a><div class="clear"></div>
				<?php if(!empty($_SESSION['user'])){ ?>
					<?php if($_SESSION['user']['iduser'] != $project->iduser){ ?>
						<a href="/messages/send/<?php echo h(st($project->iduser))?>/" width="550" height="<?php echo SEND_MESSAGE_HEIGHT ?>" rel="prettyPhoto" class="message" title="send message to '<?php echo h(st($project->username))?>'">
							Send message
						</a>   
					<?php } ?>
				<?php } else { ?>
					<a href="/user/login/" width="450" height="<?php echo LOGIN_HEIGHT ?>" rel="prettyPhoto" class="message" title="send message to '<?php echo h(st($project->username))?>'">
						Send message
					</a>   
				<?php } ?>
			</div>
			<?php if($project->user_bio) { ?>
				<div class="clear10"></div>
				<p>
					<?php echo h(st($project->user_bio))?>
					<a href="/user/<?php echo h(st($project->user_slug)) ?>/" title="View profile">view profile &rsaquo;</a>
				</p>
			<?php } ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php if($project->helpers == 1) { ?>
		<?php if($project->time) { ?>
			<div class="author">
				<div class="box">
					<span class="title left overflow">YOU CAN ALSO SUPPORT THIS PROJECT WITH YOUR TIME</span><div class="clear"></div>
					<p>If you have a spare hour (or more...) you can pledge that along with your money to help support this project. We are seeking <?php echo $project->time ?> hour<?php echo ($project->time > 1) ? 's' : '' ?> in total.</p>
					<?php /*<p><a href="#" title="">More</a> about time pledging</p>*/ ?>
					<div class="clear"></div>
				</div>
			</div>
		<?php } ?>
		<?php if(isset($project->skills) && $project->skills) { ?>
			<div class="author">
				<div class="box">
					<span class="title left overflow">YOU CAN ALSO SUPPORT THIS PROJECT WITH YOUR SKILLS</span><div class="clear"></div>
					<p>If you have a spare hour (or more...) you can pledge that along with your money to help support this project. This project is also seeking the following skills.</p> 
					<p>
						<?php 
							$project->skills = unserialize($project->skills);
							echo implode('<br />', $project->skills);
						?>
					</p>
					<?php /*<p><a href="#" title="">More</a> about skill pledging</p>*/ ?>
					<div class="clear"></div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
<?php } ?>

<?php if(!empty($_SESSION['user']) && $project->iduser <> $_SESSION['user']['iduser']){ ?>
	<div class="author">
		<div class="box">
			<div class="clear"></div>
			<a href="/projects/report/index/<?php echo $project->idproject ?>/" width="700" height="<?php echo REPORT_HEIGHT ?>" rel="prettyPhoto" title="Report this project">REPORT THIS PROJECT</a>
		</div>
	</div>
<?php } ?>