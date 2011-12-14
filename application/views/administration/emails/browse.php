<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

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
			<td>Status</td>
			<td>Subject</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $email){ ?>
				<tr id="<?php echo $email->idemail ?>">
					<td class="switch">
						<?php if($email->active == '2') { ?>
							<center>-</center>
						<?php } else { ?>
							<span class="turn_on<?php if($email->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
							<span class="turn_off<?php if($email->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
						<?php } ?>
					</td>
					<td><?php echo $email->subject; ?></td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/emails/edit/'.$email->idemail); ?>" class="edit" title="Edit">Edit</a>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>