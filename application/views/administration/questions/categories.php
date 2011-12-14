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
			<td>Title</td>
			<td>Questions</td>
			<td>Actions</td>
			<td>Order</td>
		</tr>
	</thead>
	
<?php if(!empty($categories)){ ?>
	<tbody>
		<?php foreach ($categories as $category){ ?>
				<tr id="<?php echo $category->id ?>" style="background-color: #BBBBBB; color: #000000;">
					<td class="switch">
						<span class="turn_on<?php if($category->status == 'inactive') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($category->status == 'active') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo $category->title; ?></td>
					<td>( <a title="See the questions from this category" href="/administration/questions/index/category/<?php echo $category->id ?>/"><?php echo $category->category_questions; ?></a> )</td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/qacategories/edit/'.$category->id); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
					<td class="sort">
						<a class="sorting_up" href="/administration/qacategories/sort/up/idcategory/<?php echo $category->id ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
						<a class="sorting_down" href="/administration/qacategories/sort/down/idcategory/<?php echo $category->id ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
					</td>
				</tr>
				
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>