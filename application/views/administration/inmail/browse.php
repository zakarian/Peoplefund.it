<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<div class="mainForm"><fieldset>
	<dl>
		<dd>
			<label class="long">Stats:</label>
			<label class="long" style="width: 300px; text-align: left; font-weight: bold; padding-left: 0; margin-left: 0; line-height: 18px;">
				<span style="font-weight: normal;">
					Number of new messages in the last 24 hrs: <?php echo $stats['1']; ?><br />
					Number of new messages in the last 7 days: <?php echo $stats['7']; ?><br />
					Number of new messages in the last 30 days: <?php echo $stats['30']; ?><br />
					Total number of messages: <?php echo $stats['total']; ?><br />
				</span>
			</label>
		</dd>

		<form id="search-form" method="post" action="/administration/inmail/">
			<dd>
				<label for="string" class="long">Search for</label>
				<input type="text" id="string" name="string" class="long" value="<?php echo @$string; ?>" style="margin-bottom: 4px;" />
				<label for="sender" class="long">Sender</label>
				<input type="text" id="sender" name="sender" class="long" value="<?php echo @$sender; ?>" style="margin-bottom: 4px;" />
				
				<script>
					$(function() {

						$( "#sender" ).autocomplete({
							source: "/administration/users/autocomplete/",
							minLength: 2,
							select: function( event, ui ) {}
						});
					});
				</script>
				
				<label for="receiver" class="long">Receiver</label>
				<input type="text" id="receiver" name="receiver" class="long" value="<?php echo @$receiver; ?>" style="margin-bottom: 4px;" />
				
				<script>
					$(function() {

						$( "#receiver" ).autocomplete({
							source: "/administration/users/autocomplete/",
							minLength: 2,
							select: function( event, ui ) {}
						});
					});
				</script>
				
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
			<td>Date</td>
			<td>Sender</td>
			<td>Receiver</td>
			<td>Title</td>
			<td>Message</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $message){ ?>
				<tr id="<?php echo $message->idmessage ?>">
				
					<td>
						<?php 
							$date_sent = new DateTime($message->date_sent); 
							echo $date_sent->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td><?php echo $message->sender_username; ?></td>
					<td><?php echo $message->receiver_username; ?></td>
					<td><?php echo $message->title; ?></td>
					<td><?php echo $message->text; ?></td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/inmail/edit/'.$message->idmessage); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>
</table>