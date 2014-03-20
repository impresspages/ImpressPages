ipTinyMceConfig = function() {
    return {
        inline: true,
        visual_table_class: 'ipTableManagement',
        //directionality : 'ltr', //TODO according to the current language
        plugins: "advlist, paste, link, table",
        entity_encoding : "raw",
        menubar: false,
        statusbar: false,
        toolbar1: 'bold italic alignleft aligncenter alignright styleselect removeformat table',
        toolbar2: 'link bullist numlist outdent indent subscript superscript undo redo',
        valid_elements : "@[class|style],table,tbody,tr,td,th,strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li",
        paste_word_valid_elements: "table,tbody,tr,td,th,strong,em,br,sup,sub,p,span,b,u,i,a,ul,ol,li",
        style_formats : [
            {title : 'Note', inline : 'span', classes : 'note'},
            {title : 'Button', inline : 'span', classes : 'button'}
        ],
        forced_root_block : "p",

        document_base_url : ip.baseUrl,
        remove_script_host : false,
        relative_urls : false,

        file_browser_callback: function(field_name, url, type, win) {
            var $input = $('#' + field_name);
            var $dialog = $input.closest('.mce-window');
            $('#mce-modal-block, .mce-tinymce-inline').addClass('hidden');
            $dialog.addClass('hidden');

            ipBrowseLink(function(link) {
                $('#mce-modal-block, .mce-tinymce-inline').removeClass('hidden');
                $dialog.removeClass('hidden');
                $input.val(link);
            })
        },

        paste_preprocess: function(pl, o) {
            ipTinyMceConfigPastePreprocess(pl, o, new Array('caption', 'signature', 'note', 'button'));
        }
    }
};
