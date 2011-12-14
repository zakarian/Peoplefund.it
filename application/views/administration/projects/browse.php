<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<div class="mainForm"><fieldset>
	<dl>
		<dd>
			<label class="long">Stats:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0; line-height: 18px;">
				<span style="font-weight: normal;">
					Number of new projects in the last 24 hrs: <?php echo $stats['1']; ?><br />
					Number of new projects in the last 7 days: <?php echo $stats['7']; ?><br />
					Number of new projects in the last 30 days: <?php echo $stats['30']; ?><br />
					Total number of projects: <?php echo $stats['total']; ?><br />
				</span>
			</label>
		</dd>

		<form id="search-form" method="post" action="/administration/projects/">
			<dd>
				<label for="string" class="long">Search for</label>
				<input type="text" id="string" name="string" class="long" value="<?php echo @$string; ?>" style="margin-bottom: 4px;" />
				<label for="status" class="long">Status</label>
				<select id="status" name="status" class="long" style="margin-bottom: 4px;">
					<option value="">- All -</option>
					<option value="open" <?php if(@$status == "open"){ echo "SELECTED"; }?>>Open</option>
					<option value="closed" <?php if(@$status == "closed"){ echo "SELECTED"; }?>>Closed</option>
					<option value="temp" <?php if(@$status == "temp"){ echo "SELECTED"; }?>>Temp</option>
					<option value="editors_pick" <?php if(@$status == "editors_pick"){ echo "SELECTED"; }?>>Editors pick</option>
				</select>
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
			<td>Editors Pick</td>
			<td>User</td>
			<td>Title</td>
			<td>Amount</td>
			<td>Amount raised</td>
			<td>Date Created</td>
			<td>Date Expire</td>
			<td>Status</td>
			<td>Actions</td>
			<?php if(@$status == "editors_pick") : ?> 
			<td>Sort</td>
			<?php endif;?>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $project){ ?>
				<tr id="<?php echo $project->idproject ?>">
					<td class="switch">
						<span class="turn_on<?php if($project->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($project->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td class="switch_picks">
						<span class="turn_on_pick<?php if($project->editors_pick == '0') echo ' hidden'?>"><a class="turn_on_pick" name="turn_on_pick" title="Turn Off"></a></span>
						<span class="turn_off_pick<?php if($project->editors_pick == '1') echo ' hidden'?>"><a class="turn_off_pick" name="turn_off_pick" title="Turn On"></a></span>
					</td>
					<td><?php echo $project->username; ?></td>
					<td><?php echo $project->title; ?></td>
					<td>&pound;<?php echo $project->amount; ?></td>
					<td>
						&pound;<?php echo $project->amount_pledged; ?> 
						<?php if($project->pledges_count > 0){ ?>
							<a href="/administration/pledges/index/string/new/<?php echo $project->title?>/">(<?php echo $project->pledges_count?>)</a>
						<?php } else { ?>
							(0)
						<?php } ?>
					</td>
					<td>
						<?php 
							$date_register = new DateTime($project->date_created); 
							echo $date_register->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td>
						<?php 
							$date_register = new DateTime($project->date_expire); 
							echo $date_register->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td><?php echo ucfirst($project->status); ?></td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/projects/edit/'.$project->idproject); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
						<a href="/administration/projects/comments/string/all/idproject/<?php echo $project->idproject?>" class="browse" name="Comments" title="Comments">Comments</a>
					</td>
					<?php if(@$status == "editors_pick") : ?> 
					<td class="sort">
						<a class="sorting_up" href="/administration/projects/sort/up/idproject/<?php echo $project->idproject ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
						<a class="sorting_down" href="/administration/projects/sort/down/idproject/<?php echo $project->idproject ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
					</td>
					<?php endif;?>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>