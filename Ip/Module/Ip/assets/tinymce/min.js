ipTinyMceConfigMin = function() {
    return {
        inline: true,
        //directionality : 'ltr', TODOX current language
//    plugins : "paste,inlinepopups,iplink,autoresize",
        plugins: "paste, link",
        entity_encoding : "raw",
        menubar: false,
//    theme_advanced_buttons1 : "copy,paste,pastetext,separator,justifyleft,justifycenter,justifyright,separator,undo,redo,separator",
//    theme_advanced_buttons2 : "bold,italic,underline,styleselect",
//    theme_advanced_buttons3 : "bullist,numlist,outdent,indent,link,unlink,sub,sup",
        toolbar1: 'bold italic alignleft aligncenter alignright formatselect undo redo ',
        toolbar2: 'cut copy paste link bullist numlist outdent indent removeformat subscript superscript',
        valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
        //DOESN'T EXIST theme_advanced_styles : "Text=;Caption=caption;Signature=signature;Note=note;Button=button",
//        style_formats : [
//            {title : 'Bold text', inline : 'b'},
//            {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
//            {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
//            {title : 'Example 1', inline : 'span', classes : 'example1'},
//            {title : 'Example 2', inline : 'span', classes : 'example2'},
//            {title : 'Table styles'},
//            {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
//        ],
        formats : {
            alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
            aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
            alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
            alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
            bold : {inline : 'span', 'classes' : 'bold'},
            italic : {inline : 'span', 'classes' : 'italic'},
            underline : {inline : 'span', 'classes' : 'underline', exact : true},
            strikethrough : {inline : 'del'},
            forecolor : {inline : 'span', classes : 'forecolor', styles : {color : '%value'}},
            hilitecolor : {inline : 'span', classes : 'hilitecolor', styles : {backgroundColor : '%value'}},
            custom_format : {block : 'h1', attributes : {title : "Header"}, styles : {color : '#ff0000'}}
        },
        style_formats : [
            {title : 'Bold text', inline : 'b'},
            {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
            {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
            {title : 'Example 1', inline : 'span', classes : 'example1'},
            {title : 'Example 2', inline : 'span', classes : 'example2'},
            {title : 'Table styles'},
            {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
        ],
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