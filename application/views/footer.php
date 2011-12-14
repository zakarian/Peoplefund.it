<?php if(!isset($ajax)) { ?>
		<footer id="footer">
			<div class="site-width">
				<?php if(!empty($this->pagination) && $this->pagination->total_rows > 0){ ?>
					<div class="paging full">
						<?php /*Sort by: 
						<select>
							<option value="">Default</option>
						</select>&nbsp;&nbsp;*/ ?>
						<?php
							$total = (ceil($this->pagination->total_rows / $this->pagination->per_page));
							if($total > 1) { 
								if(!empty($page) && $page == "all"){ 
								?>
								&nbsp;<a href="/" title="Show all"><b>Show pages</b></a>
								
							<?php } else { ?>
								Page <?php echo (floor($page)); ?> of <?php echo (ceil($this->pagination->total_rows / $this->pagination->per_page)) ?>&nbsp;&nbsp;
								
								<?php if (isset($pagination) && !empty($pagination)){ ?>
									 
										<?php echo $pagination ?>
											 
								<?php } ?>
								
								<?php /* if(@$current_page == "index"){ ?>
									&nbsp;<a href="/projects/all/" title="Show all"><b>Show all</b></a>
								<?php } else if(@$current_page == "projects"){ ?>
									
									<?php if(!empty($all_link)){ ?>
										&nbsp;<a href="<?php echo $all_link ?>" title="Show all"><b>Show all</b></a>
									<?php } ?>
									
								<?php } */ ?>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } ?>
				<div class="suported left">
					<?php 
						$cache_file_path = CACHE_DIR . "php_cached/footer_logos.php";
						if (file_exists($cache_file_path)){
							require_once $cache_file_path;
						}else{
							$footer_logos_arr = array();
						} 
					
					?>
						<span><?php echo all_pages_footer_supported_by ?></span>					
						<?php 
							if (!empty($footer_logos_arr)){
									foreach($footer_logos_arr as $i => $flogo){ 
						?>
									<?php if (!empty($flogo['url'])){ ?>
										<a href="<?php echo $flogo['url']; ?>" title="<?php echo h($flogo['title']); ?>" target="_blank"><img src="<?php echo $flogo['image']; ?>" border="0" height="44" alt="<?php echo h($flogo['title']); ?>"></a>
									<?php }else{ ?>
										<img src="<?php echo $flogo['image']; ?>" border="0" height="<?php echo $configuration['footer_logos_height']; ?>" alt="<?php echo h($flogo['title']); ?>">
									<?php } ?>
						<?php 		}
							} 
						?>
					
				</div>
				<?php
					$pages = $this->db->query("SELECT * FROM `pages` WHERE `in_foot` = 1 AND `active` = '1' ORDER BY `order_foot` ASC")->result();
					if($pages) {
				?>
					<div class="right links">
						<?php foreach($pages as $item) { ?>
							<a href="/<?php echo h(st($item->slug)) ?>/" title="<?php echo h(st($item->title)) ?>"><?php echo h(st($item->title)) ?></a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</footer>
<?php } ?>	
		<div id="fb-root"></div>
		<script type="text/javascript">
			$(document).ready( function() {
			
				var div_cnt	= 	$('.view-projects .site-width .item').length;
				var div_h3 	= 	$('.view-projects .site-width .item h3');
				var div_p 	= 	$('.view-projects .site-width .item p');
				for(var i = 0; i < div_cnt; i += 4) {
					var tmpH2 	= 0;
					var tmpH3 	= 0;
					var tmpP 	= 0;
					
					if( div_h3.eq(i).height() > tmpH3 ) tmpH3 = div_h3.eq(i).height();
					if( div_h3.eq(i+1).height() > tmpH3 ) tmpH3 = div_h3.eq(i+1).height();
					if( div_h3.eq(i+2).height() > tmpH3 ) tmpH3 = div_h3.eq(i+2).height();
					if( div_h3.eq(i+3).height() > tmpH3 ) tmpH3 = div_h3.eq(i+3).height();

					if( div_p.eq(i).height() > tmpP ) tmpP = div_p.eq(i).height();
					if( div_p.eq(i+1).height() > tmpP ) tmpP = div_p.eq(i+1).height();
					if( div_p.eq(i+2).height() > tmpP ) tmpP = div_p.eq(i+2).height();
					if( div_p.eq(i+3).height() > tmpP ) tmpP = div_p.eq(i+3).height();
					
					div_h3.eq(i).height(tmpH3); div_p.eq(i).height(tmpP);
					div_h3.eq(i+1).height(tmpH3); div_p.eq(i+1).height(tmpP);
					div_h3.eq(i+2).height(tmpH3); div_p.eq(i+2).height(tmpP);
					div_h3.eq(i+3).height(tmpH3); div_p.eq(i+3).height(tmpP);
				}
				
				$("a[rel^='prettyPhoto']").each(function(){
					$(this).attr('href', $(this).attr( 'href' ) + 'ajax/?iframe=true&width='+$(this).attr('width')+'&height='+$(this).attr('height'));
				});
				$("a[rel^='prettyPhoto']").prettyPhoto();
				
				$('marquee').marquee('pointer').mouseover(function () {
					$(this).trigger('stop');
				}).mouseout(function () {
					$(this).trigger('start');
				}).mousemove(function (event) {
					if ($(this).data('drag') == true) {
						this.scrollLeft = $(this).data('scrollX') + ($(this).data('x') - event.clientX);
					}
				}).mousedown(function (event) {
					$(this).data('drag', true).data('x', event.clientX).data('scrollX', this.scrollLeft);
				}).mouseup(function () {
					$(this).data('drag', false);
				});
				
			});
			
			window.fbAsyncInit = function() {
				FB.init({
					appId   : '<?php echo $fb_app_id; ?>',
					session : null,
					status  : true,
					cookie  : true,
					xfbml   : true
				});
			};

			(function() {
				var e = document.createElement( 'script' );
				e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
				e.async = true;
				document.getElementById( 'fb-root' ).appendChild(e);
			}());
		</script>
		<script src="//static.getclicky.com/js" type="text/javascript"></script>
		<script type="text/javascript">try{ clicky.init(66497783); }catch(e){}</script>
		<noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/66497783ns.gif" /></p></noscript>
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-26702818-1']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</body>
</html>