<?php if(!isset($ajax)) { ?><div class="site-width"><?php } ?>
	<form class="global_form report_project" method="post" action="">
		<h1>Report this project</h1>
		<?php
		if(!empty($errors)){
			foreach($errors AS $error){
				echo '<span class="global_error">'.$error.'</span>';
			}
			echo '<div class="clear10"></div>';
		}
		?>
		<script>
			$(function() {
				<?php if(!empty($errors)){ ?>
					parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?> + <?php echo (int)count($errors) ?>*25);
				<?php } ?>
				<?php if(!empty($success)){ ?>
					parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?> - 135);
				<?php } ?>
				var textarea_report = $('.query.hidden');
				var other = $('#other');
				var illegal_activity = $('#illegal_activity');
				var peoplefund_guidelines = $('#peoplefund_guidelines');
				var wrong_category = $('#wrong_category');
				other.focus(function(){	
					other.attr('checked', true);
					textarea_report.show();
					<?php if(!empty($errors)){ ?>
						parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?> + <?php echo (int)count($errors) ?>*25 + 115);
					<?php } else { ?>
						parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?> + 115);
					<?php } ?>
				});
				illegal_activity.focus(function(){	
					closeTextarea(illegal_activity);
				});
				peoplefund_guidelines.focus(function(){	
					closeTextarea(peoplefund_guidelines);
				});
				wrong_category.focus(function(){	
					closeTextarea(wrong_category);
				});
				
				function closeTextarea(element) {
					element.attr('checked', true);
					other.attr('checked', false);
					textarea_report.hide();
					<?php if(!empty($errors)){ ?>
						parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?> + <?php echo (int)count($errors) ?>*25);
					<?php } else { ?>
						parent.$.prettyPhoto.refresh(<?php echo REPORT_HEIGHT ?>);
					<?php } ?>
				}
				
				$('#cancel').focus(function(){
					parent.$.prettyPhoto.close();
				});
			});
		</script>
		<?php if(!isset($success)) { ?>
			<fieldset>
				You have selected to report this project. Please select why you are reporting this project:
				<div class="clear"></div>
			</fieldset>
			<fieldset>
				<label for="illegal_activity">The project is promoting an illegal activity</label>
				<input id="illegal_activity" type="radio" name="report_project" value="illegal_activity" class="" />
			</fieldset>
			<fieldset>
				<label for="peoplefund_guidelines">The project does not meet the peoplefund.it guidelines to change the world for the better.</label>
				<input id="peoplefund_guidelines" type="radio" name="report_project" value="peoplefund_guidelines" class="" />
			</fieldset>
			<fieldset>
				<label for="wrong_category">The project does not fit into one of the peoplefund.it categories: food, energy, environment, health, recreation or community.</label>
				<input id="wrong_category" type="radio" name="report_project" value="wrong_category" class="" />
			</fieldset>
			<fieldset>
				<label for="other">Other</label>
				<input id="other" type="radio" name="report_project" value="other" class="" />
				<div class="clear"></div>
				<textarea class="query hidden" cols="" rows="" name="text"></textarea>
			</fieldset>
			<input style="margin-left: 10px;" class="button" type="submit" name="submit" value="Report" />
			<input id="cancel" class="button" type="submit" name="submit" value="Cancel" />
		<?php } else { ?>
			<fieldset>
				<?php
				if(!empty($success)){
					foreach($success AS $item){
						echo $item;
					}
				}
				?>
				<div class="clear"></div>
			</fieldset>
			<input id="cancel" class="button" type="submit" name="submit" value="Cancel" />
		<?php } ?>
		<div class="clear"></div>
	</form>
	
<?php if(!isset($ajax)) { ?></div><?php } ?>