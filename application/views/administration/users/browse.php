<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<div class="mainForm"><fieldset>
	<dl>
		<?php if(!empty($stats)){ ?>
			<dd>
				<label class="long">Stats:</label>
				<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0; line-height: 18px;">
					<span style="font-weight: normal;">
						Number of new registrations in the last 24 hrs: <?php echo $stats['1']; ?><br />
						Number of new registrations in the last 7 days: <?php echo $stats['7']; ?><br />
						Number of new registrations in the last 30 days: <?php echo $stats['30']; ?><br />
						Total number of registrations: <?php echo $stats['total']; ?><br /><br />
						
						Total number of FB registrations: <?php echo $stats['fb_total']; ?><br />
						Total number of ES registrations: <?php echo $stats['es_total']; ?><br />
					</span>
				</label>
			</dd>
		<?php } ?>

		<form id="search-form" method="post" action="/administration/users/<?php echo (isset($subslug) && $subslug) ? $subslug : '' ?>/">
			<dd>
				<label for="filter" class="long">Filter by letter:</label>
				<label class="short">
					<a href="/administration/<?php echo $current_module?>/">All</a> | 
					<?php $letters = str_split('#abcdefghijklmnopqrstuvwxyz'); ?>
					<?php foreach ($letters as $letter): ?>
							<?php if (@$filters['letter'] == strtoupper($letter)): ?>
									<b><?php echo strtoupper($letter) ?></b>
							<?php else: ?>
									<a href="/administration/<?php echo $current_module ?>/<?php echo (isset($subslug) && $subslug) ? $subslug : 'index' ?>/letter/<?php echo strtoupper($letter) ?>/string/all/"><?php echo strtoupper($letter); ?></a>
							<?php endif; ?>
					<?php endforeach ?>
				</label>
			</dd>
			<dd>
				<label for="string" class="long">Search for</label>
				<input type="text" id="string" name="string" class="long" value="<?php echo @$string; ?>" style="margin-bottom: 4px;" />
				<input id="search-submit" type="image" src="<?php echo site_url('img/buttonSearch.gif'); ?>" title="Search" style="margin-left: 5px;" />
			</dd>
		</form>
	</dl>
</fieldset></div>	

<?php if (!empty($message)){ ?>
		<div class="mainForm session-messages">
			<fieldset style="border: 1px dashed #68bc5b;">
				<center><?php echo $message?></center>
			</fieldset>
		</div>
<?php } ?>

<table id="listing" width="670" cellpadding="0" cellspacing="0" class="resultsTable">
	<thead>
		<tr>
			<td>Active</td>
			<td>Username</td>
			<td>Email</td>
			<td>Registered</td>
			<td>Last Login</td>
			<td>Projects</td>
			<td>Confirmed</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $administrator){ ?>
				<tr id="<?php echo $administrator->iduser ?>">
					<td class="switch">
						<span class="turn_on<?php if($administrator->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($administrator->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo $administrator->username; ?></td>
					<td><?php echo $administrator->email; ?></td>
					<td>
						<?php 
							$date_register = new DateTime($administrator->date_register); 
							echo $date_register->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td>
						<?php 
							if($administrator->date_login != "0000-00-00 00:00:00"){
								$date_login = new DateTime($administrator->date_login); 
								echo $date_login->format('M d\t\h Y H:i'); 
							} else {
								echo "-";
							}
						?>
					</td>
					<td>
						<?php if(!empty($administrator->projects)){ ?>
						
							<?php foreach($administrator->projects AS $project){ ?>
								<a href="/administration/projects/edit/<?php echo $project->idproject?>/"><?php echo $project->title?></a><br>
							<?php } ?>
						
						<?php } else { echo "-"; } ?>
					</td>
					<td>
						<?php if($administrator->confirmed == 1){ ?>
							Yes
						<?php } else { ?>
							No
						<?php } ?>
					</td>
					<td class="icons" style="width: 152px;">
						<a href="<?php echo site_url('administration/users/edit/'.$administrator->iduser); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
						<a class="login" href="<?php echo site_url('administration/users/login/'.$administrator->iduser); ?>" name="Login" title="Login as <?php echo $administrator->username?>">Login</a>
						<?php if( !empty( $administrator->fbid ) ) { ?><a title="View facebook profile" class="facebook" href="http://facebook.com/profile.php?id=<?php echo  $administrator->fbid ?>" target="_blank">View facebook profile</a><?php } ?>
						<?php if( !empty( $administrator->esid ) ) { ?><a title="View energyshare profile" class="energyshare" href="http://www.energyshare.com/profile/id:<?php echo  $administrator->esid ?>/" target="_blank">View energyshare profile</a><?php } ?>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

</table>