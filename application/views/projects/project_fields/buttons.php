<?php	
	if(isset($amounts) && $amounts)
		$amounts = $amounts;
	else if(isset($_POST['amounts']) && $_POST['amounts'])
		$amounts = $_POST['amounts'];
	else
		$amounts = '';	
			
	if(isset($project->status) && $project->status)
		$status = h(st($project->status));
	else
		$status = 'temp';	
?>
<input type="hidden" id="amountsCount" value="<?php echo ($amounts) ? count($amounts) : 0  ?>">
<input class="button left" name="submit" type="submit" value="Submit" style="">
<?php if($status == 'temp'){ ?>
	<input class="button left edit-later" name="save" type="submit" value="Save &amp; edit later">
<?php } ?>