/**
 * TinyMCE configuration for admin form fields. The ones that appear in administration panel.
 * https://www.impresspages.org/docs/tinymce
 */


ipTinyMceConfig = function () {
    return {
        inline: true,
        skin: 'impresspages',
        visual_table_class: 'ipTableManagement',
        directionality : ip.languageTextDirection,
        plugins: "advlist, paste, link, table, colorpicker, textcolor, alignrollup, anchor, autolink",
        entity_encoding: "raw",
        menubar: false,
        statusbar: false,
        toolbar1: 'bold italic alignrollup styleselect removeformat table, undo redo',
        toolbar2: 'link bullist numlist outdent indent subscript superscript forecolor backcolor',
        valid_elements: "@[class|style],table[border],tbody,tr[rowspan],td[colspan|rowspan],th[colspan],strong,em,br,sup,sub,p,span,b,u,i,a[name|href|target|title],ul,ol,li,h1,h2,h3,h4,h5,h6",
        paste_word_valid_elements: "table,tbody,tr,td,th,strong,em,br,sup,sub,p,span,b,u,i,a,ul,ol,li",
        style_formats: [
            {title: 'Quote', inline: 'span', classes: 'quote'},
            {title: 'Note', inline: 'span', classes: 'note'},
            {title: 'Button', inline: 'span', classes: 'button'}
        ],
        forced_root_block: "p",
        gecko_spellcheck: true,
        document_base_url: ip.baseUrl,
        remove_script_host: false,
        relative_urls: false,

        allow_script_urls: true,

        file_browser_callback: function (field_name, url, type, win) {
            var $input = $('#' + field_name);
            var $dialog = $input.closest('.mce-window');
            $('#mce-modal-block, .mce-tinymce-inline').addClass('hidden');
            $dialog.addClass('hidden');

            ipBrowseLink(function (link) {
                $('#mce-modal-block, .mce-tinymce-inline').removeClass('hidden');
                $dialog.removeClass('hidden');
                $input.val(link);
            })
        },

        paste_preprocess: function (pl, o) {
            var validClasses = [];
            var allFormats = ipTinyMceConfig().style_formats;
            $.each(allFormats, function (key, value) {
                if (value.classes) {
                    validClasses.push(value.classes);
                }
            });
            ipTinyMceConfigPastePreprocess(pl, o, validClasses);
        }
    }

};
