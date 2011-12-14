<div class="site-width">
	<div class="generic-form overflow left">
		<div class="steps">
			<span>• Edit project</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		<div class="inner-form left">
			<form class="global_form_2" method="post" action="">
				<h2>Edit your project</h2>
				
				<?php if(!empty($errors)){ 
						foreach($errors AS $error){
						?>
							<div class="global-error">- <?php echo $error;?></div>
						<?php 					}
					echo '<div class="clear20"></div>';
				} ?>
				
				<?php include('../application/views/projects/project_fields/title.php') ?>
				<?php include('../application/views/projects/project_fields/location.php') ?>
				<?php include('../application/views/projects/project_fields/slug.php') ?>
				<?php include('../application/views/projects/project_fields/website.php') ?>
				<?php include('../application/views/projects/project_fields/category.php') ?>
				<?php include('../application/views/projects/project_fields/media.php') ?>
				<?php include('../application/views/projects/project_fields/aim.php') ?>
				<?php include('../application/views/projects/project_fields/about.php') ?>
				<?php include('../application/views/projects/project_fields/pledges.php') ?>
				<?php include('../application/views/projects/project_fields/period.php') ?>
				<?php include('../application/views/projects/project_fields/amount.php') ?>
				<?php include('../application/views/projects/project_fields/gocardless.php') ?>
				<?php // include('../application/views/projects/project_fields/paypal_email.php') ?>
				<?php include('../application/views/projects/project_fields/pledge_more.php') ?>
				<?php include('../application/views/projects/project_fields/helpers.php') ?>
				<?php include('../application/views/projects/project_fields/time.php') ?>
				<?php include('../application/views/projects/project_fields/skills.php') ?>
				<?php include('../application/views/projects/project_fields/buttons.php') ?>
				<div class="clear"></div>
			</form>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php

	$gocardlessMerchantTitle 	= '';	
	$gocardlessFirstName 		= '';	
	$gocardlessLastName 		= '';	
	$gocardlessEmail 			= '';	
	
	if(isset($project->title) && $project->title) $gocardlessMerchantTitle = h(st($project->title));
	else if(isset($post['title']) && $post['title']) $gocardlessMerchantTitle = h(st($post['title']));
		
	$name					= explode(' ', !empty($_SESSION['user']['name']) ? $_SESSION['user']['name'] : $_SESSION['user']['username']);
	$gocardlessFirstName 	= array_shift($name);
	$gocardlessLastName 	= join(' ', $name);
	$gocardlessEmail		= $_SESSION['user']['email'];
	
?>
<form id="add-gocardless-account" method="post" action="<?php echo $gocardless['oauth_authorize_url'] ?>" class="hidden">
	<div>
		<input type="hidden" name="client_id" value="<?php echo $gocardless['app_id'] ?>">
		<input type="hidden" name="redirect_uri" value="<?php echo $gocardless['redirect_uri'] ?>">
		<input type="hidden" name="response_type" value="<?php echo $gocardless['response_type'] ?>">
		<input type="hidden" name="scope" value="<?php echo $gocardless['scope'] ?>">
		<input type="hidden" name="merchant[name]" value="<?php echo $gocardlessMerchantTitle ?>">
		<input type="hidden" name="merchant[user][email]" value="<?php echo $gocardlessEmail ?>">
		<input type="hidden" name="merchant[user][first_name]" value="<?php echo $gocardlessFirstName ?>">
		<input type="hidden" name="merchant[user][last_name]" value="<?php echo $gocardlessLastName ?>">
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
		html += '<div style="line-height: normal;" class="label">Pledge amount <small>(You can only enter numbers)</small></div><input maxlength="6" class="pledge focus-style" name="amounts_add['+cnt+']" value="" /><div class="clear10"></div>';
		html += '<div style="line-height: normal;" class="label">Are there a limited number<br /> of this reward available?</div>';
		html += '<select name="amounts_limited_add['+cnt+']" class="pledge focus-style">';
		html += '<option value="no">No</option>';
		html += '<option value="yes">Yes</option>';
		html += '</select>';
		html += '<div class="n_reward_l">';
		html += '<div style="width: 70px;" class="label">Number</div>';
		html += '<input class="pledge_small focus-style" name="amounts_numbers_add['+cnt+'] "value="" />';
		html += '</div>';
		html += '<div class="clear10"></div>';
		html += '<div class="label">Description of reward</div>';
		html += '<textarea class="pledge focus-style" name="amounts_descriptions_add['+cnt+']"></textarea>';
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
	
	function removeAmount(idelement, idamount){
		$("#amount_"+idelement).remove();
		if(idamount)
			$(".global_form_2").append("<input type='hidden' name='remove_amount[]' value='"+idamount+"'>");
	}
	function removeAddedAmount(id){
		$("#added_amount_"+id).hide();
		$("#added_delete_"+id).val("1");
	}
</script>