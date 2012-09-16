ipTinyMceConfigTable = {
    // Location of TinyMCE script
    script_url : ip.baseUrl + ip.libraryDir + 'js/tiny_mce/tiny_mce.js',
    
    theme : "advanced",
    plugins : "paste,inlinepopups,iplink,table,autoresize",
    entity_encoding : "raw", 
    theme_advanced_buttons1 : "copy,paste,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
    theme_advanced_buttons2 : "bold,italic,underline,styleselect",
    theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
    theme_advanced_buttons4 : "tablecontrols",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : 0,
    theme_advanced_resizing : false,
    valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li,table,tbody,thead,th,tr,td[colspan|rowspan]",
    height : 300,
    width : '100%',
    content_css : ip.baseUrl + ip.themeDir + ip.theme + "/ip_content.css",
    theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note",
    forced_root_block : "p",

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
    console.log(pl);
    console.log(o);
        ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note'));
    }
    

};