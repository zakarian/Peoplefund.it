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
			<td>Question</td>
			<td>Accordant</td>			
			<td>Answer</td>
			<td>Helpful Answer</td>
			<td>Actions</td>
		</tr>
	</thead>
	
<?php if(!empty($items)){ ?>
	<tbody>
		<?php foreach ($items as $answer){ ?>
				<tr id="<?php echo $answer->id ?>" style="background-color: #BBBBBB; color: #000000;">
					<td class="switch">
						<span class="turn_on<?php if($answer->status == 'inactive') echo ' hidden'?>"><a class="turn_on" name="turn_on" title="Turn Off"></a></span>
						<span class="turn_off<?php if($answer->status == 'active') echo ' hidden'?>"><a class="turn_off" name="turn_off" title="Turn On"></a></span>
					</td>
					<td><?php echo  date( "d.m.Y h:m", $answer->posted_at ) ?></td>
					<td> ( <a href="/administration/answers/index/question/<?php echo $answer->question_id ?>" title="See all the answers for this question"><?php echo $answer->total_answers ?></a> ) </td>
					<td><a href="<?php echo '/user/'.$answer->username.'/' ?>" title="View this member profile"><?php echo  h( $answer->username ) ?></a> ( <a href="<?php echo '/administration/answers/index/member/'.$answer->member_id.'/' ?>" title="View this member questions"><?php  echo $answer->member_answers ?></a> )</td>
					<td><a href="#" class="more-info" title="Read full text"><?php echo empty( $answer->title ) ? h(st( substr($answer->text, 0, 50) )) . ( strlen( $answer->text ) > 50 ? '...' : '' ) : h(st( $answer->title )) ?></a>
							<div class="popup-screen hidden formsEditScreen_reviews_">
								<form method="post" action="" class="mainForm jform">
								<fieldset style="margin: 0 0 0 0;">
								<img src="<?php echo '/img/buttonClose.png' ?>" alt="Close" title="Close" border="0" class="xcancel" onclick="$(this).parents('div.popup-screen:first').addClass('hidden');" />
									<dl>
										<dd>
											<p>
												<b>Author:</b> <a href="<?php echo  '/administration/users/' . $answer->member_id . '/' ?>" title="View this member profile"><?php echo h(st( $answer->username )) ?></a><br />
												<b>Date:</b> <?php echo date("d.m.Y h:m", $answer->posted_at) ?><br/>
												<br/>
												<?php echo  nl2br( h(st( $answer->text )) ) ?>
											</p>
										</dd>
									</dl>
								</fieldset>
								</form>
							</div>
					</td> 
					<td class="icons" style="width: 60px;"><a class="blank" href="#"><?php echo $answer->helpful_cnt ?></a></td>					
					<td class="icons" style="width: 90px;">
						<a href="<?php echo site_url('administration/answers/edit/'.$answer->id); ?>" class="edit" title="Edit">Edit</a>
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