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
			<td>Active</td>
			<td>Title</td>
			<td>Image</td>
			<td>Actions</td>
			<td>Sort</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $item){ ?>
				<tr id="<?php echo $item->idpartner ?>">
					<td class="switch">
						<span class="turn_on<?php if($item->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($item->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo $item->title; ?></td>
					<td>
						<?php if (!empty($item->image)){ ?>
							<img src="<?php echo $item->image; ?>?=<?php echo rand() ?>" height="<?php echo $configuration['footer_logos_height']; ?>"/>
						<?php } ?>
					</td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/partners/edit/'.$item->idpartner); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
					<td class="sort">
						<a class="sorting_up" href="/administration/partners/sort/up/id/<?php echo $item->idpartner ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
						<a class="sorting_down" href="/administration/partners/sort/down/id/<?php echo $item->idpartner ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
					</td>					
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>