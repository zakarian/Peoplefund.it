<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>

<div class="mainForm"><fieldset>
	<dl>

		<form id="search-form" method="post" action="/administration/texts/">
			<dd>
				<label for="url" class="long">URL</label>
				<select id="url" name="url" class="long" style="margin-bottom: 4px;">
					<option value="">- All -</option>
					<?php if(!empty($urls)){
							foreach($urls AS $url) {?>
								<option value="<?php echo base64_encode(h(st($url->url))) ?>"><?php echo h(st($url->url)) ?></option>
					<?php } } ?>
				</select>
				<script>
					$(function() {
						$('#url').change(function(){
							window.location = '/administration/texts/index/url-'+$(this).val();
						});
					});
				</script>
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
			<td>Key</td>
			<td>Value</td>
			<td>URL</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $key => $value){ ?>
				<tr>
					<td><?php echo h(st($value->key)) ?></td>
					<td><?php echo h(st($value->text)) ?></td>
					<td>
						<a href="/administration/texts/index/url-<?php echo base64_encode(h(st($value->url))) ?>/" title="<?php echo h(st($value->url)) ?>">
							<?php echo h(st($value->url)) ?>
						</a>
					</td>
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/texts/edit/index/'.$value->id); ?>" class="edit" title="Edit">Edit</a>
					</td>
				</tr>
		<?php } ?>
	</tbody>
<?php } ?>

	
</table>