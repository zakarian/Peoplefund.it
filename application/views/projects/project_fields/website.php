<?php	
	if(isset($project->websites) && $project->websites)
		$addValue = explode("|", $project->websites);
	else if(isset($post['websites']) && $post['websites'])
		$addValue = $post['websites'];
	else
		$addValue = '';		
?>
<fieldset>
	<label for="insert_website">WEBSITES</label>
	<div class="clear10"></div>
	<div id="websites">
		<?php if($addValue) {
			foreach($addValue as $k => $site){ ?>
			<div id="<?php echo (time() + $k) ?>">
				<input type="text" class="query websites focus-style" style="width: 300px; float: left;" readonly="readonly" name="websites[]" value="<?php echo h(st($site)) ?>">
				&nbsp;<a href="javascript:;" style="text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;" title='Remove' onClick="removeWebsite('<?php echo (time() + $k);?>');">&nbsp;<img src="/img/site/delete_16.png" alt="" /></a>
				<div class="clear"></div>
			</div>
			<div class="clear10"></div>	
		<?php } } ?>
	</div>	
	
	<input autocomplete="off" class="rounded" id="add_site" type="text" name="websites_temp" value="http://" />
	<input class="verify" name="button" type="button" value="ADD" onClick="add_website();" />
	<div class="clear10"></div>
</fieldset>	
<script>
	function add_website(){
		var site = $("#add_site").val();
		if(site == "http://") return;
		
		
		if (site.substr(0, 7) != 'http://' && site.substr(0, 8) != 'https://'){
			site = 'http://' + site;
		}
		
		var html;
		html = "<div id='"+Number(new Date())+"'>";
		html += "<input type='text' class='query focus-style websites' style='width: 300px; float: left;' name='websites[]' readonly='readonly' value='"+site+"'>";
		html += "&nbsp;<a href='javascript:;' style='text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;' title='Remove' onClick=\"removeWebsite('"+Number(new Date())+"');\">&nbsp;<img src='/img/site/delete_16.png'></a><div class='clear10'></div>	";
		html += "</div>";
		
		$("#websites").append(html);
		$("#add_site").val("http://");
	}
	
	function removeWebsite(site){
		$("#"+site).remove();
	}
</script>