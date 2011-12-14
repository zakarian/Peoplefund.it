<?php 
	$active_tab = "backers";
	include("view_header.php"); 
?>		
				<div class="box">
					<div class="alphabet-filter">
						<a <?php if(@$letter == "A"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/A/" title="A">A</a> | 
						<a <?php if(@$letter == "B"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/B/" title="B">B</a> |  
						<a <?php if(@$letter == "C"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/C/" title="C">C</a> |  
						<a <?php if(@$letter == "D"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/D/" title="D">D</a> |  
						<a <?php if(@$letter == "E"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/E/" title="E">E</a> |  
						<a <?php if(@$letter == "F"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/F/" title="F">F</a> |  
						<a <?php if(@$letter == "G"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/G/" title="G">G</a> |  
						<a <?php if(@$letter == "H"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/H/" title="H">H</a> |  
						<a <?php if(@$letter == "I"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/I/" title="I">I</a> |  
						<a <?php if(@$letter == "J"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/J/" title="J">J</a> |  
						<a <?php if(@$letter == "K"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/K/" title="K">K</a> |  
						<a <?php if(@$letter == "L"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/L/" title="L">L</a> |  
						<a <?php if(@$letter == "M"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/M/" title="M">M</a> |  
						<a <?php if(@$letter == "N"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/N/" title="N">N</a> |  
						<a <?php if(@$letter == "O"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/O/" title="O">O</a> |  
						<a <?php if(@$letter == "P"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/P/" title="P">P</a> |  
						<a <?php if(@$letter == "Q"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Q/" title="Q">Q</a> |  
						<a <?php if(@$letter == "R"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/R/" title="R">R</a> |  
						<a <?php if(@$letter == "S"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/S/" title="S">S</a> |  
						<a <?php if(@$letter == "T"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/T/" title="T">T</a> |  
						<a <?php if(@$letter == "U"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/U/" title="U">U</a> |  
						<a <?php if(@$letter == "V"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/V/" title="V">V</a> |  
						<a <?php if(@$letter == "W"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/W/" title="W">W</a> |  
						<a <?php if(@$letter == "X"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/X/" title="X">X</a> |  
						<a <?php if(@$letter == "Y"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Y/" title="Y">Y</a> |  
						<a <?php if(@$letter == "Z"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Z/" title="Z">Z</a>   
					</div>
					<?php /*<div class="inner-paging">
						Page 1 of 3  &lsaquo; Previous 1 
						<a href="" title="">2</a> 
						<a href="" title="">3</a> 
						<a href="" title="">Next &rsaquo;</a>   
						<a href="" title="">Show all</a> 
					</div>*/ ?>
						
					<?php if(!empty($pledges)){ ?>
						<div class="people">
							
							<?php foreach($pledges AS $k => $pledge){ 
								if(!$pledge->public) {
									$pledge->user_ext = $pledge->location_preview = $pledge->username = '';
								}
								?>
								<div class="item <?php if(($k + 1) % 4 == 0){ ?>latest<?php } ?>">
									<?php if(!empty($pledge->username)){ ?><a href="/user/<?php echo $pledge->user_slug?>/" title="<?php echo $pledge->username?>"><?php } ?>
										<?php if (!empty($pledge->user_ext) && file_exists('uploads/users/'.$pledge->iduser.'_150x150.jpg')){ ?>
											<img width="150" height="150" src="/uploads/users/<?php echo $pledge->iduser; ?>_150x150.jpg" alt="<?php echo h(st($pledge->username)); ?>" />
										<?php }else{ ?>
											<img width="150" height="150" src="<?php echo  DEFAULT_USER_SMALL_THUMB ?>" alt="<?php echo h(st($pledge->username)) ?>" />
										<?php } ?>
									<?php if(!empty($pledge->username)){ ?></a><?php } ?>
									<?php if(!empty($pledge->username)){ ?>
									<a class="fname" href="/user/<?php echo $pledge->user_slug?>/" title="<?php echo $pledge->username?>"><?php echo $pledge->username?></a><div class="clear"></div>
									<?php } ?>
									<?php if(!empty($pledge->location_preview)){ ?>
										<a class="location" href="/projects/search/string:<?php echo $pledge->location_preview?>/" title=""><?php echo $pledge->location_preview?></a><div class="clear"></div>
									<?php } ?>
								</div>
								<?php if(($k+1)%4==0) { ?>
									<div class="clear"></div>
								<?php } ?>
							<?php } ?>

							<div class="clear10"></div>
						</div>
					<?php } else { ?>
						<center>
							No backers found
						</center>
						<br>
					<?php } ?>
					<div class="alphabet-filter">
						<a <?php if(@$letter == "A"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/A/" title="A">A</a> | 
						<a <?php if(@$letter == "B"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/B/" title="B">B</a> |  
						<a <?php if(@$letter == "C"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/C/" title="C">C</a> |  
						<a <?php if(@$letter == "D"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/D/" title="D">D</a> |  
						<a <?php if(@$letter == "E"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/E/" title="E">E</a> |  
						<a <?php if(@$letter == "F"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/F/" title="F">F</a> |  
						<a <?php if(@$letter == "G"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/G/" title="G">G</a> |  
						<a <?php if(@$letter == "H"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/H/" title="H">H</a> |  
						<a <?php if(@$letter == "I"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/I/" title="I">I</a> |  
						<a <?php if(@$letter == "J"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/J/" title="J">J</a> |  
						<a <?php if(@$letter == "K"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/K/" title="K">K</a> |  
						<a <?php if(@$letter == "L"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/L/" title="L">L</a> |  
						<a <?php if(@$letter == "M"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/M/" title="M">M</a> |  
						<a <?php if(@$letter == "N"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/N/" title="N">N</a> |  
						<a <?php if(@$letter == "O"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/O/" title="O">O</a> |  
						<a <?php if(@$letter == "P"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/P/" title="P">P</a> |  
						<a <?php if(@$letter == "Q"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Q/" title="Q">Q</a> |  
						<a <?php if(@$letter == "R"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/R/" title="R">R</a> |  
						<a <?php if(@$letter == "S"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/S/" title="S">S</a> |  
						<a <?php if(@$letter == "T"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/T/" title="T">T</a> |  
						<a <?php if(@$letter == "U"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/U/" title="U">U</a> |  
						<a <?php if(@$letter == "V"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/V/" title="V">V</a> |  
						<a <?php if(@$letter == "W"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/W/" title="W">W</a> |  
						<a <?php if(@$letter == "X"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/X/" title="X">X</a> |  
						<a <?php if(@$letter == "Y"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Y/" title="Y">Y</a> |  
						<a <?php if(@$letter == "Z"){ ?>class="active"<?php } ?> href="/<?php echo h(st($project->slug))?>/backers/Z/" title="Z">Z</a>   
					</div>
					<?php /*<div class="inner-paging">
						Page 1 of 3  &lsaquo; Previous 1 
						<a href="" title="">2</a> 
						<a href="" title="">3</a> 
						<a href="" title="">Next &rsaquo;</a>   
						<a href="" title="">Show all</a> 
					</div>*/ ?>
					<div class="clear"></div>
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

<?php include('liked.php') ?>