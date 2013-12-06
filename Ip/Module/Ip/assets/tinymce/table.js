ipTinyMceConfigTable = function() {
    return {
        inline: true,
        //directionality : 'ltr', //TODO according to the current language
        plugins: "paste, link, table",
        entity_encoding : "raw",
        menubar: false,
        toolbar1: 'bold italic alignleft aligncenter alignright styleselect removeformat',
        toolbar2: 'link bullist numlist outdent indent subscript superscript undo redo',
        toolbar3: 'tablecontrols',
        valid_elements : "@[class|style],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li,table,tbody,thead,th,tr,td[colspan|rowspan]",
        paste_word_valid_elements: "strong,em,br,sup,sub,p,span,b,u,i,a,ul,ol,li",
        style_formats : [
            {title : 'Note', inline : 'span', classes : 'note'},
            {title : 'Button', inline : 'span', classes : 'button'}
        ],
        forced_root_block : "p",

        document_base_url : ip.baseUrl,
        remove_script_host : false,
        relative_urls : false,

        paste_preprocess : function(pl, o) {
            ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note', 'button'));
        }
    }
};