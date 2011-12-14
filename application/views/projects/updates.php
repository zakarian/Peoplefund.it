<?php 
	$active_tab = "comments";
	include("view_header.php"); 
?>			
				<div class="box">
					
					<?php if(!empty($_SESSION['user'])){ ?>

						<?php if ($project->iduser == $_SESSION['user']['iduser']){ ?>
						
						<a class="edit-project" title="Edit project" href="/<?php echo $project->slug?>/comments/">Post comment</a>
						<a class="edit-project" title="Edit project" href="/<?php echo $project->slug?>/updates/">Post project update</a>
						<div class="clear10"></div>
						
						<?php } ?>
					
						<script src="/js/jquery/jquery.form.js"></script>
						<script src="/js/site/tiny_mce/jquery.tinymce.js"></script>
						<script> 

							var cmsSection = '';
								$(document).ready(function() {
								$('textarea.mceEditor').tinymce({
									script_url : '/js/site/tiny_mce/tiny_mce.js',
						 
									theme : 'advanced',
									skin : 'o2k7',
									skin_variant : "silver",
						 
									plugins: "safari, advimage, table, widgets, contextmenu, paste, inlinepopups, emotions, ccSimpleUploader, embed, media",
						 
									theme_advanced_toolbar_location : "top",
									theme_advanced_toolbar_align	: "left",
									theme_advanced_buttons1: "bold, italic, underline, forecolor, |, justifyleft, justifycenter, justifyright, |, bullist, numlist, |, link, unlink, |, image, embed, |, removeformat, emotions",
									theme_advanced_buttons2: "",
									theme_advanced_buttons3: "",
									content_css: "/css/admin/mce.css",
									relative_urls : false,
									file_browser_callback: "ccSimpleUploader",
									//valid_elements: "iframe[*],small,object[*],embed[*],+a[id|style|rel|rev|charset|hreflang|dir|lang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],-strong/-b[class|style],-em/-i[class|style],-u[class|style],#p[id|style|dir|class|align],-ol[class|style],-ul[class|style],-li[class|style],br,img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|style|dir|id|lang|bgcolor|background|bordercolor],-tr[id|lang|dir|class|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor],tbody[id|class],thead[id|class],tfoot[id|class],#td[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|bgcolor|background|bordercolor|scope],-th[id|lang|dir|class|colspan|rowspan|width|height|align|valign|style|scope],-div[id|dir|class|align|style],-span[style|class|align],-h4[id|style|dir|class|align],dd[id|class|title|style|dir|lang],dl[id|class|title|style|dir|lang],dt[id|class|title|style|dir|lang]",
									paste_create_paragraphs : true, paste_create_linebreaks : false, paste_use_dialog : true, paste_auto_cleanup_on_paste : true, paste_convert_middot_lists : true, paste_unindented_list_class : "unindentedList", paste_convert_headers_to_strong : true, paste_remove_styles : true, paste_remove_spans: true
								});
							});
							
						</script>

						<form method="post" action="">
							<fieldset>
								<textarea name="text" rows="10" cols="80" class="mceEditor" ><?php echo @$post['text']?></textarea>
								<br />
								<input type="submit" value="Post Update" style="border: 1px solid silver; padding: 5px;">
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
													<?php if(!empty($comment->user_ext)){ ?>
														<img src="/uploads/users/<?php echo $comment->iduser?>.<?php echo $comment->user_ext?>" alt="<?php echo h(st($comment->username));?>" />
													<?php } else { 
														$project->thumb = DEFAULT_PROJECT_THUMB; ?>
														<img src="<?php echo $project->thumb?>" alt="<?php echo h(st($comment->username));?>" />
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
													<p><?php echo $comment->text?></p>
												</div>
											</div>
											<div class="clear"></div>
										</div>
										
									<?php } else if($comment->type == "update"){ ?>
										<div class="update">
											<div class="picture">
												<a href="/user/<?php echo $comment->user_slug?>/" title="<?php echo $comment->username?>">
													<?php if(!empty($comment->user_ext)){ ?>
														<img src="/uploads/users/<?php echo $comment->iduser?>.<?php echo $comment->user_ext?>" alt="<?php echo h(st($comment->username));?>" />
													<?php } else { 
														$project->thumb = DEFAULT_PROJECT_THUMB; ?>
														<img src="<?php echo $project->thumb?>" alt="<?php echo h(st($comment->username));?>" />
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
											<p><?php echo $comment->text?></p>
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
																	<a href="" title="">
																		<img src="/img/site/delete/PF_homepage_v05_19.png" alt="" />
																	</a>
																</div>
																<div class="details">
																	<a href="/user/<?php echo h(st($v->user_slug))?>/" title="<?php echo h(st($v->username))?>"><?php echo h(st($v->username))?></a> 
																	<p><?php echo $v->text?></p>
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
																<textarea name="text" cols="" rows="" onKeyPress="return submitUpdateComment(this, event, <?php echo intval(intval($comment->idupdate))?>, '<?php echo $project->user_slug?>', '<?php echo $project->username?>')" onClick="if(this.value == 'Post a comment ...') this.value = ''" onBlur="if(this.value == '') this.value = 'Post a comment ...'">Post a comment ...</textarea>
															</fieldset>
														</form>
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

<input type="hidden" id="from" value="5">
