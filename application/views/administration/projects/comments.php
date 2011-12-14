<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = 'projects/delete_comment';</script>

<div class="mainForm"><fieldset>
	<dl>
		<form id="search-form" method="post" action="/administration/projects/comments/">
			<dd>
				<label for="string" class="long">Search for </label>
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
			<td>Date</td>
			<td>Project</td>
			<td>Text</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $comment){ ?>
				<tr id="<?php echo $comment->idcomment ?>">
				
					<td>
						<?php 
							$date_added = new DateTime($comment->date_added); 
							echo $date_added->format('M d\t\h Y H:i'); 
						?>
					</td>
					<td>
						<?php echo $comment->project_title; ?>
						<?php if($comment->comments_count > 0){ ?>
							<a href="/administration/projects/comments/string/all/idproject/<?php echo $comment->idproject?>/">(<?php echo $comment->comments_count?>)</a>
						<?php } else { ?>
							(0)
						<?php } ?>
					</td>
					<td><?php echo $comment->text; ?></td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/projects/edit_comment/'.$comment->idcomment); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>