<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<?php if(!isset($filterbysection)) { ?>
<div class="mainForm"><fieldset>
	<dl>
		<form id="search-form" method="post" action="/administration/pledges/">
			<dd>
				<label for="filterbynav" class="long">Filter by navigation</label>
				<select id="filterbynav" name="filterbynav" class="long" style="margin-bottom: 4px;">
					<option value="0">- All -</option>
					<option value="in_main"<?php if(@$filterbynav == 'in_main'){ echo ' selected="selected"'; }?>>Header nav</option>
					<option value="in_foot"<?php if(@$filterbynav == 'in_foot'){ echo ' selected="selected"'; }?>>Footer nav</option>
				</select>
			</dd>
			<script>
				$(function() {
					$('#filterbynav').change(function(){
						window.location = '/administration/<?php echo $current_module; ?>/index/'+$(this).val();
					});
				});
			</script>
		</form>
	</dl>
</fieldset></div>	
<?php } ?>

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
			<td<?php if(isset($filterbynav) && $filterbynav) { ?> colspan="2"<?php } ?>>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($pages) || !empty($sections)){ ?>
	<tbody>
		<?php 
			if(!empty($pages)){
				foreach ($pages as $page){ ?>
					<tr id="<?php echo $page->idpage ?>">
						<td class="switch">
							<?php if($page->active == '2') { ?>
								<center>-</center>
							<?php } else { ?>
								<span class="turn_on<?php if($page->active == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
								<span class="turn_off<?php if($page->active == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
							<?php } ?>
						</td>
						<td>
							<?php if($page->is_section == 1) { ?>
								<a href="/administration/pages/index/section/<?php echo $page->idpage ?>/" title="<?php echo h(st($page->title)) ?>">
									<?php echo h(st($page->title)) ?>
								</a>
							<?php } else { ?>
								<a href="/administration/pages/edit/<?php echo $page->idpage ?>" title="<?php echo h(st($page->title)) ?>">
									<?php echo h(st($page->title)) ?>
								</a>
							<?php } ?>
						</td>
						<td><?php echo $page->slug; ?></td>
						<td class="icons" style="width: 90px;">
							<a href="<?php echo site_url('administration/pages/edit/'.$page->idpage); ?>" class="edit" title="Edit">Edit</a>
							<?php if($page->active != '2') { ?>
								<a class="delete" name="Delete" title="Delete">Delete</a>
							<?php } ?>
							<?php if($page->is_section == 1) { ?>
								<a class="browse" href="/administration/pages/index/section/<?php echo $page->idpage ?>/" title="<?php echo h(st($page->title)) ?>">
									<?php echo h(st($page->title)) ?>
								</a>
							<?php } ?>
						</td>
						<?php if(isset($filterbynav) && $filterbynav) { ?>
							<td class="sort">
								<a class="sorting_up" href="/administration/<?php echo $current_module; ?>/sort/up/idpage/<?php echo $page->idpage ?>/nav/<?php echo $filterbynav ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
								<a class="sorting_down" href="/administration/<?php echo $current_module; ?>/sort/down/idpage/<?php echo $page->idpage ?>/nav/<?php echo $filterbynav ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
							</td>
						<?php } ?>
					</tr>
		<?php 				} 
			}
		?>
	</tbody>
<?php } ?>

	
</table>