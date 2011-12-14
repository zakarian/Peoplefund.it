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
			<td>Sort</td>
		</tr>
	</thead>
	
<?php if(!empty($menus)){ ?>
	<tbody>
		<?php 
			function showMenu($menus){
				foreach ($menus as $k => $menu){ ?>
					<tr id="<?php echo $menu['idmenu'] ?>">
						<td class="switch">
							<?php if($menu['active'] == '2') { ?>
								<center>-</center>
							<?php } else { ?>
								<span class="turn_on<?php if($menu['active'] == '0') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
								<span class="turn_off<?php if($menu['active'] == '1') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
							<?php } ?>
						</td>
						<td style="padding-left: <?php echo  ($menu['index'] != 1) ? $menu['index'] * 15 : 10 ; ?>px;">
						
						<?php echo $menu['title']; ?></td>
						<td class="icons" style="width: 90px;">
							<a href="<?php echo site_url('administration/menu/edit/'.$menu['idmenu']); ?>" class="edit" title="Edit">Edit</a>
							<a class="delete_button delete_menu" name="Delete" title="Delete">Delete</a>
						</td>
						<td class="sort">
							<a class="sorting_up" href="/administration/menu/sort/up/idcategory/<?php echo $menu['idmenu'] ?>"><img src="/share/images/up.png" title="Move up" class="up"></a>
							<a class="sorting_down" href="/administration/menu/sort/down/idcategory/<?php echo $menu['idmenu'] ?>"><img src="/share/images/down.png" title="Move down" class="down"></a>
						</td>
					</tr>
		<?php 
					
					if(!empty($menu['submenus'])){
						showMenu($menu['submenus']);
					}
				}

			}
			
			showMenu($menus);
		?>
	</tbody>
<?php } ?>

	
</table>