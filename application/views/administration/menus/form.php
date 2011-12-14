<form method="POST" action="" class="mainForm">
	<fieldset>
		<dl>
			<?php if(!empty($errors)): ?>
					<dd class="error">
						<strong>Errors:</strong><br />
						<?php foreach($errors as $e): ?>
									&nbsp;&nbsp;<img src="<?php echo site_url('img/bullet3.gif'); ?>" alt="" />&nbsp;<?php echo ucfirst($e) ?><br />
						<?php endforeach; ?>
					</dd>
			<?php endif; ?>

			<dd>
				<h3>Menu details</h3>
			</dd>
			<dd>
				<label for="title" class="long">Title</label>
				<input type="text" id="title" name="title" class="long validate" value="<?php echo @$post['title']?>"/>
			</dd>
		
			<dd>
				<label for="idsubmenu" class="long">Submenu of</label>
				<select name="idsubmenu" class="long validate">
					<option value="0">None</option>
					
					<?php if($menus){ ?>
					
						<?php 
						function showMenu($menus, $active_menu = ""){
							foreach ($menus as $k => $menu){ ?>
									<option value="<?php echo $menu['idmenu']?>" <?php if($menu['idmenu'] == $active_menu){ echo "SELECTED"; } ?> style="padding-left: 
										<?php 
											if($menu['index'] == 1){ 
												echo "0"; 
											} else if($menu['index'] == 2){
												echo "10"; 
											} else {
												echo $menu['index'] * 10;
											}											
										?>px;">
										<?php echo $menu['title']?>
									</option>
					<?php 
								if(!empty($menu['submenus'])){
									showMenu($menu['submenus'], $active_menu);
								}
							}

						}
						
						if(!empty($post['idsubmenu'])){
							showMenu($menus, $post['idsubmenu']);
						} else {
							showMenu($menus);
						}
					?>
					<?php } ?>
				</select>
			</dd>
			
			<dd>
				<label for="active" class="long">Active</label>
				<select name="active" class="long validate">
					<option value="" DISABLED>- Please select -</option>
					<option value="1" <?php if(@$post['active'] == "1"){ ?>SELECTED<?php } ?>>Active</option>
					<option value="0" <?php if(@$post['active'] == "0"){ ?>SELECTED<?php } ?>>Inactive</option>
				</select>
			</dd>
			<dd>
				<label for="target" class="long">Target</label>
				<select name="target" class="long validate" onChange="showDiv(this.value);">
					<option value="" SELECTED DISABLED>- Please select -</option>
					<option value="page" <?php if(!empty($post['idpage'])){ ?>SELECTED<?php } ?>>Page</option>
					<option value="url" <?php if(!empty($post['url'])){ ?>SELECTED<?php } ?>>URL</option>
					<option value="action" <?php if(!empty($post['action'])){ ?>SELECTED<?php } ?>>Prepared Action</option>
				</select>
				
				<script>
					function showDiv(selected){
						if(selected == "page"){
							$("#page").show();
							$("#url").hide();
							$("#action").hide();
						} else if(selected == "url"){
							$("#url").show();
							$("#page").hide();
							$("#action").hide();
						} else {
							$("#action").show();
							$("#url").hide();
							$("#page").hide();
						}
					}
				</script>
			</dd>
			<dd class="hidden" id="page">
				<label for="idpage" class="long">Select page</label>
				<select name="idpage" class="long">
					<?php if(!empty($pages)){ 
							foreach($pages AS $page){
								?>
									<option value="<?php echo $page->idpage?>" <?php if($page->idpage == @$post['idpage']){ echo "SELECTED"; }?>><?php echo $page->title?></option>
								<?php 
							}
					   } else { ?>
						<option value="0">None</option>
					<?php } ?>
				</select>
			</dd>
			
			<dd class="hidden" id="url">
				<label for="url" class="long">Enter URL</label>
				<input type="text" id="url" name="url" class="long" value="<?php echo @$post['url']?>"/>
			</dd>
			
			<dd class="hidden" id="action">
				<label for="action" class="long">Select Action</label>
				<select name="action" class="long">
					<option value="/projects/" <?php if(@$post['action'] == "/projects/"){ echo "SELECTED"; } ?>>Browse Projects</option>
					<option value="/" <?php if(@$post['action'] == "/"){ echo "SELECTED"; } ?>>Home page</option>
				</select>
			</dd>
			
			<?php if(!empty($post['idpage'])){ ?>
				<script>showDiv("page");</script>
			<?php } else if(!empty($post['url'])){ ?>
				<script>showDiv("url");</script>
			<?php } else if(!empty($post['action'])){ ?>
				<script>showDiv("action");</script>
			<?php } ?>
					
			<dd class="submitDD">
				<a href="/administration/<?php echo $current_module?>/"><img src="<?php echo site_url('img/buttonCancel.gif'); ?>" alt="Cancel" title="Cancel" border="0" class="cancel" /></a>
				<input type="image" class="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>" name="submit" />
			</dd>
		</dl>
	</fieldset>
</form>