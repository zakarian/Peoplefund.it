<!doctype html> 
<html lang="en">
	<head>
		<title>People Fund<?php if(isset($page_title) && $page_title) echo ' - '.h(st($page_title)) ?></title>
		<meta charset="utf-8">
		<meta name="description" content="<?php if(isset($page_description) && $page_description) echo ' - '.h(st($page_description)) ?>" />
		<meta name="keywords" content="<?php if(isset($page_keywords) && $page_keywords) echo ' - '.h(st($page_keywords)) ?>" />

		<link href="/styles.php" media="screen" type="text/css" rel="stylesheet" />
		
		<?php if(isset($show_map) && $show_map) { ?>
			<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry"></script>
			<script  type="text/javascript" src="/js/site/map.js"></script>
		<?php } ?>
		<?php if(isset($current_page) && $current_page == 'message_send'){ ?>
			<script src="/js/jquery/jquery-ui-1.8.13.custom.min.js>" type="text/javascript"></script>
		<?php } ?>
		
		<script src="/script.php" type="text/javascript"></script>
		
		<!--[if lte IE 9]> 
			<script src="/js/site/jquery.corners.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					$('.rounded_class_top_3px').corner('top 3px');
					$('.view-projects .item h2').corner('3px');
					$('#header nav li.active a').corner('3px');
					$('.view-project .project_meta .fund-this-project .item a').corner('3px');
				});
			</script>
		<![endif]-->
		<!--[if lte IE 8]>
			<script type="text/javascript" src="/js/site/html5.js"></script>
		<![endif]-->
	
		
		<?php include('../application/views/templates/og_tags.php') ?>
		
		
	</head>

	<?php  if(@$show_map){ ?>
		<body onload="initialize('<?php echo (isset($searchSql['keywords']) && $searchSql['keywords']) ? $searchSql['keywords'] : 'empty'; ?>', '<?php echo (isset($searchSql['category']) && $searchSql['category']) ? $searchSql['category'] : 'empty'; ?>', '<?php echo (isset($searchSql['string']) && $searchSql['string']) ? $searchSql['string'] : 'empty'; ?>', '<?php echo (!empty($user_data)) ? $user_data['postcode'] : ""; ?>', '<?php echo (!empty($user_data)) ? $user_data['lat'] : ""; ?>', '<?php echo (!empty($user_data)) ? $user_data['lng'] : ""; ?>', '<?php echo (!empty($search_location_data['lat'])) ? $search_location_data['lat'] : ""; ?>', '<?php echo (!empty($search_location_data['lng'])) ? $search_location_data['lng'] : ""; ?>')" id="ajax">
	<?php } else { ?>
		<body id="ajax">
	<?php } ?>
		<?php if(!isset($ajax)) { ?>
		<div id="userbar">
			<div class="site-width">
				<div class="beta"></div>
				<ul>
					<?php if(empty($user_data)) { ?>
						<li class="first"><a href="/user/sign_up/" title="<?php echo all_pages_header_signup_button ?>" width="450" height="400" rel="prettyPhoto"><?php echo all_pages_header_signup_button ?></a>|</li>
						<li><a href="/user/login/" title="<?php echo all_pages_header_login_button ?>" width="450" height="<?php echo LOGIN_HEIGHT ?>" rel="prettyPhoto"><?php echo all_pages_header_login_button ?></a></li>
					<?php } else { ?>
						<li class="first"><b><?php echo h(st($user_data['username'])) ?></b></li>
						<li><a href="/user/<?php echo h(st($user_data['slug'])) ?>/" title="<?php echo all_pages_header_my_projects_button ?>"><?php echo all_pages_header_my_projects_button ?></a></li>      
						<li><a href="/messages/inbox/" title="<?php echo all_pages_header_messages_button ?>"><?php echo all_pages_header_messages_button ?></a><?php if (!empty($_SESSION['info']['new_messages'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_messages']; ?></span><?php } ?></li>           
						<li><a href="/notifications/" title="<?php echo all_pages_header_alerts_button ?>"><?php echo all_pages_header_alerts_button ?></a><?php if (!empty($_SESSION['info']['new_notifications'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_notifications']; ?></span><?php } ?></li>           
						<li><a href="/user/logout/" title="<?php echo all_pages_header_logout_button ?>"><?php echo all_pages_header_logout_button ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php if(@$current_page == "index" OR @$current_page == "about") { ?>
			<div id="marquee">
				<div class="site-width">
					<marquee behavior="scroll" scrollamount="2" direction="left">
						<span>Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched! <span>&nbsp;•&nbsp;</span> Peoplefund.it has just launched!</span>
					</marquee>
				</div>
			</div>
		<?php } ?>
		<header id="header">
			<div class="site-width">
				<h1><a href="/" title="People Fund" rel="home">People Fund</a></h1>
				<?php
					$pages = $this->db->query("SELECT * FROM `pages` WHERE `in_main` = 1 AND `active` = '1' AND `idsection` = '0' ORDER BY `order_main` DESC")->result();
					if($pages) {
				?>
				<nav>
					<ul>
						<?php foreach($pages as $item) { ?>
							<?php
								$itemCurrentPage = $item->slug;
								$attr = $active = '';
								if($item->slug == 'home') {
									$item->slug = '';
									$itemCurrentPage = 'index';
								} 
								if($item->slug == 'projects-add') {
									if(!empty($user_data))
										$item->slug = 'projects/checklist';
									else {
										$attr = ' width="450" height="'.LOGIN_HEIGHT.'" rel="prettyPhoto"';
										$item->slug = 'user/login/refer:projects+checklist/';
									}
									$itemCurrentPage = 'projects_add';
								}
								if($item->is_section == 1) {
									$subs = $this->db->query("SELECT * FROM `pages` WHERE `in_main` = 1 AND `active` = '1' AND idsection = $item->idpage ORDER BY `order_main` DESC")->result();	
									if($subs) {
										$sub = $subs[0];
										$slug = $item->slug;
										if($item->slug == 'help')
											$item->slug = $item->slug.'/faqs';
										else
											$item->slug = $item->slug.'/'.$sub->slug;
									}
								}
								if($itemCurrentPage == @$current_page) $active = TRUE;
							?>
							<li<?php if($active){ ?> class="active"<?php } ?>>
								<a<?php echo $attr ?> href="/<?php echo h(st($item->slug)); if(!empty($item->slug)) echo '/' ?>" title="<?php echo h(st($item->title)) ?>">
									<?php echo h(st($item->title)) ?>
								</a>
								<?php if($item->is_section == 1) { ?>
									<?php	
										if($subs) {
									?>
									<ul>	
										<?php foreach($subs as $k=>$sub) { ?>
											<li<?php echo (($k+1) == count($subs)) ? ' class="latest"' : '' ?>><a href="/<?php echo $slug ?>/<?php echo h(st($sub->slug)) ?>/" title="<?php echo h(st($sub->title)) ?>"><?php echo h(st($sub->title)) ?></a></li>
										<?php } ?>
									</ul>
								<?php } } ?>
							</li>
						<?php } ?>
					</ul>
				</nav>
				<?php } ?>
				<?php /*<form action="/projects/search/" method="post" onSubmit="return checkSearchString('header_search_string');">
					<fieldset>
						<input type="text" name="string" id="header_search_string" value="Search, town or postcode" class="query focus-style clear-text" />
						<input type="submit" name="" value="go" class="button" />
					</fieldset>
				</form>*/ ?>
				<div class="clear"></div>
			</div>
		</header>
		<?php } ?>	
