
var html =  '<div class="pf_container"><div class="item"<?php if(isset($attrItem)) echo $attrItem ?>>' +
			'<h2>' +
			'<span class="clip"></span>' +
			'<span class="category">' +
			'CATEGORIES: <span class="clear5"></span>' +
			'<?php foreach($project->categories AS $category){ ?><a target="_blank" class="thumb-category-<?php echo slugify(h(st($category->title))) ?>" rel="tag" href="<?php echo $webroot ?>/projects/search/category:<?php echo urlencode(h(st($category->slug))) ?>/" title="<?php echo h(st($category->title)) ?>"><?php echo h(st($category->title)) ?></a>' +
			'<?php } ?>' +
			'</span>' +
			'<span class="clear"></span>' +
			'</h2>' +
			'<h3><a target="_blank" href="<?php echo $webroot ?>/<?php echo h(st($project->slug))?>" title="<?php echo h(st($project->title)); ?>"><?php echo h(st($project->title)); ?></a></h3>' +
			'<a target="_blank" href="<?php echo $webroot ?>/<?php echo h(st($project->slug))?>" title="<?php echo h(st($project->title)); ?>">' +
			<?php if(!empty($project->ext)){ ?>
			'<img width="211" height="130" src="<?php echo $webroot ?>/uploads/projects/<?php echo $project->idproject?>_211x130.jpg" alt="<?php echo h(st($project->title));?>" />' +
			<?php } else { $project->thumb = DEFAULT_PROJECT_THUMB; ?>
			'<img width="211" height="130" src="<?php echo $webroot ?>/<?php echo $project->thumb?>" alt="<?php echo h(st($project->title));?>" />' +
			<?php } ?>
			'</a>' +
			'<p><?php echo str_replace(array("\n", "\t", "\r"), '', addslashes(h(st($project->outcome)))); ?></p>' +
			'<div class="stats">' +
			'<span class="unit">SO FAR: <span>&pound;<?php echo number_format($project->amount_pledged)?></span></span>' +
			'<div class="bar">' +
			'<div style="width: <?php echo $project->pledged_percent?>%;" class="fill">&nbsp;<?php echo $project->pledged_percent?>%&nbsp;</div>' +
			'</div>' +
			'<span class="from-to">' +
			'<span class="from">&pound;0</span>' +
			'<span class="to">&pound;<?php echo number_format($project->amount)?></span>' +
			'<span class="clear5"></span>' +
			'</span>' +
			'<span class="unit">day<?php if($project->period > 1) echo 's' ?> Remaining: <span><?php echo $project->days_left?></span></span>' +
			'<div class="bar">' +
			'<div style="width: <?php echo h(st($project->pledged_days)) ?>%;" class="fill">&nbsp;<?php echo h(st($project->pledged_days)) ?>%&nbsp;</div>' +
			'</div>' +
			'<span class="from-to">' +
			'<span class="from">0</span>' +
			'<span class="to"><?php echo h(st($project->period)) ?> day<?php if($project->period > 1) echo 's' ?></span>' +
			'<span class="clear"></span>' +
			'</span>' +
			'<p>Help make it happen with your spare cash, time and skills on peoplefund.it</p>' +
			'</div>' +
			'</div>' +
			'</div>' +
			
			'<style>' + 
			'* { border: 0; outline: none; padding: 0; margin: 0; list-style: none; font-weight: normal; }' + 
			'body, html { background-color: #FFFFFF; color: #000000; font-weight: normal; font-size: 12px; font-family: Verdana; }' +	
			'strong, b { font-weight: bold; }' +	
			'.clear { display: block; clear: both; overflow: hidden; height: 0px;  }' +	
			'.clear5 { display: block; clear: both; overflow: hidden; height: 5px;  }' +	
			'.clear10 { display: block; clear: both; overflow: hidden; height: 10px; }' +	
			'.clear15 { display: block; clear: both; overflow: hidden; height: 15px; }' +	
			'.clear20 { display: block; clear: both; overflow: hidden; height: 20px; }' +	
			'.clear25 { display: block; clear: both; overflow: hidden; height: 25px; }' +	
