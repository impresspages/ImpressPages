/*
 *	Andrew Tetlaw, Felipe Grajales - 2008/03 - for TinyMCE 3.0.5 and above
 *	A port of the FCKEditor file browser as a TinyMCE plugin.
 *	http://tetlaw.id.au/view/blog/fckeditor-file-browser-plugin-for-tinymce-editor/
 *      http://ingenian.com
 */
(function() {
	// Load plugin specific language pack
	tinymce.create('tinymce.plugins.SimpleBrowserPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			//tinyMCE.settings['file_browser_callback'] = "tinymce.plugins.SimpleBrowserPlugin";
			ed.addCommand('mceSimpleBrowser', function() {
				ed.windowManager.open({
					file : url + '/browser.html',
					width : tinyMCE.activeEditor.getParam("plugin_simplebrowser_width", '600'),
					height : tinyMCE.activeEditor.getParam("plugin_simplebrowser_height", '450'),
					inline : 1,
					browseimageurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browseimageurl", false),
					browselinkurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browselinkurl", false),
					browseflashurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browseflashurl", false)
				}, {
					plugin_url : url // Plugin absolute URL
					//some_custom_arg : 'custom arg' // Custom argument
				});
			});


			ed.addCommand('mceSimpleBrowserCallback', function(ui, params) {
				ed.windowManager.open({
					file : url + '/browser.html',
					width : tinyMCE.activeEditor.getParam("plugin_simplebrowser_width", '600'),
					height : tinyMCE.activeEditor.getParam("plugin_simplebrowser_height", '450'),
					inline : 1,
					browseimageurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browseimageurl", false),
					browselinkurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browselinkurl", false),
					browseflashurl : tinyMCE.activeEditor.getParam("plugin_simplebrowser_browseflashurl", false)
				}, {
					window : params[3],
					field_name : params[0],
    					editor_id : tinyMCE.selectedInstance.editorId,
					plugin_url: url
					//local_path_to_replace: '/home/xxx/domains/site.com/public_html/ip_libs/js/tiny_mce/plugins/simplebrowser'
				});
			});

			// Register example button
			ed.addButton('simplebrowser', {
				title : 'Simple Browser',
				cmd : 'mceSimpleBrowser',
				image : url + '/images/icons/32/ai.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('simplebrowser', n.nodeName == 'IMG');
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Simple Browser plugin',
				author : 'Andrew Tetlaw, Felipe Grajales',
				authorurl : 'http://ingenian.com',
				infourl : 'http://tetlaw.id.au/view/blog/fckeditor-file-browser-plugin-for-tinymce-editor/',
				version : "3.0.5"
			};
		}

	});

	// Register plugin
	tinymce.PluginManager.add('simplebrowser', tinymce.plugins.SimpleBrowserPlugin);
})();

function simplebrowser_browse(field_name, url, type, win) {
	var params = new Array(4);
	params[0] = field_name;
	params[1] = url;
	params[2] = type;
	params[3] = win;
	tinyMCE.activeEditor.execCommand('mceSimpleBrowserCallback', true, params);
};
