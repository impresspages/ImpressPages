ipTinyMceConfigMed = {
    // Location of TinyMCE script
    script_url : ip.baseUrl + ip.libraryDir + 'js/tiny_mce/tiny_mce.js',
    
    theme : "advanced",
    plugins : "paste,inlinepopups,iplink,simplebrowser,advhr,advimage",
    entity_encoding : "raw", 
    theme_advanced_buttons1 : "copy,paste,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
    theme_advanced_buttons2 : "bold,italic,underline,styleselect,image,code",
    theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : false,
    valid_elements : "@[class|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|name],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
    height : 300,
    width : '100%',
    content_css : ip.baseUrl + ip.themeDir + ip.theme + "/ip_content.css",
    theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note",
    forced_root_block : "p",

    gecko_spellcheck : true,

    file_browser_callback : "simplebrowser_browse", 

    document_base_url : ip.baseUrl,
    remove_script_host : false,
    relative_urls : false,
    convert_urls : true,

    paste_auto_cleanup_on_paste : true,
    paste_retain_style_properties : "",
    paste_strip_class_attributes : false,
    paste_remove_spans : false,
    paste_remove_styles : true,
    paste_convert_middot_lists : true,
    paste_text_use_dialog : true,    
    
    paste_preprocess : function(pl, o) {
        ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note'));
    }
    

};