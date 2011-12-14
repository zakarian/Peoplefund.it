			<div class="site-width questions">
			<h1>Questions and answers</h1>
			<p>Add new question <a title="Add question" <?php echo isset($_SESSION['user']['iduser']) ? 'href="/questions/add"' : 'href="http://localhost:8080/user/login/" rel="prettyPhoto"';?>>here</a>.</p>
			
			<div class="search">
			
			<div class="select-questions-category">
				<label for="select-questions-category">Select category</label>
				<select id="select-questions-category" name="select-questions-category">
					<option selected="selected" value="">ALL</option>
					<?php foreach( $categories as $item ): ?>
					<option value="<?php echo $item->slug ?>"<?php echo isset( $category ) && $category == $item->slug ? ' selected="selected"' : isset( $category ) && $category == $item->slug ? ' selected="selected"' : '' ?>><?php echo h(st( $item->title )) ?></option>
					<?php endforeach; ?>
				</select>
				<script type="text/javascript">
					$( document ).ready( function() {
						$( '#search-keyword' ).focus( function() {
							$( this ).val( '' );
						});

						$( '#select-questions-category' ).change( function() {
							var category = $( this ).val() ? 'category:' + $( this ).val() : '';
							document.location = '/questions/search/<?php echo ( $type == 'popular' ) ? 'popular/' : '' ?>' + category;
						});

					});
				</script>
			</div>
			
			
			<form action="/questions/search/" method="post">
				<div>
					<input type="text" name="keyword" id="search-keyword" value="<?php echo isset( $searchPhrase ) && !empty( $searchPhrase ) ? h( $searchPhrase ) : 'Search the Questions' ?>" />
					<input name="action" value="search" type="hidden">
									
					<input type="submit" class="button" name="submit" value="SEARCH" />
				</div>
			</form>			
			</div>
			
			<section class="view-questions">
				
				<?php if(!empty($questions)){
						foreach($questions as $k => $question){ ?>
							<article>
								<?php if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/' . $question->member_id . '_40x40.jpg')) : ?>
									<img src="<?php echo '/uploads/users/' . $question->member_id . '_40x40.jpg' ?>" alt="<?php echo h(st( $question->username )) ?>">
								<?php else : ?>
									<img src="<?php echo '/uploads/thumb-not-available.png' ?>" alt="<?php echo h(st( $question->username )) ?>">
								<?php endif; ?>
								<div>
								<h3><a href="/questions/preview/<?php echo $question->id ?>/" title="<?php echo substr(h(st( $question->text )), 0, 280 ); if (strlen($question->text) > 280) { echo '...'; } ?>"><?php echo substr(h(st( $question->text)), 0, 280 ); if (strlen($question->text) > 280) { echo '...'; } ?></a></h3>
								
								Started by: <a href="/users/<?php echo h(st($question->username)) ?>/" title="<?php echo h(st( $question->username )) ?>"><?php echo h(st( $question->username )) ?></a>
								| On: <span><?php echo date( 'd/m/Y', $question->posted_at ) ?></span>
								| Category: <a href="/questions/category/<?php echo h(st( $question->category_slug )) ?>/" title="<?php echo h(st( $question->category_name )) ?>"><?php echo h(st( $question->category_name )) ?></a> 
								
								<?php if( $question->question_answers > 0 ): ?><p class="no-padding"><a href="/questions/preview/<?php echo $question->id ?>/#answers" title="View this question answers"><?php echo $question->question_answers; echo $question->question_answers == 1 ? ' Reply' : ' Replies' ?></a> so far</p><?php endif; ?>
								</div>							
							</article>
						<?php } ?>
					<div class="clear"></div>
					<?php } else {
						?><br><center>No questions found</center><br><?php 					} ?>
			</section>
			</div>