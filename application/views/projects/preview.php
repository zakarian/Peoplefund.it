<div class="site-width">
	<div class="generic-form overflow left">
		<div class="steps">
			• <b>step 1</b> checklist &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>Step 2</b> project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			• <b>step 3</b> account &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span>• <b>step 4</b> submit</span>
		</div>
		<div class="inner-form left">
			<form class="global_form_2" method="post" action="">
				<h2>Please review your project, and click "submit" once it is complete</h2>
				
				<?php if(!empty($errors)){ 
						foreach($errors AS $error){
						?>
							<div class="global-error">- <?php echo $error;?></div>
						<?php 					}
					echo '<div class="clear20"></div>';
				} ?>
				
				<fieldset>
					<label for="insert_title">The name of your project</label>
					<a href="javascript:;" onClick="editField('title', 'show');" title="Edit title" class="edit_link">EDIT</a>
					
					<div class="clear10"></div>

					<div class="ex_text" id="title_preview">
						<?php echo $project->title?>
					</div>
					<div id="title_edit" style="display: none;">
						<input type="text" name="title" id="title" value="<?php echo $project->title?>" autocomplete="off" class="query focus-style" onBlur="fillSlug('title', 'slug')">
						<input class="button gray left" name="button" type="button" onClick="editField('title', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>

					<div class="clear10"></div>
					<?php /*<div class="ex_text"><?php echo $_POST['outcome']?></div>*/ ?>
					<div class="clear10"></div>
				</fieldset>	
				<fieldset>
					<label for="insert_location">Location</label>
					<a href="javascript:;" onClick="editField('postcode', 'show');" title="Edit title" class="edit_link">EDIT</a>

					<div class="clear5"></div>
					<small style="color: #999999;" class="label" id="postcode_location">
						<?php echo $project->location_preview?>, <?php echo $project->county_name?>
					</small>
					
					<div class="clear10"></div>
					
					<div class="ex_text" id="postcode_preview"><small><?php echo $project->postcode?></small></div>
					<span id="postcode_edit" style="display: none;">
						<input type="text" name="postcode" id="postcode" value="<?php echo $project->postcode?>" autocomplete="off" class="rounded">
						<input class="verify" name="button" type="button" value="Verify" onClick="checkPostcode();" />
						
						<?php if(!empty($project->postcode)){ ?>
							<script>
								$(function() {
									checkPostcode();
								});
							</script>
						<?php } ?>
						<br clear="all">
						<div id="postcode_message" style="padding-top: 5px;"></div>
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('postcode', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</span>
						
					
					
					<div class="clear10"></div>
				</fieldset>	
				<fieldset>
					<label for="insert_slug">Vanity URL</label>
					<a href="javascript:;" onClick="editField('slug', 'show');" title="Edit title" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<div class="ex_text" id="slug_preview"><small>https://www.peoplefund.it/<?php echo $project->slug?></small></div>
					
					<span id="slug_edit" style="display: none;">
						<input type="text" name="slug" id="slug" value="<?php echo $project->slug?>" class="query focus-style">
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('slug', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</span>
					
					<div class="clear"></div>
				</fieldset>	
				<fieldset>
					<label for="insert_website">WEBSITES</label>
					<?php /*<a href="javascript:;" onClick="editField('websites', 'show');" title="Edit title" class="edit_link">EDIT</a> */ ?>
					<div class="clear10"></div>
					<?/*<div class="ex_text" id="websites_preview">
						<?php 
							$websites = explode("|", $project->websites);
							foreach($websites AS $site){
							?>
								<a href="<?php echo $site?>" title="<?php echo $site?>"><?php echo $site?></a><br>
							<?php 							}
						?>
					</div>*/ ?>
					<div id="websites_edit">
						<div id="websites">
							<?php if(!empty($project->websites)){
								$project->websites = explode("|", $project->websites);
								foreach($project->websites AS $k => $site){
									?>
										<div id="<?php echo (time() + $k);?>" style="margin-top: 10px;">
											<input type='text' class="query focus-style" style="width: 300px; float: left;" name='websites[]' value="<?php echo $site?>">
											&nbsp;<a href='javascript:;' style='text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;' title='Remove' onClick="removeWebsite('<?php echo (time() + $k);?>');">&nbsp;<img src='/img/site/delete_16.png'></a><br clear='all'>
										</div>
									<?php 								}
							} ?>
						</div>
						<div class="clear10"></div>
						<input autocomplete="off" class="rounded" id="add_site" type="text" name="websites_temp" value="http://" />
						<input class="verify" name="button" type="button" value="ADD" onClick="add_website();" />
						<div class="clear10"></div>
						<?php /*<input class="button gray left" name="button" type="button" onClick="editField('websites', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div> */ ?>
					</div>
					
					<div class="clear10"></div>
				</fieldset>	
				<fieldset>
					<label for="insert_website">Category</label>
					<a href="javascript:;" onClick="editField('category', 'show');" title="Edit category" class="edit_link">EDIT</a>
					<div class="clear5"></div>
					<small style="color: #999999;" class="label">
						Please select which categories your project has an innovative solution for.
					</small>
					<div class="clear10"></div>
					
					
					<div class="ex_text" id="category_preview">
						<small>
							<?php foreach($project->categories AS $category){
								$selected_categories[$category->idcategory] = $category->title;
							} 
							echo implode(", ", $selected_categories);
							?>
						</small>
					</div>
					<div id="category_edit" style="display: none;">
						<?php
							if(!empty($categories)){
								foreach($categories AS $category){ ?>
										<div style="float: left; width: 180px; padding-top: 5px;">
											<input type="checkbox" class="categories_checkbox" name="categories[<?php echo $category->idcategory?>]" <?php if(!empty($selected_categories[$category->idcategory])){ echo "CHECKED"; }?> value="<?php echo $category->idcategory?>"> <?php echo $category->title; ?>
										</div>
										<?php 										if(!empty($category->subcategories)){
											foreach($category->subcategories AS $subcategory){
												?>
													<div style="float: left; width: 180px; padding-top: 5px;">
														<input type="checkbox" class="categories_checkbox" name="categories[<?php echo $subcategory->idcategory?>]" <?php if(!empty($selected_categories[$subcategory->idcategory])){ echo "CHECKED"; }?> value="<?php echo $subcategory->idcategory?>"> <?php echo $subcategory->title; ?>
													</div>
												<?php
											}
										}
								} 
							} else {
								?> 
									<option value="0">None</option>
								<?php
							}
						?>
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('category', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
					
					<div class="clear10"></div>
				</fieldset>	
 
				<?php include('../application/views/projects/project_fields/media.php') ?>

				<fieldset>

					<label for="insert_outcome">Aim</label>
					<a href="javascript:;" onClick="editField('outcome', 'show');" title="Edit outcome" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<small style="color: #999999;" class="label">
						Please explain what your project aims to do in no more than 100 words. To make it clear you could start 'We aim to…' or 'We will…'. Top tip: projects that have clear, tangible, distinctive aims are far, far more likely to get funded. Only the first 97 characters will show up in the search results – so make sure they grab people's interest!
					</small>
					<div class="clear10"></div>
					
					<div class="ex_text" id="outcome_preview">
						<small><?php echo $project->outcome?></small>
					</div>
					<div id="outcome_edit" style="display: none;">
						<textarea style="width: 711px; padding: 10px; height: 50px; border: 1px solid silver;" id="outcome" name="outcome" onKeyUp="limitTextArea('outcome', 'outcome_counter', 100);" onKeyDown="limitTextArea('outcome', 'outcome_counter', 100);"><?php echo $project->outcome?></textarea>
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('outcome', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
					<div class="clear10"></div>
				</fieldset>	
				<fieldset>
					<label for="insert_outcome">About your project</label>
					<a href="javascript:;" onClick="editField('about', 'show');" title="Edit about" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<small class="label">
						Explain your project - this text will be visible only on the project page
					</small>
					<div class="clear10"></div>
					<div class="ex_text normal-page" id="about_preview">
						<?php echo $project->about?>
					</div>
					<div id="about_edit" style="display: none;">
						<textarea class="aboutMceEditor" id="about" name="about" style="width: 100%; height: 220px;"><?php echo  $project->about ?></textarea>
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('about', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
					<div class="clear10"></div>
					<div class="clear10"></div>
				</fieldset>	

				<fieldset>
					<label for="">Rewards</label>
					
					<div class="clear10"></div>
					<small style="color: #999999;" class="label">
						Please list up to 10 rewards you are offering in return for people pledging money to your project. You can find more about offering rewards in the frequently asked questions.
					</small>
					<div class="clear10"></div>
					
					<div id="amount_options_container">
						<?php if(!empty($amounts)){ ?>
						
							<?php foreach($amounts AS $k => $amount){ ?>
								<div class="pledge focus-style" id="amount_<?php echo $amount->idamount?>" style="margin-top: 10px;">
									<span class="title">REWARD </span>
									<div class="clear10"></div>
									<div style="line-height: normal;" class="label">Pledge amount <small>(You can only enter numbers)</small></div><input class="pledge<?php if(!$amount->amount OR !is_numeric($amount->amount)) echo ' error' ?>" name="amounts[<?php echo $amount->idamount?>]" maxlength="6" value="<?php echo $amount->amount?>" /><div class="clear10"></div>
										<div style="line-height: normal;" class="label">Are there a limited number<br /> of this reward available?</div>
										<select name="amounts_limited[<?php echo $amount->idamount?>]" class="pledge focus-style">
											<option value="no" <?php if($amount->limited == "no"){ echo "SELECTED"; } ?>>No</option>
											<option value="yes" <?php if($amount->limited == "yes"){ echo "SELECTED"; } ?>>Yes</option>
										</select>
										<div class="n_reward_l"<?php if(@$amount->limited == "yes"){ echo ' style="display: block;"'; } ?>>
											<div style="width: 70px;" class="label">Number</div>
											<input class="pledge_small focus-style" name="amounts_numbers[<?php echo $amount->idamount?>]" value="<?php echo $amount->number?>" />
										</div>
									<div class="clear10"></div>
									<div class="label">Description of reward</div>
									<textarea class="pledge<?php if(!$amount->description) echo ' error' ?>" name="amounts_descriptions[<?php echo $amount->idamount?>]"><?php echo $amount->description?></textarea>
									<a href="javascript:;" onClick="removeAmount('<?php echo $amount->idamount?>');" style="margin-left: 210px; padding-top: 5px; float: left;">Remove reward</a>
									<div class="clear10"></div>
								</div>
							<?php } ?>

						<?php } else { ?>
							<div class="pledge focus-style" id="amount_0" style="margin-top: 10px;">
								<span class="title">REWARD </span>
								<div class="clear10"></div>
								<div style="line-height: normal;" class="label">Pledge amount <small>(You can only enter numbers)</small></div><input class="pledge focus-style" name="amounts[0]" value="" /><div class="clear10"></div>
									<div class="label">Limited</div>
									<select name="amounts_limited[0]" class="pledge focus-style">
										<option value="no">No</option>
										<option value="yes">Yes</option>
									</select>
									<div class="n_reward_l">
										<div style="width: 70px;" class="label">Number</div>
										<input class="pledge_small focus-style" name="amounts_numbers[0]" value="" />
									</div>
								<div class="clear10"></div>
								<div class="label">Description of reward</div>
								<textarea class="pledge focus-style" name="amounts_descriptions[0]"></textarea>
								<div class="clear10"></div>
							</div>
						<?php } ?>
					</div>
					
					<div class="clear10"></div>
					<input class="button left" name="button" type="button" value="Add another" onClick="addAmount();" /><div class="clear10"></div>
					<div class="clear10"></div>
					
					<div class="clear5"></div>
				</fieldset>	
				<fieldset>
					<label for="" style="width: auto;">How long do you want to have the project up for?</label>
					<a href="javascript:;" onClick="editField('period', 'show');" title="Edit period" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<small class="label">
						How long your project will be visible for pledgers<br />
					</small>
					<div class="clear10"></div>
					
					<div class="ex_text" id="period_preview">
						<small><?php echo ($project->period / 7) ?> Week<?php if($project->period / 7 != 1){?>s<?}?></small>
					</div>
					
					<span id="period_edit" style="display: none;">
						<span class="label">Weeks</span>
						<select id="period" class="small focus-style" name="period">
							<?php /*<option value="1"<?php if(($project->period / 7) == 1) echo ' selected="selected"'; ?>>1 Week</option>
							<option value="2"<?php if(($project->period / 7) == 2) echo ' selected="selected"'; ?>>2 Weeks</option>
							<option value="3"<?php if(($project->period / 7) == 3) echo ' selected="selected"'; ?>>3 Weeks</option>*/ ?>
							<option value="4"<?php if(($project->period / 7) == 4) echo ' selected="selected"'; ?>>4 Weeks</option>
							<option value="5"<?php if(($project->period / 7) == 5) echo ' selected="selected"'; ?>>5 Weeks</option>
							<option value="6"<?php if(($project->period / 7) == 6) echo ' selected="selected"'; ?>>6 Weeks</option>
							<option value="7"<?php if(($project->period / 7) == 7) echo ' selected="selected"'; ?>>7 Weeks</option>
							<option value="8"<?php if(($project->period / 7) == 8) echo ' selected="selected"'; ?>>8 Weeks</option>
							<option value="9"<?php if(($project->period / 7) == 9) echo ' selected="selected"'; ?>>9 Weeks</option>
							<option value="10"<?php if(($project->period / 7) == 10) echo ' selected="selected"'; ?>>10 Weeks</option>
						</select>
						<div class="clear10"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('period', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</span>
					
					<div class="clear"></div>
				</fieldset>	
				
				<fieldset>	
					<label for="insert_amount" style="width: auto;">FUNDING TARGET IN &pound; (YOU CAN ONLY ENTER NUMBERS. THE MAXIMUM FUNDING TARGET YOU CAN ENTER IS &pound;50,000 AND THE MINIMUM IS &pound;1000, HOWEVER YOU CAN CHOOSE TO ENABLE PEOPLE TO KEEP PLEDGING BEYOND THIS TARGET BELOW.)</label>
					<a href="javascript:;" onClick="editField('amount', 'show');" title="Edit amount" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<div class="ex_text" id="amount_preview">
						<small><?php echo $project->amount?></small>
					</div>
					<div id="amount_edit" style="display: none;">
						<input id="amount" class="query focus-style" type="text" name="amount" value="<?php echo h(st($project->amount)) ?>" onKeyUp="limitTextArea('amount', 'amount_counter', 6);" onKeyDown="limitTextArea('amount', 'amount_counter', 6);">
						<input class="button gray left" name="button" type="button" onClick="editField('amount', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
					
				</fieldset>	
				
				<?php /* <fieldset>	
					<label for="paypal_email" style="width: auto;">PayPal account email</label>
					<a href="javascript:;" onClick="editField('paypal_email', 'show');" title="Edit paypal email" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<div class="ex_text" id="paypal_email_preview">
						<small><?php echo $project->paypal_email?></small>
					</div>
					<div id="paypal_email_edit" style="display: none;">
						<input id="paypal_email" class="query focus-style" type="text" name="paypal_email" value="<?php echo h(st($project->paypal_email)) ?>">
						<input class="button gray left" name="button" type="button" onClick="editField('paypal_email', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
					
				</fieldset>	*/ ?>
				<?php if(!$project->merchant_id) { ?>
				<fieldset>	
					<label for="merchant_id" style="width: auto;">
						<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
						<span id="gocardless-no">Associate your project with GoCardless account</span>
						<span id="gocardless-yes" class="hidden">You have successfully linked your GoCardless account</span>
					</label>
					<div id="gocardless-no-block">
						<div class="clear10"></div>
						<input id="select-gocardless-account" class="button left" name="gocardless" type="button" value="Select GoCardless account">
					</div>
					<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
					<input id="access_token" type="hidden" name="access_token" value="<?php echo (int) $project->access_token ?>">
				</fieldset>	
				<?php } else { ?>
				<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
				<fieldset>	
					<label for="merchant_id" style="width: auto;">
						<?php if(isset($errors['merchant_id']) && $errors['merchant_id']) { ?><b style="color: red;">*</b><?php } ?>
						You have successfully linked your GoCardless account
					</label>
					<input id="merchant_id" type="hidden" name="merchant_id" value="<?php echo (int) $project->merchant_id ?>">
				</fieldset>	
				<?php } ?>
				
				<fieldset>
					<label for="" style="width: auto;">WOULD YOU LIKE TO ENABLE FUNDING TO CONTINUE BEYOND 100% IF YOU REACH THE TARGET WITHIN YOUR FUNDING TIMESCALE? (N.B. IF YOU HAVE A LIMITED NUMBER OF REWARDS AVAILABLE PLEASE MAKE SURE YOU LIMIT THEM IN THE REWARDS AVAILABILITY SECTION)</label>&nbsp;
					
					
					<a href="javascript:;" onClick="editField('pledge_more', 'show');" title="Edit option" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<div class="ex_text" id="pledge_more_preview">
						<small><?php if($project->pledge_more == "0") { echo "No"; } else { echo "Yes"; } ?></small>
					</div>
					<div id="pledge_more_edit" style="display: none;">
						<input type="radio" name="pledge_more" id="pledge_more_yes" value="1"<?php if($project->pledge_more == 1 OR empty($project->hostname)){ ?> checked="checked"<?php } ?>> Yes
						<input type="radio" name="pledge_more" id="pledge_more_no" value="0"<?php if($project->pledge_more == 0){ ?> checked="checked"<?php } ?>> No
						<div class="clear5"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('pledge_more', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
				
				</fieldset>
				<fieldset>
					<label class="small focus-style" for="" style="width: auto;">Would you like to ask people if they would also like to support your project with their time and / or skills after they have pledged to fund your project?</label>&nbsp;
					
					<a href="javascript:;" onClick="editField('helpers', 'show');" title="Edit option" class="edit_link">EDIT</a>
					<div class="clear10"></div>
					<div class="ex_text" id="helpers_preview">
						<small><?php if($project->helpers == "0") { echo "No"; } else { echo "Yes"; } ?></small>
					</div>
					<div id="helpers_edit" style="display: none;">
						<input type="radio" name="helpers" id="helpers_yes" value="1"<?php if($project->helpers == 1 OR empty($project->helpers)){ ?> checked="checked"<?php } ?>> Yes
						<input type="radio" name="helpers" id="helpers_no" value="0"<?php if($project->helpers == 0){ ?> checked="checked"<?php } ?>> No
						<div class="clear5"></div>
						<input class="button gray left" name="button" type="button" onClick="editField('helpers', 'hide');" value="SAVE" style="margin-top: 10px;" /><div class="clear5"></div>
					</div>
				</fieldset>
				
				<?php include('../application/views/projects/project_fields/buttons.php') ?>
				<div class="clear"></div>
			</form>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>

<form id="add-gocardless-account" method="post" action="<?php echo $gocardless['oauth_authorize_url'] ?>" class="hidden">
	<div>
		<input type="hidden" name="client_id" value="<?php echo $gocardless['app_id'] ?>">
		<input type="hidden" name="redirect_uri" value="<?php echo $gocardless['redirect_uri'] ?>">
		<input type="hidden" name="response_type" value="<?php echo $gocardless['response_type'] ?>">
		<input type="hidden" name="scope" value="<?php echo $gocardless['scope'] ?>">
	</div>
</form>

<script>
	$(document).ready(function(){
		$('.global_form_2 div.pledge select.pledge').change(function(){
			if($(this).val() == 'yes') 
				$(this).parent().find('.n_reward_l').show();
			else
				$(this).parent().find('.n_reward_l').hide();
		});
		$('#select-gocardless-account').click( function() {
			$('#add-gocardless-account').get(0).setAttribute('target', 'gocardless');
			
			window.open('about:blank', 'gocardless', 'scrollbars=yes,menubar=no,height=600,width=1024,resizable=yes,toolbar=no,status=no');
			
			$('#add-gocardless-account').submit();

			return false;
		});
	});
	function addAmount(){
		var cnt = parseInt($("#amountsCount").val());
		if(cnt > 11) return false;
		cnt = cnt + 1;
		$("#amountsCount").val(cnt);
		
		var html = '';
		html += '<div class="pledge focus-style" id="amount_'+cnt+'" style="margin-top: 10px;">';
		html += '<span class="title">REWARD </span>';
		html += '<div class="clear10"></div>';
		html += '<div style="line-height: normal;" class="label">Pledge amount <small>(You can only enter numbers)</small></div><input maxlength="6" class="pledge focus-style" name="amounts['+cnt+']" value="" /><div class="clear10"></div>';
		html += '<div style="line-height: normal;" class="label">Are there a limited number<br /> of this reward available?</div>';
		html += '<select name="amounts_limited['+cnt+']" class="pledge focus-style">';
		html += '<option value="no">No</option>';
		html += '<option value="yes">Yes</option>';
		html += '</select>';
		html += '<div class="n_reward_l">';
		html += '<div style="width: 70px;" class="label">Number</div>';
		html += '<input class="pledge_small focus-style" name="amounts_numbers['+cnt+'] "value="" />';
		html += '</div>';
		html += '<div class="clear10"></div>';
		html += '<div class="label">Description of reward</div>';
		html += '<textarea class="pledge focus-style" name="amounts_descriptions['+cnt+']"></textarea>';
		html += '<a href="javascript:;" onClick="removeAmount('+cnt+');" style="margin-left: 210px; padding-top: 5px; float: left;">Remove reward</a>';
		html += '<div class="clear10"></div>';
		html += '</div>';
			
		$("#amount_options_container").append(html);
		
		$('.global_form_2 div.pledge select.pledge').change(function(){
			if($(this).val() == 'yes') 
				$(this).parent().find('.n_reward_l').show();
			else
				$(this).parent().find('.n_reward_l').hide();
		});
	}
	
	function removeAmount(id){
		$("#amount_"+id).remove();
	}
	function removeAddedAmount(id){
		$("#added_amount_"+id).hide();
		$("#added_delete_"+id).val("1");
	}

	// Check if postcode is valid
	function checkPostcode(){
		$.post('/user/get_location_by_postcode/', {postcode: $("#postcode").val()}, function(data) {
			if($.trim(data) != ","){
				$("#postcode_message").html("<font color='green'>The postcode is OK</font>");
				$("#postcode_location").html($.trim(data));
			} else {
				$("#postcode_message").html("<font color='red'>The postcode is not found</font>");
				$("#postcode_location").html("");
			}
		});
	}

	// Add website
	function add_website(){
		var site = $("#add_site").val();
		if(site == "http://") return;
		
		if (site.substr(0, 7) != 'http://' && site.substr(0, 8) != 'https://'){
			site = 'http://' + site;
		}		
		
		var html;
		html = "<div id='"+Number(new Date())+"' style='margin-top: 10px;'>";
		html += "<input type='text' class='query' style='width: 300px; float: left;' name='websites[]' value='"+site+"'>";
		html += "&nbsp;<a href='javascript:;' style='text-decoration: none; padding-top: 7px; padding-left: 5px; float: left;' title='Remove' onClick=\"removeWebsite('"+Number(new Date())+"');\">&nbsp;<img src='/img/site/delete_16.png'></a><br clear='all'>";
		html += "</div>";
		
		$("#websites").append(html);
		$("#add_site").val("http://");
	}
	
	// Remove website
	function removeWebsite(site){
		$("#"+site).remove();
	}
	
	// Edit field
	function editField(field, show){
		if(show == "show"){
			$("#"+field+"_preview").hide();
			$("#"+field+"_edit").show();
		} else {
		
			if(field == "slug"){
				$("#"+field+"_preview").html("<small>"+ "http://peoplefund.it/"+$("#"+field).val() +"</small>");
			} else if(field == "period"){
				var weeks = "";
				if($("#"+field).val() != 1){
					weeks = "s";
				}
				$("#"+field+"_preview").html("<small>"+ $("#"+field).val() +" Week"+weeks+"</small>");
			} else if(field == "pledge_more"){
				if($("#pledge_more_yes").is(":checked")){
					$("#"+field+"_preview").html("<small>Yes</small>");
				} else {
					$("#"+field+"_preview").html("<small>No</small>");
				}
			} else if(field == "helpers"){
				if($("#helpers_yes").is(":checked")){
					$("#"+field+"_preview").html("<small>Yes</small>");
				} else {
					$("#"+field+"_preview").html("<small>No</small>");
				}
			} else if(field == "category"){
				
				var checkBoxes = $(".categories_checkbox");
				var checked = new Array();

				$.each(checkBoxes, function() {
					if ($(this).attr('checked')){
						checked.push(this.value);
					}
				});
				
				$.post('/projects/get_categories_names_by_ids/', {ids: checked}, function(data) {
					if($.trim(data)){
						$("#"+field+"_preview").html("<small>"+data+"</small>");
					}
				});
	
			
			} else {
				$("#"+field+"_preview").html("<small>"+ $("#"+field).val() +"</small>");
			}
			
			$("#"+field+"_preview").show();
			$("#"+field+"_edit").hide();
		}
	}
	
	// Add some vars
	var local_ed;
	var spellTimer;
	
	$(document).ready(function() {
		
		$('textarea.aboutMceEditor').tinymce({
			script_url : '/js/site/tiny_mce/tiny_mce.js',
			theme : 'advanced',
			skin : 'o2k7',
			skin_variant : "silver",
			//max_chars : 500,
			spellchecker_rpc_url : "/js/tiny_mce/plugins/spellchecker/rpc.php",
			max_chars_indicator : "about_counter",
			plugins: "maxchars, safari, advimage, paste, spellchecker, table, widgets, contextmenu, paste, inlinepopups, emotions, ccSimpleUploader, embed, media",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align	: "left",
			theme_advanced_buttons1: "bold, italic, underline, forecolor, |, formatselect, |, justifyleft, justifycenter, justifyright, |, bullist, numlist, |, link, unlink, |, image, embed, |, removeformat, emotions",
			theme_advanced_buttons2: "",
			theme_advanced_buttons3: "",
			content_css: "/css/site/mce.css?<?php echo  rand() ?>",
			relative_urls : false,
			file_browser_callback: "ccSimpleUploader",
			//valid_elements: "iframe[*],small,object[*],embed[*],+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/-b[class|style],-em/-i[class|style],-u[class|style],#p[id|style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],#td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],-div[id|dir|class|align|style],-span[style|class|align],-h4[id|style|dir|class|align],dd[id|class|title|style|dir|lang],dl[id|class|title|style|dir|lang],dt[id|class|title|style|dir|lang]",
		
			paste_auto_cleanup_on_paste : true,
			paste_create_paragraphs : false, paste_create_linebreaks : false, paste_use_dialog : true, paste_convert_middot_lists : false, paste_unindented_list_class : "unindentedList", paste_convert_headers_to_strong : true, paste_remove_styles : false,
			
			setup: function(ed) {
				ed.onKeyUp.add(function(ed, e) {
					// console.debug('Key up event: ' + e.keyCode);

					clearTimeout(spellTimer);
					local_ed = ed;

					// Run 1.5 sec after the user stop writing...
					spellTimer = setTimeout("local_ed.execCommand('mceSpellCheckSilent')", 1500);
				});
				ed.onPaste.add( function(ed, e, o) {
					ed.execCommand('mcePasteText', true);
					return tinymce.dom.Event.cancel(e);
				});
			}
		});
	});
</script>