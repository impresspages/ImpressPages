/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

tinyMCE.init({
  theme : "advanced",
  mode: "exact",
	elements : "management_" + this.collection_number + "_text",
	plugins : "paste,inlinepopups", 
	theme_advanced_buttons1 : "pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
	theme_advanced_buttons2 : "bold,italic,underline,styleselect",
	theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",	
  theme_advanced_statusbar_location : "bottom",
  theme_advanced_resizing : true,
 	theme_advanced_resize_horizontal : false,
	/*theme_advanced_resize_vertical : true,*/
	/*theme_advanced_path_location : "none",*/
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	height : 300,
	content_css : global_config_base_url + global_config_template_url + global_config_template + "/ip_content.css",
	theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note",
	forced_root_block : "p",


	document_base_url : global_config_base_url,
  remove_script_host : false,
	relative_urls : false,
	convert_urls : false,


  paste_auto_cleanup_on_paste : true,
  paste_retain_style_properties: false,
  paste_strip_class_attributes: true,
  paste_remove_spans: true,
  paste_remove_styles: true,
  paste_convert_middot_lists: true,
  
  paste_preprocess : function(pl, o) {
    o.content = o.content.stripScripts(); 
    var tmpContent = o.content;

    tmpContent = tmpContent.replace(new RegExp('<!(?:--[\\s\\S]*?--\s*)?>', 'g'), '') //remove comments
    tmpContent = tmpContent.replace(/(<([^>]+)>)/ig,"</p><p>");
    tmpContent = tmpContent.replace(/\n/ig," "); //remove newlines
    tmpContent = tmpContent.replace(/\r/ig," "); //remove newlines
    tmpContent = tmpContent.replace(/[\t]+/ig," "); //remove tabs
    tmpContent = tmpContent.replace(/[ ]+/ig," ");  //remove multiple spaces

    tmpContent = tmpContent.replace(/(<\/p><p>([ ]*(<\/p><p>)*[ ]*)*<\/p><p>)/ig, "</p><p>"); //remove multiple paragraphs
    
    o.content = tmpContent;
      // Content string containing the HTML from the clipboard
      //alert(o.content);
  },
  paste_postprocess : function(pl, o) {
      // Content DOM node containing the DOM structure of the clipboard
      //alert(o.node.innerHTML);
  }
}
)