(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('example');

	tinymce.create('tinymce.plugins.MaxChars', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			var t=this;
			t.editor = ed;
			
			ed.addCommand('CheckMaxChars', t._checkMaxChars, t);
			
			ed.onPostRender.add(function(ed,o) { 
				if (tinymce.isIE){
					ed.getBody().innerText.replace(/<\/?[^>]+(>|$)/g,"").replace(/&nbsp;/g,"");						 
				}
				else{
					getContent = ed.getBody().textContent.replace(/<\/?[^>]+(>|$)/g,"").replace(/&nbsp;/g,"");
				} 
			 			 
    		t._MCtext = ed.getBody().innerHTML;	 }, t);
			
			ed.onKeyUp.add(t._checkMaxChars, t);
			ed.onChange.add(t._checkMaxChars, t); 
			ed.onSetContent.add(t._checkMaxChars, t);
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname 	: 'Maximum Characters',
				author 		: 'Lorenzo Campanis',
				authorurl 	: 'http://www.lcampanis.com',
				infourl 	: 'http://www.lcampanis.com',
				version 	: "2.0"
			};
		},
		
		_checkMaxChars : function() {
			var ed = this.editor;
			
			var mc = parseInt(ed.getParam("max_chars"));//how many characters (int)
			var no_spaces = ed.getParam("max_chars_nospaces");//count spaces or not (1 or empty)
			var lb = ed.getParam("max_chars_indicator");//ID of div, span container or input
			var mn = ed.getParam("max_chars_text","");//message before the number of characters
			var mm = ed.getParam("max_chars_maxText","");//message when max chars are reached
			
			var cnt_cur = "";//current text content
        	var cnt_html = "";//current html content
        
			if (tinymce.isIE){
				cnt_cur = ed.getBody().innerText.replace(/<\/?[^>]+(>|$)/g,"").replace(/&nbsp;/gi,"");
				cnt_html =  ed.getBody().innerHTML;
			}else{
				cnt_cur = ed.getBody().textContent.replace(/<\/?[^>]+(>|$)/g,"").replace(/&nbsp;/gi,"");
				cnt_html =  ed.getBody().innerHTML;
			} 	
			
			var cnt = cnt_cur.replace(/<\/?[^>]+(>|$)/g, "");
			//check for no spaces count
			if(no_spaces==1)
				cnt = cnt.replace(/\s/g, "");
		
			if( mc > 0 && cnt.length >= 0 && mc<cnt.length && this._MCtext != cnt_cur ) {				
				ed.setContent(this._MCtext);				
			}
			else
				this._MCtext = cnt_html;
			
			//check for indicator
			if(lb = document.getElementById(lb)) {
				var lb_val = (lb.tagName.toLowerCase() == "input") ? lb.value : parseInt(lb.innerHTML);	
				
				if(lb_val != null) {				
					if(lb.tagName.toLowerCase()=='input'){
						if (mc - cnt.length < 0) {
							if(mm!="") 
					    		lb.value = mm;
						}else{ 
							lb.value = mn + " " + (mc - cnt.length) + "/" + mc;
						}
					}
					else{
						if (mc - cnt.length < 0) {
							//alert(cnt.length);
							if(mm!="") 
								lb.innerHTML =  mm;
							//tinyMCE.execCommand(tinyMCE.execCommand(ed.id,'mceFocus', false));
						}
						else{
							lb.innerHTML = mn + " " + (mc - cnt.length) + "/" + mc;
						}				  
				    }
				}
			}
			return 1;
		}
	});

	// Register plugin
	tinymce.PluginManager.add('maxchars', tinymce.plugins.MaxChars);
})();