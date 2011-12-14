<form class="mainForm" action="" method="post" name="settings">

	<?php if (!empty($message)){ ?>
		<div class="mainForm session-messages">
			<fieldset style="border: 1px dashed #68bc5b;">
				<center><?php echo $message?></center>
			</fieldset>
		</div>
	<?php } ?>

	<fieldset>
		<dl>
			<dd>
				<input type="hidden" value="update" name="action">

				<label class="long"></label>
				<label class="short"><strong>Base settings</strong></label>
			</dd>
			<dd>
				<label class="long" for="site_title">Site title</label>
				<input type="text" value="<?php echo @$data['site_title']?>" class="long" name="site_title" id="site_title">
			</dd>
			<dd>
				<label class="long" for="site_description">Site Description<small>This text will be used as HTML description for all website pages without specific description values.</small></label>
				<textarea cols="15" rows="3" class="noeditor long" name="site_description" id="site_description"><?php echo @$data['site_description']?></textarea>
			</dd>
			<dd>
				<label class="long" for="site_keywords">Site Keywords<small>The content of this field will be used as HTML keywords for all website pages without specific keywords values.</small></label>
				<textarea cols="15" rows="3" class="noeditor long" name="site_keywords" id="site_keywords"><?php echo @$data['site_keywords']?></textarea>
			</dd>
			<dd>
				<label class="long" for="admin_email">Administrator email<small>This mail address will receive all CMS system messages.</small></label>
				<input type="text" value="<?php echo @$data['admin_email']?>" class="long" name="admin_email" id="admin_email">
			</dd>
			<dd>
				<label class="long" for="notify_email">Notification email<small>Here you will receive copy of every email</small></label>
				<input type="text" value="<?php echo @$data['notify_email']?>" class="long" name="notify_email" id="notify_email">
			</dd>
			<dd>
				<label class="long" for="footer_logos_height">Partners logo height<small>Height of footer's partners logos in pixels</small></label>
				<input type="text" value="<?php echo @$data['footer_logos_height']?>" class="long" name="footer_logos_height" id="footer_logos_height">
			</dd>
			<dd class="submitDD">
				<input type="image" name="submit" src="<?php echo site_url('img/buttonSubmit.gif'); ?>">
			</dd>
		</dl>
	</fieldset>
</form>