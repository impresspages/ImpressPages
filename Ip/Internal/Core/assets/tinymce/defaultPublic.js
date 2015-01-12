/**
 * TinyMCE configuration for public form fields. The ones that can be accessed by visitors of your website.
 * https://www.impresspages.org/docs/tinymce
 */

ipTinyMceConfigPublic = function () {

    return {
        directionality : ip.languageTextDirection,
        plugins: "advlist, paste, link, table, colorpicker, textcolor, anchor, alignrollup, autolink",
        entity_encoding: "raw",
        menubar: false,
        statusbar: false,
        toolbar1: 'bold italic alignrollup styleselect removeformat table, undo redo',
        toolbar2: 'link bullist numlist outdent indent subscript superscript forecolor backcolor',
        paste_word_valid_elements: "table,tbody,tr,td,th,strong,em,br,sup,sub,p,span,b,u,i,a,ul,ol,li",
        forced_root_block: "p",
        allow_script_urls: false,
        convert_urls: false
    };


    return {
        selector: "textarea",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    }

};

