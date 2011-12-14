<!-- Global JS variable for handling the AJAX URLs -->
<script type="text/javascript">var fromPage = '<?php echo $current_module; ?>';</script>



<link rel="stylesheet" href="/css/admin/uploadify.css" type="text/css" />
<script type="text/javascript" src="/js/admin/swfobject.js"></script>
<script type="text/javascript" src="/js/admin/jquery.uploadify.js"></script>




<?php if (!empty($message)){ ?>
	<div class="mainForm session-messages">
		<fieldset style="border: 1px dashed #68bc5b;">
			<center><?php echo $message?></center>
		</fieldset>
	</div>
<?php } ?>

<!-- Create folder popup -->
<div class="mainForm popup-screen" id="createFolderPopup" style="display: none;">
	<fieldset style="border: 5px solid #5a758e; margin: 0px;">
		<img src="/img/buttonClose.png" alt="Close" title="Close" border="0" class="xcancel" onclick="$('#createFolderPopup').fadeOut();" style="right: -15px; top: -10px;"/>
		<form class="jform" action="/administration/assets/create_folder/" method="post" id="newFolderForm">
			<br />
			<dl>
				<dd>
					<label for="name" class="long" style="width: 100px;">Name</label>
					<input type="text" class="long" id="name" name="name" value="" />
					<input type="hidden" id="createFolderCurrentPath" name="path" value="" />
				</dd>
				<dd class="submitDD">
					<img class="close" src="/img/buttons/button-close.gif" alt="Close" title="Close" border="0" style="margin-left: 200px;" onclick="$('#createFolderPopup').fadeOut();"/>
					<input type="image" class="save" src="/img/buttons/button-save.gif" name="Save" title="Save" />
				</dd>
			</dl>
		</form>
	</fieldset>
</div>

<!-- Add file popup -->
<div class="mainForm popup-screen" id="addFilePopup" style="display: none;">
	<fieldset style="border: 5px solid #5a758e; margin: 0px;">
		<img src="/img/buttonClose.png" alt="Close" title="Close" border="0" class="xcancel" onclick="$('#addFilePopup').fadeOut();" style="right: -15px; top: -10px;"/>
		<form enctype="multipart/form-data" class="jform" action="/administration/assets/add_file/" method="post" id="add_fileForm">
			<br />
			<dl>
				<dd>
					<label for="file" class="long" style="width: 30%;">File</label>
					<input type="file" class="long" id="file" name="file" value="" />
					<input type="hidden" id="addFileCurrentPath" name="path" value="" />
				</dd>
				<dd class="submitDD">
					<img class="close" src="/img/buttons/button-close.gif" alt="Close" title="Close" border="0" style="margin-left: 200px;" onclick="$('#addFilePopup').fadeOut();"/>
					<input type="image" class="save" src="/img/buttons/button-save.gif" name="Save" title="Save" />
				</dd>
			</dl>
		</form>
	</fieldset>
</div>

<!-- Add multiple files popup -->
<div class="mainForm popup-screen" id="showMultipleFiles" style="display: none;">
	<fieldset style="border: 5px solid #5a758e; margin: 0px;">
		<img src="/img/buttonClose.png" alt="Close" title="Close" border="0" class="xcancel" onclick="$('#showMultipleFiles').fadeOut();" style="right: -15px; top: -10px;"/>
		<form enctype="multipart/form-data" class="jform" action="/administration/assets/add_file/" method="post" id="addMultipleFilesForm">
			<br />
			<dl>
				<table width="100%">
					<tr>
						<td width="40%" align="right">
							<label for="file" class="long" style="width: 30%; float: none; padding-right: 30px;">Select Files</label>
						</td>
						<td align="left" id="multiple_upload_holder">
							<input id="file_upload" name="file_upload" type="file" />
						</td>
					</tr>
				</table>
				
				
				<dd class="submitDD">
					<img class="close" src="/img/buttons/button-close.gif" alt="Close" title="Close" border="0" style="margin-left: 240px;" onclick="$('#showMultipleFiles').fadeOut();"/>
				</dd>
			</dl>
		</form>
	</fieldset>
</div>

<div class="mainForm" id="location">
	<fieldset>
		<div class="h1">Location: <span id="current_path">/</span></div>
	</fieldset>
</div>

