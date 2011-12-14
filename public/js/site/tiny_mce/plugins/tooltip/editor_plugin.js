(function() {
	var each = tinymce.each;
	
	tinymce.PluginManager.requireLangPack('tooltip');

	tinymce.create('tinymce.plugins.TooltipPlugin', {
		init : function(ed, url) {
			var t = this;

			ed.addCommand('mceInsertTooltip', function( something, data ) {

				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;

				var title = se.getContent({format : 'text'});

 				if( typeof(data) === 'undefined' ) {
					ed.windowManager.open({
						file : '/administration/bible/insert/title:' + title,
						width : 400,
						height : 330,
						inline : 1
					}, {
						plugin_url : url
					});
				} else {
					ed.execCommand('mceInsertContent', false, '<a class="tooltip" name="" onmouseover="Tip(\'' + data.text + '⇒' + data.footer + '\', BALLOON, true, ABOVE, true)" onmouseout="UnTip()">' + data.title + '</a>');
				}
			});

			ed.addButton('tooltip', {
				title : 'tooltip.tooltip_button',
				cmd : 'mceInsertTooltip'
			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('tooltip', co && n.nodeName != 'A');
				cm.setActive('tooltip', n.nodeName == 'A' && ed.dom.hasClass(n, 'tooltip'));
			});

			t._handleMoreBreak(ed, url);
		},

		_handleMoreBreak : function(ed, url) {

		},

		getInfo : function() {
			return {
				longname : 'Tooltip Plugin for tinyMCE 3',
				author : 'Valentin Borisov',
				authorurl : 'http://www.mtr-design.com',
				infourl : 'http://www.mtr-design.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('tooltip', tinymce.plugins.TooltipPlugin);
})();