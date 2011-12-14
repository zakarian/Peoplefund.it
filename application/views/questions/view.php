<div class="site-width question-view">
	<section class="question">
		<article>
		<h3><?php echo h(st( $question->text )) ?></h3>
		
		<div class="question-info">
		<?php if( $question->member_id > 0 ) :?>
			<?php if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/' . $question->member_id . '_40x40.jpg')) : ?>
				<img src="<?php echo '/uploads/users/' . $question->member_id . '_40x40.jpg' ?>" alt="<?php echo h(st( $question->username )) ?>">
			<?php else : ?>
				<img src="<?php echo '/uploads/thumb-not-available.png' ?>" alt="<?php echo h(st( $question->username )) ?>">
			<?php endif; ?>
			<span>STARTED BY: <a href="/user/<?php echo h(st( $question->username)) ?>/" title="View this question author"><?php echo h(st( $question->username)) ?></a></span>
		<?php endif; ?> 
		<time class="published" datetime="<?php echo  date( 'Y-m-d', $question->posted_at ) ?>">| Added: <?php echo date( 'd/m/Y', $question->posted_at ); ?></time>
		<span> | Category: <a href="/questions/search/category:<?php echo h( $question->category_slug ) ?>/" title="<?php echo h( $question->category_name ) ?>"><?php echo h( $question->category_name ) ?></a></span>
		</div>

		</article>
	</section>
	<h3><span class="tip"><?php echo $question->question_answers ?></span> <?php echo $question->answers == 1 ? 'Reply' : 'Replies' ?></h3>
	<form class="global_form" method="post" action="/questions/preview/<?php echo $question->id ?>/">
			<input type="hidden" name="action" value="newanswer" />
			<input type="hidden" name="question_id" value="<?php echo $question->id ?>" />
			<textarea id="comment-field" name="text" rows="10" cols="50">Write answer here...</textarea>
			<br>
			<input type="submit" class="button" name="submit" value="Add reply" />
	</form>
	
	<section class="answers">
	<?php if( !empty( $answers ) ) : ?>
		<?php foreach($answers as $answer) : ?>
			<article>
				<div class="answer-info">
					<?php if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/' . $answer->member_id . '_40x40.jpg')) : ?>
						<img src="<?php echo '/uploads/users/' . $answer->member_id . '_40x40.jpg' ?>" alt="<?php echo h(st( $answer->username )) ?>">
					<?php else : ?>
									<img src="<?php echo '/uploads/thumb-not-available.png' ?>" alt="<?php echo h(st( $question->username )) ?>">
					<?php endif; ?>
					<div class="helpful-box <?php if( $answer->helpful_total == 0 ) echo ' hidden'; ?>">
					<strong><?php echo $answer->helpful_cnt == $answer->helpful_total ? '<span id="ht' . $answer->id . '">' . $answer->helpful_total . '</span>' : '<span id="hc' . $answer->id . '">' . $answer->helpful_cnt . '</span> of <span id="ht' . $answer->id . '">' . $answer->helpful_total . '</span>' ?> <?php echo $answer->helpful_total == 1 ? 'Person' : 'People' ?> found this answer helpful:</strong>
					</div>
					<span>				
					Answered by <a href="/users/<?php echo h(st( $answer->username )) ?>/" title="<?php echo h(st( $answer->username )) ?>"><?php echo h(st( $answer->username )) ?></a> <time>On <?php echo date( 'Y-m-d', $answer->posted_at ) ?></time>
					</span>
				</div>
				
									<div class="clear"></div>

				<?php if( $answer->member_status == 'published' ) : 
					echo '<p id="post-' . $answer->id . '">' . nl2br(auto_link(h(st( $answer->text )))) . '</p>';
				elseif ( $answer->member_status == 'deleted' ) : ?>
					<p>This answer has been removed by moderator.</p>
				<?php endif; ?>
				<?php	if( $answer->pending_moderation == 'yes' ) {?>
					<p>This answer is still pending moderation.</p>
				<?php } ?>				
				
				<?php if( !empty($_SESSION['user']) && $_SESSION['user']['iduser'] != $answer->member_id && $answer->member_status == 'published' ): ?>
				<p>Was this answer helpful? <a class="helpful" rel="h<?php echo $answer->id ?>" href="#" title="Yes">Yes</a> / <a class="nothelpful" rel="h<?php echo $answer->id ?>" href="#" title="No">No</a></p>
				<script type="text/javascript">
					$( '.helpful, .nothelpful' ).unbind( 'click' );
					$( '.helpful, .nothelpful' ).click( function() {
						var thisHref = $( this );
						var id = $( this ).attr( 'rel' ).replace( 'h', '' );
						var helpful = $( this ).hasClass( 'nothelpful' ) ? 'no' : 'yes'

					
						$.post( '/questions/helpful/', { answer_id: id, helpful: helpful }, function( ret ) {
							if( ret == 'mass-increment' ) {
								if( $( '#hc' + id ).size() > 0 ) $( '#hc' + id ).text( parseFloat( $( '#hc' + id ).text() ) + 1 );
								if( $( '#ht' + id ).size() > 0 ) $( '#ht' + id ).text( parseFloat( $( '#ht' + id ).text() ) + 1 );
							}

							thisHref.parents( 'p:first' ).hide();
							thisHref.parents( 'article:first' ).children( 'div.helpful-box' ).removeClass( 'hidden' );
						});

						return false;
					});
				</script>
				<?php endif; ?>
				
				<?php 
					$userid = isset($_SESSION['user']['iduser']) ? $_SESSION['user']['iduser'] : '';
					$can_edit = $answer->member_id == $userid ? TRUE : FALSE;
					$can_delete = $answer->member_id == $userid ? TRUE : FALSE;
				?>
				<?php if( $can_edit OR $can_delete ): ?><p><?php endif; ?>
				<?php if( $can_edit ): ?><a id="edit_comment_<?php echo  $answer->id ?>" href="" class="edit edit_post">Edit this answer</a><?php endif; ?>
				<?php if( $can_edit && $can_delete ): ?>|<?php endif; ?>
				<?php if( $can_edit ): ?><a href="" class="delete_comment" rel="<?php echo  $answer->question_id ?>_<?php echo  $answer->id ?>">Delete this answer</a><?php endif; ?>
				<?php if( $can_edit OR $can_delete ): ?></p><?php endif; ?>
			</article>
		<?php endforeach; ?>
		<script type="text/javascript">
			$(document).ready( function() {
				$('.delete_comment').click( function() {	
					var item_id = $( this ).attr( 'rel' ).replace( '<?php echo $answer->question_id ?>_', '' );
					
					if (confirm('Are you sure want to delete this answer?')) {
						$.post( '/questions/delete/', {  answer_id: item_id, question_id: <?php echo $answer->question_id ?> }, function( ret ) {
							if( ret == 'success' )
								document.location = document.location.toString().replace( /\#./, '' );
						});
						
					}
					return false;
				});

				$('.edit_post').click( function() {
					
					if( $( '#reply-post' ).size() > 0 ) return false;

					var item_id = $( this ).attr( 'id' ).replace( 'edit_comment_', '' );
					var position = $( '#post-' + item_id );
					var textarea_to_html = ''+

					'<form id="reply-post" class="edit-comment" action="/questions/preview/<?php echo $answer->question_id ?>/" method="post" style="width: 570px;">'+
					'	<fieldset>'+
					'		<textarea id="atext" name="text" cols="60" rows="4" style="border: 1px solid #777;"></textarea>'+
					'		<input type="hidden" name="action" value="editanswer">'+

					'		<input type="hidden" id="answer_id" name="answer_id" value="">'+
					'		<input type="hidden" name="question_id" value="<?php echo $answer->question_id ?>">'+

					'		<input type="submit" name="submit" class="remove button blue small right" value="Edit answer" title="Edit answer" style="" />'+
					'		<a href="#" class="cancel button blue small right" title="Close" style="margin-right: 8px;">Close</a>'+
					'	</fieldset>'+
					'</form>';

					position.hide();
					position.after( textarea_to_html );
					$('#atext').val( position.html( ).replace( /<br\s*\/?>/mg, "" ) );
					$('#answer_id').val( item_id );

					$('#reply-post a.cancel').click( function() {
						position.show();
						$('#reply-post').remove();

						return false;
					});

					$('#reply-post').submit( function() {
						if( $('#atext').val() == '') {
							jAlert( 'Please type your answer.' );

							return false;
						}
					});

					return false;
				});
			});
		</script>
	<?php else : ?>
		There are no answers yet.
	<?php endif; ?>
	</section>
</div>
<script type="text/javascript">
	$(document).ready( function() {
		$( '#comment-field' ).focus( function() {
			if( $( this ).val( ).trim() == 'Write answer here...' )
				$( this ).val( '' );
			<?php if(empty($_SESSION['user'])) : ?> $( '#userbar div ul li:last a' ).trigger( 'click' );<?php endif; ?>
		});
});
</script>