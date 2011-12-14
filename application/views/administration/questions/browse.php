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
			<td>Date</td>
			<td>Author</td>
			<td>Question</td>			
			<td>Answers</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $question){ ?>
				<tr id="<?php echo $question->id ?>" style="background-color: #BBBBBB; color: #000000;">
					<td class="switch">
						<span class="turn_on<?php if($question->status == 'inactive') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($question->status == 'active') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo  date( "d.m.Y h:m", $question->posted_at ) ?></td>
					<td><a href="<?php echo '/user/'.$question->username.'/' ?>" title="View this member profile"><?php echo  h( $question->username ) ?></a> ( <a href="<?php echo '/administration/questions/index/member/'.$question->member_id.'/' ?>" title="View this member questions"><?php  echo $question->member_questions ?></a> )</td>
					<td><a href="#" class="more-info" title="Read full text"><?php echo empty( $question->title ) ? h(st( substr($question->text, 0, 50) )) . ( strlen( $question->text ) > 50 ? '...' : '' ) : h(st( $question->title )) ?></a>
							<div class="popup-screen hidden formsEditScreen_reviews_">
								<form method="post" action="" class="mainForm jform">
								<fieldset style="margin: 0 0 0 0;">
								<img src="<?php echo '/img/buttonClose.png' ?>" alt="Close" title="Close" border="0" class="xcancel" onclick="$(this).parents('div.popup-screen:first').addClass('hidden');" />
									<dl>
										<dd>
											<p>
												<b>Author:</b> <a href="<?php echo  '/administration/users/' . $question->member_id . '/' ?>" title="View this member profile"><?php echo h(st( $question->username )) ?></a><br />
												<b>Date:</b> <?php echo date("d.m.Y h:m", $question->posted_at) ?><br/>
												<br/>
												<?php echo  nl2br( h(st( $question->text )) ) ?>
											</p>
										</dd>
									</dl>
								</fieldset>
								</form>
							</div>
					</td> 
					<td class="icons" style="width: 60px;"><a class="blank" href="<?php echo '/administration/answers/index/question/'.$question->id ?>"><?php echo $question->question_answers ?></a></td>					
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/questions/edit/'.$question->id); ?>" class="edit" title="Edit">Edit</a>
						<a class="delete" name="Delete" title="Delete">Delete</a>
					</td>
				</tr>				
		<?php } ?>
	</tbody>
<?php } ?>

</table>
<script type="text/javascript">
			$(document).ready(function() {
				$('.more-info').click(function() {
					var arrPageSizes = ___getPageSize();
					var arrPageScroll = ___getPageScroll();

					$(this).siblings('div:first').css({ top: arrPageScroll[1] + 200 }).removeClass('hidden');

					return false;
				});
			})
</script>