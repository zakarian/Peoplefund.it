<?php if(!empty($_SESSION['user']) && $_SESSION['user']['iduser'] == @$user->iduser){ ?>
	<ul class="tabs">
		<li><a href="/user/<?php echo h(st(h(st(@$user->slug)))) ?>/"<?php if($tab == "profile"){ ?> class="active"<?php } ?> title="My profile">My profile</a></li>
		<li><a href="/user/<?php echo h(st(@$user->slug))?>/projects/"<?php if($tab == "projects"){ ?> class="active"<?php } ?> title="Projects">Projects</a></li>
		<li><a href="/messages/inbox/"<?php if($tab == "messages"){ ?> class="active"<?php } ?> title="Messages"><span>Messages</span>
			<?php if (!empty($_SESSION['info']['new_messages'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_messages'] ?></span><?php } ?>
		</a></li>  
		<li><a href="/notifications/"<?php if($tab == "notifications"){ ?> class="active"<?php } ?> title="Alerts"><span>Alerts</span>
			<?php if (!empty($_SESSION['info']['new_notifications'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_notifications'] ?></span><?php } ?>
		</a></li>  
	</ul>
<?php } else { ?>
	<ul class="tabs">
		<?php if($tab == "messages") $user->slug = $_SESSION['user']['slug']; ?>
		<?php if($tab == "notifications") $user->slug = $_SESSION['user']['slug']; ?>
		<li><a<?php if($tab == "profile"){ ?> class="active"<?php } ?> href="/user/<?php echo h(st(@$user->slug))?>/" title="<?php echo h(st(@$user->username))?> profile">Profile</a></li>  
		<li><a<?php if($tab == "projects"){ ?> class="active"<?php } ?> href="/user/<?php echo h(st(@$user->slug))?>/projects/" title="<?php echo h(st(@$user->username))?> Projects">Projects</a></li>  	
		<?php if($tab == "messages" OR $tab == "notifications") { ?>
			<li><a href="/messages/inbox/"<?php if($tab == "messages"){ ?> class="active"<?php } ?> title="Messages"><span>Messages</span>
				<?php if (!empty($_SESSION['info']['new_messages'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_messages'] ?></span><?php } ?>
			</a></li>  
			<li><a href="/notifications/"<?php if($tab == "notifications"){ ?> class="active"<?php } ?> title="Alerts"><span>Alerts</span>
				<?php if (!empty($_SESSION['info']['new_notifications'])){ ?><span class="alerts"><?php echo $_SESSION['info']['new_notifications'] ?></span><?php } ?>
			</a></li>  
		<?php } ?>
	</ul>
<?php } ?>