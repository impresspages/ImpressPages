ipTinyMceConfigMin = {
    // Location of TinyMCE script
    script_url : ip.baseUrl + 'Ip/Module/Assets/assets/js/tiny_mce/tiny_mce.js',
    inline: true,
//    theme : "advanced",
//    plugins : "paste,inlinepopups,iplink,autoresize",
    plugins: "paste",
    entity_encoding : "raw",
//    theme_advanced_buttons1 : "copy,paste,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
//    theme_advanced_buttons2 : "bold,italic,underline,styleselect",
//    theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
    valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
    theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note;Button=button",
    forced_root_block : "p",

    gecko_spellcheck : true,

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
        ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note', 'button'));
    }
    

};