'span.thumb-category-energy,a.thumb-category-energy{display:block;width:28px;height:26px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Energy.png) left top no-repeat;float:left;margin:-1px 0 0 15px;overflow:hidden;text-indent:-9999px;}span.thumb-category-food,a.thumb-category-food{display:block;width:15px;height:19px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Food.png) left top no-repeat;float:left;margin:2px 0 0 15px;overflow:hidden;text-indent:-9999px;}span.thumb-category-health,a.thumb-category-health{display:block;width:24px;height:23px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Health.png) left top no-repeat;float:left;margin:0 0 0 15px;overflow:hidden;text-indent:-9999px;}span.thumb-category-community,a.thumb-category-community{display:block;width:27px;height:22px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Community.png) left top no-repeat;float:left;margin:0 0 0 15px;overflow:hidden;text-indent:-9999px;}span.thumb-category-environment,a.thumb-category-environment{display:block;width:25px;height:24px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Environment.png) left top no-repeat;float:left;margin:0 0 0 15px;overflow:hidden;text-indent:-9999px;}span.thumb-category-recreation,a.thumb-category-recreation{display:block;width:29px;height:26px;background:transparent url(<?php echo $webroot ?>/img/site/cats/Recreation.png) left top no-repeat;float:left;margin:-2px 0 0 15px;overflow:hidden;text-indent:-9999px;}a.thumb-category-energy,a.thumb-category-food,a.thumb-category-health,a.thumb-category-community,a.thumb-category-environment,a.thumb-category-recreation{margin-left:0;margin-right:6px;}a.thumb-category-energy:hover,a.thumb-category-food:hover,a.thumb-category-health:hover,a.thumb-category-community:hover,a.thumb-category-environment:hover,a.thumb-category-recreation:hover{background-position:bottom left;}' +	
			'.pf_container .item { width: 216px !important; padding: 7px !important; background-color: #F0F0F0 !important; float: left !important; margin-right: 5px !important; }' + 
			'.pf_container .item:hover { -moz-box-shadow: 0 0 3px #BFBFBF !important; -webkit-box-shadow: 0 0 3px #BFBFBF !important; box-shadow: 0 0 3px #BFBFBF !important; }' + 
			'.pf_container .item h2 { background-color: #D6D6D6 !important; padding: 4px 7px !important; -moz-border-radius: 3px !important; -webkit-border-radius: 3px !important; -khtml-border-radius: 3px !important; border-radius: 3px !important; text-transform: uppercase !important; }' + 
			'.pf_container .item h2, .item h2 a { font-size: 11px !important; color: #C37814 !important; text-transform: uppercasec !important; }' + 
			'.pf_container .item h3 a, .item h2 a { text-decoration: none !important; }' + 
			'.pf_container .item h2 a:hover { color: #005b7f !important; }' + 
			'.pf_container .item h3 a:hover { color: #C37814 !important; }' + 
			'.pf_container .item h2 span.clip, .item h2 span.category { display: block !important; }' + 
			'.pf_container .item h2 span.clip { float: left !important; }' + 
			'.pf_container .item h2 span.category { overflow: hidden !important; }' + 
			'.pf_container .item h2 span.clip { width: 11px !important; height: 24px !important; margin-top: -11px !important; background: transparent url(<?php echo $webroot ?>/img/site/site.png) -222px 0 no-repeat !important; }' + 
			'.pf_container .item p, .item h3, .item h3 a { padding: 7px 0 !important; color: #000 !important; }' + 
			'.pf_container .item h3, .item h3 a { color: #005B7F !important; }' + 
			'.pf_container .item h3, .item h3 a { font-size: 14px !important; overflow: hidden !important; max-height: 50px !important; height: expression(this.height > 50 ? 50 : true) !important; }' + 
			'.pf_container .item p { font-size: 11px !important; line-height: 15px !important; max-height: 39px !important; height: expression(this.height > 39 ? 39 : true) !important; overflow: hidden !important; }' + 
			'.pf_container .item .stats span.unit, .pf_container .item .stats span.from-to { display: block !important; }' +
			'.pf_container .item .stats span.unit, .pf_container .item .stats span.unit span { font-size: 13px !important; color: #231F20 !important; text-transform: uppercase !important; font-weight: bold !important; }' +
			'.pf_container .item .stats span.unit span { color: #005B7F !important; }' +
			'.pf_container .item .stats span.from-to .from, .pf_container .item .stats span.from-to .to { font-size: 10px !important; display: block !important; }' +
			'.pf_container .item .stats span.from-to .from { float: left !important; }' +
			'.pf_container .item .stats span.from-to .to { float: right !important; }' +
			'.pf_container .item .stats .bar { height: 16px !important; background-color: #BEBEBE !important; margin: 3px 0 !important; }' +
			'.pf_container .item .stats .bar .fill { height: 16px !important; line-height: 16px !important; background-color: #005B7F !important; text-align: right !important; color: #FFF !important; font-size: 10px !important; }' +
			'.pf_container .box-items { background-color: #EEF2F4 !important; padding: 10px 0 !important; margin-bottom: 10px !important; }' +
			'.pf_container .box-items .see_all { float: right !important; font-size: 11px !important; color: #C37814 !important; text-transform: uppercase !important; text-decoration: none !important; line-height: 26px !important; }' +
			'.pf_container .box-items .see_all:hover { color: #005B7F !important; }' +
			'.pf_container .box-items {  margin-top: 0 !important;  padding: 10px !important; }' +
			'.pf_container .box-items .item { width: 211px !important; }' +
			'</style>';

document.write( html );