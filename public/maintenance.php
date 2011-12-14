<?php
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 3600');
?>
<html>
	<head>
		<title>Site upgrade in progress</title>
		<meta name="robots" content="none" />
		<style>
			@charset "utf-8";
			
			* {	
				border: 0; 
				outline: none; 
				padding: 0;
				margin: 0; 
				list-style: none; 
				font-weight: normal; 
			}

			body, html {
				background-color: #FFFFFF;
				color: #000000;
				font-weight: normal; 
				font-size: 12px;
				font-family: Verdana;
			}

			textarea,
			select,
			input {
				color: #000000;
				font-weight: normal; 
				font-size: 12px;
				font-family: Verdana;
			}

			header, article, section, nav, hgroup, footer 				{ display: block; }
			blockquote:before, blockquote:after, q:before, q:after  { content: "";	  }
			blockquote, q 										{ quotes: "" "";  }

			p		 { padding: 0 0 15px 0; 			}
			textarea	 { resize: none; 				}
			a 		 { text-decoration: underline; 	}
			a:hover   { text-decoration: none; 		}

			strong, b { font-weight: bold; 		  	}
			u 		 { text-decoration: underline; 	}
			i, em	 { font-style: italic; 		  	}
			abbr		 { border-bottom: 1px dashed;	}

			.center { text-align: center; }

			.clear     { display: block; clear: both; overflow: hidden; height: 0px;  }
			.clear5   { display: block; clear: both; overflow: hidden; height: 5px;  }
			.clear10 { display: block; clear: both; overflow: hidden; height: 10px; }
			.clear15 { display: block; clear: both; overflow: hidden; height: 15px; }
			.clear20 { display: block; clear: both; overflow: hidden; height: 20px; }
			.clear25 { display: block; clear: both; overflow: hidden; height: 25px; }
			.clear30 { display: block; clear: both; overflow: hidden; height: 30px; }
			.clear35 { display: block; clear: both; overflow: hidden; height: 35px; }

			.left             { float: left !important;  }
			.right           { float: right !important; }
			.justright  { float: right !important; }
			.justleft    { float: left !important;  }

			.block 		{ display: block; 	  }
			.hidden 		{ display: none;  	  }
			.visibility 	{ visibility: hidden; }
			.relative 	{ position: relative; }
			.absolute 	{ position: absolute; }
			.overflow 	{ overflow: hidden;   }
			
			/* USERBAR */
			#userbar {
				width: 100%;
				height: 40px;
				line-height: 40px;
				background: transparent url(/img/site/userbar.png) 0 0 repeat-x;
				overflow: hidden;
				border-bottom: 2px solid #02161E;
			}
			.site-width {
				width: 936px;
				margin: 0 auto;
				position: relative;
			}
			
			#footer .suported {
				position: absolute;
				top: 60px;
				left: 2px;
				height: 44px;
			}

			#footer .suported span {
				display: block;
				font-size: 11px;
				padding-bottom: 3px;
				clear: both;
			}	
			
			#footer .site-width {
				height: 130px;
				background: transparent url(../../img/site/footer.png) left top repeat-x;
			}
		</style>
	</head>
	<body>
		<div id="userbar">
			
		</div>
		<div class="site-width">
			<div class="clear20"></div>
			<img src="/img/maintenance.jpg" alt="The peoplefund.it site will be back up as soon we’ve finished some essential site maintenance!" />
			
		</div>
		<footer id="footer">
			<div class="site-width">
				<div class="suported left">
					<span>Supported by:</span>					
					<img src="/uploads/partners/10.png" border="0" height="44" alt="NESTA">
					<img src="/uploads/partners/18.jpg" border="0" height="44" alt="Fish Fight">
					<a href="http://www.rivercottage.net" title="River Cottage" target="_blank"><img src="/uploads/partners/11.png" border="0" height="44" alt="River Cottage"></a>
					<a href="http://www.energyshare.com" title="EnergyShare" target="_blank"><img src="/uploads/partners/12.png" border="0" height="44" alt="EnergyShare"></a>
					<img src="/uploads/partners/13.jpg" border="0" height="44" alt="Landshare">
					<img src="/uploads/partners/15.png" border="0" height="44" alt="Forum for the Future">
				</div>
			</div>
		</footer>
	</body>
</html>