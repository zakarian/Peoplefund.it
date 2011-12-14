(function() {
	var each = tinymce.each;
	
	tinymce.PluginManager.requireLangPack('widgets');

	tinymce.create('tinymce.plugins.WidgetsPlugin', {
		init : function(ed, url) {
			var t = this;

			ed.addCommand('mceInsertForms', function( something, value ) {

				if( typeof(value) === 'undefined' ) {
					ed.windowManager.open({
						file : '/administration/forms/insert',
						width : 350,
						height : 250,
						inline : 1
					}, {
						plugin_url : url
					});
				} else {
					ed.execCommand('mceInsertContent', false, '<img src="/img/widgets/forms.gif" alt="' + value + '" class="widgetForms mceItemNoResize" title="'+ed.getLang('widgets.forms_widget')+'" />');
				}
			});

			ed.addCommand('mceInsertGalleries', function( something, value ) {

				if( typeof(value) === 'undefined' ) {
					ed.windowManager.open({
						file : '/administration/gallery/insert',
						width : 350,
						height : 250,
						inline : 1
					}, {
						plugin_url : url
					});
				} else {
					ed.execCommand('mceInsertContent', false, '<img src="/img/widgets/galleries.gif" alt="' + value + '" class="widgetGalleries mceItemNoResize" title="'+ed.getLang('widgets.galleries_widget')+'" />');
				}
			});

			ed.addCommand('mceInsertTestimonials', function( ) {

				ed.execCommand('mceInsertContent', false, '<img src="/img/widgets/testimonials.gif" class="widgetTestimonials mceItemNoResize" title="'+ed.getLang('widgets.testimonials_widget')+'" />');
			});

			ed.addCommand('mceInsertNews', function( ) {

				ed.execCommand('mceInsertContent', false, '<img src="/img/widgets/news.gif" class="widgetNews mceItemNoResize" title="'+ed.getLang('widgets.news_widget')+'" />');
			});

			ed.addCommand('mceInsertEvents', function( ) {

				ed.execCommand('mceInsertContent', false, '<img src="/img/widgets/events.gif" class="widget_events mceItemNoResize" title="'+ed.getLang('widgets.events_widget')+'" />');
			});

			ed.addButton('forms', {
				title : 'widgets.forms_button',
				cmd : 'mceInsertForms'
			});

			ed.addButton('galleries', {
				title : 'widgets.galleries_button',
				cmd : 'mceInsertGalleries'
			});

			ed.addButton('testimonials', {
				title : 'widgets.testimonials_button',
				cmd : 'mceInsertTestimonials'
			});

			ed.addButton('events', {
				title : 'widgets.events_button',
				cmd : 'mceInsertEvents'
			});

			ed.addButton('news', {
				title : 'widgets.news_button',
				cmd : 'mceInsertNews'
			});

			t._handleMoreBreak(ed, url);
		},

		_handleMoreBreak : function(ed, url) {
			var formsHTML = '<img class="widgetForms mceItemNoResize" src="/img/widgets/forms.gif" alt="$1" title="'+ed.getLang('widgets.forms_widget')+'" />';
			var galleriesHTML = '<img class="widgetGalleries mceItemNoResize" src="/img/widgets/galleries.gif" alt="$1" title="'+ed.getLang('widgets.galleries_widget')+'" />';
			var testimonialsHTML = '<img class="widgetTestimonials mceItemNoResize" src="/img/widgets/testimonials.gif" title="'+ed.getLang('widgets.testimonials_widget')+'" />';
			var eventsHTML = '<img class="widget_events mceItemNoResize" src="/img/widgets/events.gif" title="'+ed.getLang('widgets.events_widget')+'" />';
			var newsHTML = '<img class="widgetNews mceItemNoResize" src="/img/widgets/news.gif" title="'+ed.getLang('widgets.news_widget')+'" />';

			ed.onInit.add(function() {
				ed.dom.loadCSS(url + '/css/widgets.css');
			});

			// Display break instead if img in element path
			ed.onPostRender.add(function() {
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG') {
							if ( ed.dom.hasClass(o.node, 'widgetForms') )
								o.name = 'forms';
							if ( ed.dom.hasClass(o.node, 'widgetGalleries') )
								o.name = 'galleries';
							if ( ed.dom.hasClass(o.node, 'widgetTestimonials') )
								o.name = 'testimonials';
							if ( ed.dom.hasClass(o.node, 'widget_events') )
								o.name = 'events';
							if ( ed.dom.hasClass(o.node, 'widgetNews') )
								o.name = 'news';
						}

					});
				}
			});

			// Replace breaks with images
			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/<!--forms(.*?)-->/g, formsHTML);
				o.content = o.content.replace(/<!--galleries(.*?)-->/g, galleriesHTML);
				o.content = o.content.replace(/<!--testimonials-->/g, testimonialsHTML);
				o.content = o.content.replace(/<!--events-->/g, eventsHTML);
				o.content = o.content.replace(/<!--news-->/g, newsHTML);
			});

			// Replace images with break
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="widgetForms') !== -1) {
							var m, value = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--forms'+value+'-->';
						}
						if (im.indexOf('class="widgetGalleries') !== -1) {
							var m, value = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--galleries'+value+'-->';
						}
						if (im.indexOf('class="widgetTestimonials') !== -1)
							im = '<!--testimonials-->';
						if (im.indexOf('class="widget_events') !== -1)
							im = '<!--events-->';
						if (im.indexOf('class="widgetNews') !== -1)
							im = '<!--news-->';

						return im;
					});
			});

			// Set active buttons if user selected pagebreak or break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('forms', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'widgetForms'));
				cm.setActive('galleries', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'widgetGalleries'));
				cm.setActive('testimonials', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'widgetTestimonials'));
				cm.setActive('events', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'widget_events'));
				cm.setActive('news', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'widgetNews'));
			});
		},

		getInfo : function() {
			return {
				longname : 'Widgets Plugin for tinyMCE 3',
				author : 'Valentin Borisov',
				authorurl : 'http://www.mtr-design.com',
				infourl : 'http://www.mtr-design.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('widgets', tinymce.plugins.WidgetsPlugin);
})();