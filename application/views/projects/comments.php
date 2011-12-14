<?php 
	$active_tab = "comments";
	include("view_header.php"); 
?>		
<script>
$(document).ready(function(){
	/*$('#add_comment_textarea').keydown(function(e){ 
		if(e.type = 'keydown' && e.which == 13)
			$(this).height($(this).height() + 18);
	});*/
	$("#add_comment_textarea").keyup(function(){
	  FitToContent(this, document.documentElement.clientHeight)
	});
});
</script>	
				<div class="box">
					<?php if(!empty($_SESSION['user'])){ ?>
					
						<?php if ($project->iduser == $_SESSION['user']['iduser']){ ?>
						
						<a class="edit-project" title="Edit project" href="/<?php echo $project->slug?>/comments/">Post comment</a>
						<a class="edit-project" title="Edit project" href="/<?php echo $project->slug?>/updates/">Post project update</a>
						<div class="clear10"></div>
						
						<?php } ?>
					
						<form class="add-comment" action="/projects/add_comment/<?php echo $project->slug?>/" method="post">
							<fieldset>
								<textarea id="add_comment_textarea" name="text" cols="" rows="" onClick="if(this.value == 'Post a comment ...') this.value = ''" onBlur="if(this.value == '') this.value = 'Post a comment ...'">Post a comment ...</textarea>
								<input type="submit" name="" class="add" value="Send" />
							</fieldset>
						</form>
					<?php } ?>
					<div class="comments">
					
						<?php if(!empty($comments)){ ?>
							<div id="items">
								<?php foreach($comments AS $comment){ ?>
									
									<?php if($comment->type == "comment"){ ?>
										<div class="item">
											<div class="picture">
												<a href="/user/<?php echo $comment->user_slug?>/" title="<?php echo $comment->username?>">
													<?php if(!empty($comment->user_ext) && file_exists('uploads/users/'.$comment->iduser.'_40x40.jpg')){ ?>
														<img src="/uploads/users/<?php echo $comment->iduser; ?>_40x40.jpg" alt="<?php echo h(st($comment->username));?>" />
													<?php } else { ?>
														<img src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo h(st($comment->username));?>" />
													<?php } ?>
												</a>
											</div>
											<div class="left">
												<div class="justleft">	
													<a href="/user/<?php echo h(st($comment->user_slug))?>/" title="<?php echo h(st($comment->username))?>" class="fname"><?php echo h(st($comment->username))?></a><br />
													<a href="/projects/search/string:<?php echo h(st($comment->location_preview))?>/" title="<?php echo h(st($comment->location_preview))?>" class="location"><?php echo h(st($comment->location_preview))?></a>
												</div>
												<span class="date">BEFORE <?php echo date_before($comment->date_added);?></span>
												<div class="clear10"></div>
												<div class="comment">
													<p><?php echo  nl2br(st(h(stripslashes($comment->text)))) ?></p>
												</div>
											</div>
											<div class="clear"></div>
										</div>
										
									<?php } else if($comment->type == "update"){ ?>
										<div class="update">
											<div class="picture">
												<a href="/user/<?php echo $comment->user_slug?>/" title="<?php echo $comment->username?>">
													<?php if(!empty($comment->user_ext) && file_exists('uploads/users/'.$comment->iduser.'_40x40.jpg')){ ?>
														<img src="/uploads/users/<?php echo $comment->iduser; ?>_40x40.jpg" alt="<?php echo h(st($comment->username));?>" />
													<?php } else { ?>
														<img src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo h(st($comment->username));?>" />
													<?php } ?>
												</a>
											</div>
											<div class="details">
												<a class="fname" href="/user/<?php echo h(st($comment->user_slug))?>/" title="<?php echo h(st($comment->username))?>"><?php echo h(st($comment->username))?></a><div class="clear"></div>
												<a class="from" href="/<?php echo $project->slug?>/updates/" title="Updates">posted <span>project update</span></a><div class="clear"></div>
											</div>
											<span class="date">BEFORE <?php echo date_before($comment->date_added);?></span>
											<div class="clear10"></div>
											<h3><?php echo h(st($comment->title))?></h3>
											<p><?php echo stripslashes($comment->text)?></p>
											<div class="clear"></div>	
											<div class="update-comments">
											
												<div class="<?php echo intval($comment->idupdate)?>_comments">
													<?php if(!empty($comment->comments)){ ?>
													
														<?php if(count($comment->comments) > 2){ ?>
															<div class="item">
																<a href="javascript:;" onClick="showAllUpdateComments('<?php echo intval($comment->idupdate)?>');" class="view-all-comments" title="">View all <b><?php echo count($comment->comments)?> Comments</b></a>
																<div class="clear"></div>	
															</div>
														<?php } ?>
														
														<?php foreach($comment->comments AS $k => $v){ ?>
															<div class="item <?php if($k > 1){ echo "hidden_comments_".intval($comment->idupdate); }?>" <?php if($k > 1){ echo "style='display: none;'"; } ?>>
																<div class="picture">
																	<a href="/user/<?php echo $v->user_slug?>/" title="<?php echo $v->username?>">
																		<?php if(!empty($v->user_ext) && file_exists('uploads/users/'.$v->iduser.'_40x40.jpg')){ ?>
																			<img src="/uploads/users/<?php echo $v->iduser; ?>_40x40.jpg" alt="<?php echo h(st($v->username));?>" />
																		<?php } else { ?>
																			<img src="<?php echo  DEFAULT_USER_THUMB ?>" alt="<?php echo h(st($v->username));?>" />
																		<?php } ?>
																	</a>
																</div>
																<div class="details">
																	<a href="/user/<?php echo h(st($v->user_slug))?>/" title="<?php echo h(st($v->username))?>"><?php echo h(st($v->username))?></a> 
																	<p><?php echo stripslashes($v->text)?></p>
																	<br /><span class="date">BEFORE <?php echo date_before($comment->date_added);?></span>
																</div>
																<div class="clear"></div>	
															</div>
														<?php } ?>
														
													<?php } else { ?>
														<div class="item">
															<center>No comments added yet</center>
															<div class="clear"></div>	
														</div>
													<?php } ?>
												</div>
												
												<div class="item">
													<?php if(!empty($_SESSION['user'])){ ?>
														<form class="add-comment" action="" method="post">
															<fieldset>
																<textarea name="text" cols="" rows="" onKeyPress="return submitUpdateComment(this, event, <?php echo intval(intval($comment->idupdate))?>, '<?php echo $_SESSION['user']['slug']?>', '<?php echo $_SESSION['user']['username']?>','<?php echo $_SESSION['user']['iduser']?>', '<?php echo $_SESSION['user']['ext']?>')" onClick="if(this.value == 'Post a comment ...') this.value = ''" onBlur="if(this.value == '') this.value = 'Post a comment ...'">Post a comment ...</textarea>
															</fieldset>
														</form>
													<?php } else { ?>
														<i>Login to post a comment</i>
													<?php } ?>
													<div class="clear"></div>
												</div>
											</div>
											<div class="clear"></div>	
										</div>
									<?php } ?>
									<div class="clear"></div>
								<?php } ?>
							</div>
							
							<a class="older-posts" href="javascript:;" onClick="showMoreProjectUpdatesAndComments('<?php echo $project->slug?>', <?php echo $project->idproject?>);" title="Older posts">
								<span>Older posts</span>
								<div style="float: right; display: none;" id="loading_events">
									<img src="/img/wall-ajax-loader.gif">
								</div>
							</a>
							<div class="clear"></div>
						<?php } else { ?>
							<div class="clear10"></div>	
							<center>No comments added yet</center>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="project_meta">
				<?php include("sidebar.php"); ?>
			</div>
			
			<div class="clear"></div>
		</div>
		<div class="clear"></div>	
	</section>
</div>

<?php include("liked.php"); ?>