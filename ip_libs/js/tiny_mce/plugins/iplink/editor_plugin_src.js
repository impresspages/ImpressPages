/**
 * $Id: editor_plugin_src.js 1 2009.12.18 20:56:59 Audrius $
 *
 * @author Apro Media
 * @copyright Copyright © 2009, Apro Media , All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.ImpressPagesLinkPlugin', {
		init : function(ed, url) {
			this.editor = ed;

			// Register commands
			ed.addCommand('mceIPLink', function() {
				var se = ed.selection;

				// No selection and not in link
				if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
					return;

				ed.windowManager.open({
					file : url + '/iplink.htm',
					width : 480 + parseInt(ed.getLang('iplink.delta_width', 0)),
					height : 400 + parseInt(ed.getLang('iplink.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('link', {
				title : 'advlink.link_desc',
				cmd : 'mceIPLink'
			});

			ed.addShortcut('ctrl+k', 'advlink.link_desc', 'mceIPLink');

			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('link', co && n.nodeName != 'A');
				cm.setActive('link', n.nodeName == 'A' && !n.name);
			});
		},

		getInfo : function() {
			return {
				longname : 'ImpressPages link',
				author : 'Apro Media',
				authorurl : 'http://www.aproweb.eu',
				infourl : 'http://docs.impresspages.eu',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('iplink', tinymce.plugins.ImpressPagesLinkPlugin);
})();