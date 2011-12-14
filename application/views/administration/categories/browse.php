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
			<td>Slug</td>
			<td>Actions</td>
			<td>Order</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $category){ ?>
				<tr id="<?php echo $category->idcategory ?>" style="background-color: #BBBBBB; color: #000000;">
					<td class="switch">
						<span class="turn_on<?php if($category->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($category->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo $category->title; ?></td>
					<td><?php echo $category->slug; ?></td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/categories/edit/'.$category->idcategory); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
					<td class="sort">
						<a class="sorting_up" href="/administration/categories/sort/up/idcategory/<?php echo $category->idcategory ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
						<a class="sorting_down" href="/administration/categories/sort/down/idcategory/<?php echo $category->idcategory ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
					</td>
				</tr>
				
				<?php 
					if(!empty($category->subcategories)){ 
						foreach($category->subcategories AS $subcategory){
							?>
								<tr id="<?php echo $subcategory->idcategory ?>">
									<td class="switch">
										<span class="turn_on<?php if($subcategory->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
										<span class="turn_off<?php if($subcategory->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
									</td>
									<td><?php echo $subcategory->title; ?></td>
									<td><?php echo $subcategory->slug; ?></td>
									<td class="icons" style="width: 90px;">
										<a href="<?php echo site_url('administration/categories/edit/'.$subcategory->idcategory); ?>" class="edit" title="Edit">Edit</a>
										<a class="delete" name="Delete" title="Delete">Delete</a>
									</td>
									<td class="sort">
										<a class="sorting_up" href="/administration/categories/sort/up/idcategory/<?php echo $subcategory->idcategory ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
										<a class="sorting_down" href="/administration/categories/sort/down/idcategory/<?php echo $subcategory->idcategory ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
									</td>
								</tr>
							<?php 
						} 
					}
				?>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>