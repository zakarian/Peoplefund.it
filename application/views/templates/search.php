<?php /*
		<script type="text/javascript">
			$(document).ready( function() {
				var div_cnt	= 	$('.advanced-search .box').length;
				var div 	= 	$('.advanced-search .box');
				for(var i = 0; i < div_cnt; i += 3) {
					var tmp 	= 0;
					if( div.eq(i).height() > tmp ) tmp = div.eq(i).height();
					if( div.eq(i+1).height() > tmp ) tmp = div.eq(i+1).height();
					if( div.eq(i+2).height() > tmp ) tmp = div.eq(i+2).height();
					div.eq(i).height(tmp); div.eq(i+1).width(div.eq(i).width()); // ie hack
					div.eq(i+1).height(tmp);
					div.eq(i+2).height(tmp);
				}
			});
		</script>			
		<div class="site-width">
			<div class="advanced-search">
				<div class="column-one">
					<h2>Advanced search</h2>
					<div class="box">
						<form action="/projects/search/" method="post">
							<fieldset>
								<label for="search_key_words">Key words:</label>
								<input style="width: 263px;" id="search_key_words" class="query clear-text focus-style" type="text" name="keyword" class="" <?php if(!empty($keywords)){ ?>value="<?php echo h(st(stripslashes($keywords)))?>"<?php } else { ?>value="Bike, village, feild etc"<?php } ?> />
							</fieldset>
							<fieldset>
								<label for="search_category">Category:</label>
								<select id="search_category" name="category" class="select">
									<option value="" selected="selected">Select category</option>
									<?php foreach($categories as $row){ ?>
										<option value="<?php echo h(st($row->title)) ?>"<?php if(@$category == $row->title){ echo ' selected="selected"'; } ?>>&nbsp;&nbsp;<?php echo h(st($row->title)) ?></option>
										<?php if(!empty($row->subcategories)) { 
												foreach($row->subcategories as $subcategory){ ?>
													<option value="<?php echo h(st($subcategory->title)) ?>"<?php if(@$category == $subcategory->title){ echo ' selected="selected"'; } ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo h(st($subcategory->title)) ?></option>
										<?php 	} 
											} ?>
									<?php } ?>
								</select>
							</fieldset>
							<fieldset>
								<label for="search_nearby">Nearby:</label>
								<input id="search_nearby" class="query clear-text focus-style" type="text" name="string" class="" <?php if(!empty($string)){ ?>value="<?php echo h(st(stripslashes($string)))?>"<?php } else { ?>value="Enter town or postcode" <?php } ?> />
								<input type="submit" name="submit" value="search" class="button" />
							</fieldset>
						</form>
						<div class="clear"></div>
					</div>
				</div>
				<div class="column-one">
					<h2>Quick search</h2>
					<div class="box">
						<div class="categories">
							<div class="clear5"></div>
							<span class="title clip">Categories</span>
							<ul>
								<?php foreach($categories as $k => $row)
										if($k % 3 == 0)
											include('../application/views/templates/categories.php');
								?>
							</ul>
							<ul>
								<?php $i = 1;
									foreach($categories as $k => $row)
										if($k == $i) {
											include('../application/views/templates/categories.php');
											$i += 3;
										}
								?>
							</ul>
							<ul>
								<?php $i = 2;
									foreach($categories as $k => $row)
										if($k == $i) {
											include('../application/views/templates/categories.php');
											$i += 3;
										}
								?>
							</ul>
							<div class="clear20"></div>
								<span class="title star">Featured</span>
								<ul>
									<li><a rel="tag" href="/projects/most_recent/" title="Most Recent">Most Recent</a></li>
									<li><a rel="tag" href="/projects/most_funded/" title="Most Funded">Most Funded</a></li>
									<li><a rel="tag" href="/projects/ending_soon/" title="ENDING SOON">ENDING SOON</a></li>
								</ul>
								<ul>
									<li><a rel="tag" href="/projects/most_watched/" title="Most Watched">Most Watched</a></li>
									<li><a rel="tag" href="/projects/our_picks/" title="Our Picks">Our Picks</a></li>
								</ul>
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div class="column-one right latest">
					<h2>Find projects near you</h2>
					<div class="box">
						<form class="postcode_form" action="/projects/search/" method="post" onSubmit="return checkSearchString('string');">
							<fieldset>
								<label for="search_postcode">Postcode:</label>
								<input id="string" class="query clear-text focus-style" type="text" name="string" class="" value="Enter postcode" />
								<input type="submit" name="" value="go" class="button" />
							</fieldset>
						</form>
						<div class="clear20"></div>
						<div class="featured">
							<span class="title pin">Cities</span>
							<?php if(!empty($cities)){ ?>
								<ul>
									<?php
										foreach($cities as $k => $city)
											if($k % 3 == 0)
												include('../application/views/templates/cities.php');
									?>
								</ul>
								<ul>
									<?php
										$i = 1;
										foreach($cities as $k => $city)
											if($k == $i){
												include('../application/views/templates/cities.php');
												$i += 3;
											}
									?>
								</ul>
								<ul>
									<?php
										$i = 2;
										foreach($cities as $k => $city)
											if($k == $i){
												include('../application/views/templates/cities.php');
												$i += 3;
											}
									?>
								</ul>
							<?php } else { ?>
								<center>No cities found</center>
							<?php } ?>
						</div>	
						<div class="clear"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
*/ ?>

		<div class="site-width">
			<div class="clear10"></div>
			<section class="global-search">
				<form action="/" method="post">
					<label><?php echo homepage_search_by_category ?></label>
					<div class="categories" style="float: none;">
						<?php
							if(!empty($categories)){
								foreach($categories as $cat){ ?>
									<span class="thumb-category-<?php echo slugify(h(st($cat->title))) ?>"></span>
									<a href="javascript:;" onClick="markCategoryForSearch(<?php echo $cat->idcategory ?>);" id="category_<?php echo $cat->idcategory?>" class="search_categories<?php echo (isset($searchSql['category']) && preg_match("/".$cat->title."/", $searchSql['category'])) ? ' active' : '' ?>"><?php echo h(st($cat->title)) ?></a>
						<?php } } ?>
						<script>
							function markCategoryForSearch(idcategory){
								
								if($("#category_"+idcategory).hasClass("active")){
									$("#category_"+idcategory).removeClass("active");
								} else {
									$("#category_"+idcategory).addClass("active");
								}
							}
							function searchMultipleCategories(){
								var categories = $(".search_categories");
								var active_categories = new Array();
								
								$.each(categories, function(key, value) { 
									var idcategory = value.id;
									
									if($("#"+idcategory).hasClass("active")){
										active_categories.push($("#"+idcategory).text());
										
									}
								});
								
								var active_categories_string = active_categories.join(",");
								
								window.location.href = '/projects/search/category:'+js_urlencode(active_categories_string)+'/';
							}
						</script>
					</div>
					<input type="button" class="button" value="Go" onClick="searchMultipleCategories();"/>
				</form>
				<div class="clear"></div>
			</section>
			<section class="global-search">
				<form action="/projects/search/" method="post">
					<label for=""><?php echo homepage_or_search_by ?></label>
					<select class="query" name="order">
						<option<?php echo (isset($filter_type) && $filter_type == 'latest') ? ' selected="selected"' : '' ?> value="latest">Most Recent</option>
						<option<?php echo (isset($filter_type) && $filter_type == 'picks') ? ' selected="selected"' : '' ?> value="picks">Our Picks</option>
						<option<?php echo (isset($filter_type) && $filter_type == 'liked') ? ' selected="selected"' : '' ?> value="liked">Most Liked</option>
						<option<?php echo (isset($filter_type) && $filter_type == 'funded') ? ' selected="selected"' : '' ?> value="funded">Most Funded</option>
						<option<?php echo (isset($filter_type) && $filter_type == 'ending_soon') ? ' selected="selected"' : '' ?> value="ending_soon">Ending Soon</option>
					</select>
					<label for=""><?php echo homepage_nearby ?></label>
					<input type="text" name="string" class="query clear-text focus-style" value="<?php echo (isset($searchSql['string']) && $searchSql['string']) ? h(st($searchSql['string'])) : 'postcode' ?>" />
					<label for=""><?php echo homepage_keywords ?></label>
					<input type="text" name="keyword" class="query clear-text focus-style" value="<?php echo (isset($searchSql['keywords']) && $searchSql['keywords']) ? h(st($searchSql['keywords'])) : 'keyword' ?>" />
					<input style="margin-left: 0;" type="submit" class="button" value="Go" />
				</form>
				<div class="clear"></div>
			</section>
		</div>		