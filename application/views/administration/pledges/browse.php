<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<div class="mainForm"><fieldset>
	<dl>
		<dd>
			<label class="long" style="padding-top: 8px;">Stats for today:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0;">
				<table width="450" cellpadding="0" cellspacing="2">
					<?php if(!empty($stats[1])) { ?>
						<?php foreach($stats[1] as $pledge_type){ ?>
							<tr>
								<td width="15%">Status:</td> 
								<td width="20%" style="font-weight: normal;"><?php echo $pledge_type->status; ?></td>
								<td width="15%">Count:</td>  
								<td width="10%" style="font-weight: normal;"><?php echo $pledge_type->projects_count; ?></td>
								<td width="15%">Amount:</td>  
								<td style="font-weight: normal;"><?php echo $pledge_type->projects_amount; ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td width="15%">Status:</td> 
							<td width="20%" style="font-weight: normal;">-</td>
							<td width="15%">Count:</td>  
							<td width="10%" style="font-weight: normal;">-</td>
							<td width="15%">Amount:</td>  
							<td style="font-weight: normal;">-</td>
						</tr>
					<?php } ?>
				</table>
			</label>
			<br clear="all">
			<label class="long" style="padding-top: 8px;">Stats for last 7 days:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0;">
				<table width="450" cellpadding="0" cellspacing="2">
					<?php if(!empty($stats[7])) { ?>
						<?php foreach($stats[7] as $pledge_type){ ?>
							<tr>
								<td width="15%">Status:</td> 
								<td width="20%" style="font-weight: normal;"><?php echo $pledge_type->status; ?></td>
								<td width="15%">Count:</td>  
								<td width="10%" style="font-weight: normal;"><?php echo $pledge_type->projects_count; ?></td>
								<td width="15%">Amount:</td>  
								<td style="font-weight: normal;"><?php echo $pledge_type->projects_amount; ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td width="15%">Status:</td> 
							<td width="20%" style="font-weight: normal;">-</td>
							<td width="15%">Count:</td>  
							<td width="10%" style="font-weight: normal;">-</td>
							<td width="15%">Amount:</td>  
							<td style="font-weight: normal;">-</td>
						</tr>
					<?php } ?>
				</table>
			</label>
			<br clear="all">
			<label class="long" style="padding-top: 8px;">Stats for last 30 days:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0;">
				<table width="450" cellpadding="0" cellspacing="2">
					<?php if(!empty($stats[30])) { ?>
						<?php foreach($stats[30] as $pledge_type){ ?>
							<tr>
								<td width="15%">Status:</td> 
								<td width="20%" style="font-weight: normal;"><?php echo $pledge_type->status; ?></td>
								<td width="15%">Count:</td>  
								<td width="10%" style="font-weight: normal;"><?php echo $pledge_type->projects_count; ?></td>
								<td width="15%">Amount:</td>  
								<td style="font-weight: normal;"><?php echo $pledge_type->projects_amount; ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td width="15%">Status:</td> 
							<td width="20%" style="font-weight: normal;">-</td>
							<td width="15%">Count:</td>  
							<td width="10%" style="font-weight: normal;">-</td>
							<td width="15%">Amount:</td>  
							<td style="font-weight: normal;">-</td>
						</tr>
					<?php } ?>
				</table>
			</label>
			<br clear="all">
			<label class="long" style="padding-top: 8px;">Total stats:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0;">
				<table width="450" cellpadding="0" cellspacing="2">
					<?php if(!empty($stats['total'])) { ?>
						<?php foreach($stats['total'] as $pledge_type){ ?>
							<tr>
								<td width="15%">Status:</td> 
								<td width="20%" style="font-weight: normal;"><?php echo $pledge_type->status; ?></td>
								<td width="15%">Count:</td>  
								<td width="10%" style="font-weight: normal;"><?php echo $pledge_type->projects_count; ?></td>
								<td width="15%">Amount:</td>  
								<td style="font-weight: normal;"><?php echo $pledge_type->projects_amount; ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td width="15%">Status:</td> 
							<td width="20%" style="font-weight: normal;">-</td>
							<td width="15%">Count:</td>  
							<td width="10%" style="font-weight: normal;">-</td>
							<td width="15%">Amount:</td>  
							<td style="font-weight: normal;">-</td>
						</tr>
					<?php } ?>
				</table>
			</label>
		</dd>
		
		<form id="search-form" method="post" action="/administration/pledges/">
			<dd>
				<label for="string" class="long">Project</label>
				<input type="text" id="string" name="string" class="long" value="<?php echo @$string; ?>" style="margin-bottom: 4px;" />
				<label for="status" class="long">Status</label>
				<select id="status" name="status" class="long" style="margin-bottom: 4px;">
					<option value="">- All -</option>
					<option value="transferred" <?php if(@$status == "transferred"){ echo "SELECTED"; }?>>Success</option>
					<option value="pending" <?php if(@$status == "pending"){ echo "SELECTED"; }?>>Not authorized</option>
					<option value="accepted" <?php if(@$status == "accepted"){ echo "SELECTED"; }?>>Authorized</option>
					<option value="cancelled" <?php if(@$status == "cancelled"){ echo "SELECTED"; }?>>Cancelled</option>
					<option value="failed" <?php if(@$status == "failed"){ echo "SELECTED"; }?>>Failed</option>
				</select>
				<input id="search-submit" type="image" src="<?php echo site_url('img/buttonSearch.gif'); ?>" title="Search" style="margin-left: 5px;" />
			</dd>
			
			<script>
				$(function() {
					$( "#string" ).autocomplete({
						source: "/administration/projects/autocomplete/",
						minLength: 2,
						select: function( event, ui ) {}
					});
				});
			</script>
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
			<td>Date Added</td>
			<td>Project</td>
			<td>Amount</td>
			<td>User</td>
			<td>Pledge email</td>
			<td>Key</td>
			<td>Status</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $pledge){ ?>
				<tr id="<?php echo $pledge->idproject; ?>">
					<td>
						<?php 
							$date_added = new DateTime($pledge->date_added); 
							echo $date_added->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td>
						<a href="/administration/pledges/index/string/<?php echo $pledge->title?>/">
							<?php echo $pledge->title; ?>
						</a>
					</td>
					<td><?php echo $pledge->amount; ?></td>
					<td><?php echo $pledge->username; ?></td>
					<td><?php echo $pledge->email; ?></td>
					<td><?php echo $pledge->key; ?></td>
					<td>
						<?php 
							switch($pledge->status){ 
								case "transferred":
									echo "Success";
									break;
								case "pending":
									echo "Not authorized";
									break;
								case "accepted":
									echo "Authorized";
									break;
								case "cancelled":
									echo "Cancelled";
									break;
								case "failed":
									echo "Failed";
									break;
								default:
									break;
							}
						?>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

</table>