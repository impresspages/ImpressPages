ipTinyMceConfigMin = function() {
    return {
        inline: true,
        //directionality : 'ltr', TODOX current language
//    plugins : "paste,inlinepopups,iplink,autoresize",
        plugins: "paste, link",
        entity_encoding : "raw",
        menubar: false,
        toolbar1: 'bold italic alignleft aligncenter alignright styleselect removeformat',
        toolbar2: 'link bullist numlist outdent indent subscript superscript undo redo',
        valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
        paste_word_valid_elements: "strong,em,br,sup,sub,p,span,b,u,i,a,ul,ol,li",
        style_formats : [
            {title : 'Caption', inline : 'span', classes : 'caption'},
            {title : 'Note', inline : 'span', classes : 'note'},
            {title : 'Button', inline : 'span', classes : 'button'}
        ],
        //DOESN'T EXIST theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note;Button=button",
//TODOX use instead of iplink plugin?
//        link_list: [
//            {title: 'My page 1', value: 'http://www.tinymce.com'},
//            {title: 'My page 2', value: 'http://www.moxiecode.com'}
//        ],
//        or
//        link_list: "/mylist.php"

        block_formats: "Paragraph=p;Header 1=h1;Header 2=h2;Header 3=h3",
        forced_root_block : "p",

        document_base_url : ip.baseUrl,
        remove_script_host : false,
        relative_urls : false,

        paste_preprocess : function(pl, o) {
            ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note', 'button'));
        }


    }
};