<table id="listing" width="670" cellpadding="0" cellspacing="0" class="resultsTable">
	<thead>
		<tr>
			<td>File</td>
			<td>Size</td>
			<td>Actions</td>
		</tr>
	</thead>
	<tbody id="files"></tbody>
</table>

<script>
	// Get files for assets
	function getFiles(file){

		// Remove old files
		$("#files").html("");
		
		// Encode file name
		if(file){
			file = Base64.encode(file);
		} else {
			var file = "";
		}

		$.getJSON('/administration/assets/browse/' + file + '/', function(data) {
			

			if(!data.files) return;
		
			$.each(data.files, function(key, val) {
			
				var trclass = (key % 2 == 0) ? "even" : "zebra";

				// If browsing dir
				if(val.type == "dir"){
					var tr = '<tr class="dir '+trclass+'">';
							tr += '<td class="name">';
							tr += '   <a class="dragbl dir" href="javascript:;" onClick="getFiles(\'' + val.path +'\');" style="-moz-user-select: none;">'+val.name+'</a>';
							tr += '</td>';
							tr += '<td>';
							tr += '   <span class="tdpadding">-</span>';
							tr += '</td>';
							tr += '<td class="icons">';
							
							if(val.name == ".."){
								tr += "-";
							} else {
								tr += '	  <a class="rmdir" href="javascript:;" onClick="removeFile(\''+Base64.encode(val.path)+'\');" title="Delete">Delete</a>';
							}
							
							tr += '</td>';
						tr += '</tr>';
					$("#files").append(tr);
					
				// If browsing files
				} else {
					
					var tr = '<tr class="'+val.class+' '+trclass+'">';
							tr += '<td class="name">';
							tr += '   <a class="dragbl" href="javascript:;" style="-moz-user-select: none; text-decoration: none;">'+val.name+'</a>';
							tr += '</td>';
							tr += '<td>';
							tr += '   <span class="tdpadding">'+val.size+' kb</span>';
							tr += '</td>';
							tr += '<td class="icons">';
							(val.class == 'image' ? tr += '	  <a class="lightbox" href="'+(val.webpath)+'" title="Preview">Preview</a>' : '');
							tr += '	  <a class="rmdir" href="javascript:;" onClick="removeFile(\''+Base64.encode(val.path)+'\');" title="Delete">Delete</a>';
							tr += '</td>';
						tr += '</tr>';
					$("#files").append(tr);
					
					$('.lightbox').lightBox();
				}
			});
			
			$("#current_path").html(data.current);

		});
	}
	
	getFiles();
	
	// Remove file
	function removeFile(path){
		$.post('/administration/assets/delete/'+path, function(data) {
			getFiles(data);
		});
	}
	
	// Create folder popup
	function showCreateFolder(){
		$("#createFolderPopup").fadeIn();
	}
	
	// Add file popup
	function showAddFile(){
		$("#addFilePopup").fadeIn();
	}
	
	// Multiple files add popup
	function showMultipleFiles(){
		$("#showMultipleFiles").fadeIn(); 
		$("#file_upload").remove(); 
		$("#multiple_upload_holder").append('<input id="file_upload" name="file_upload" type="file" />');
		
		var uploadifyConfig = {
			'uploader': '/swf/uploadify.swf',
			'cancelImg': '/img/uploadify-cancel.png',
			'script': '/administration/assets/add_file/',
			'fileDataName': 'file',
			'multi': true,
			'auto': true,
			'scriptData': { path: $("#current_path").html()},
			'onAllComplete': function(data){ 
				getFiles( $("#current_path").html() );
				$("#showMultipleFiles").fadeOut(); 
				
			}
		}
		$("#file_upload").uploadify(uploadifyConfig);
	}
	
	function unUploadify(element){
		if(!element){var element="*";};
		$(element).next(element+"Uploader").remove();
		$(element).css("display","inline");
	};
	
	// Submit new folder form
	$('#newFolderForm').submit(function() {
		$("#createFolderCurrentPath").val($("#current_path").html());
		
		return jform.submit(this, {
			onComplete: function(data) {
				getFiles(data);
				$("#name").val("");
				$('#createFolderPopup').fadeOut();
			}
		});
		
	});
	
	// Submit add file popup
	$('#addFileForm').submit(function() {
		$("#addFileCurrentPath").val($("#current_path").html());
		
		return jform.submit(this, {
			onComplete: function(data) {
				getFiles(data);
				$("#file").val("");
				$('#addFilePopup').fadeOut();
			}
		});
		
	});
</script>