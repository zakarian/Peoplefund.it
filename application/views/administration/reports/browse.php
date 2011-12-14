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
			<?/*<td>Revised</td>*/?>
			<td>Date</td>
			<td>Project</td>
			<td>Member</td>
			<td>Type</td>
			<td>Text</td>
			<td>Host</td>
		</tr>
	</thead>
	
<?php if(isset($items) && !empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $project){ ?>
				<tr id="<?php echo $project->id ?>">
					<?/*<td class="switch">
						<span class="turn_on<?php if($project->revised == 'no') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($project->revised == 'yes') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>*/?>
					<td><?php echo $project->date; ?></td>
					<td>
						<a href="/administration/projects/reports/index/p-<?php echo $project->project_id ?>/" title="Filter by project">
							<?php echo h(st($project->project_title)) ?>
						</a>
					</td>
					<td>	
						<a href="/administration/projects/reports/index/u-<?php echo $project->user_id ?>/" title="Filter by username">
							<?php echo h(st($project->username)) ?>
						</a>
					</td>
					<td>
						<?php 
							if($project->type == 'illegal_activity')
								echo 'Illegal activity';
							else if($project->type == 'peoplefund_guidelines')
								echo 'Peoplefund.it guidelines';
							else if($project->type == 'wrong_category')
								echo 'Wrong category';
							else if($project->type == 'other')
								echo 'Other';
						?>
					</td>
					<td><?php echo h(st($project->text)) ?></td>
					<td><?php echo h(st($project->host)) ?></td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>