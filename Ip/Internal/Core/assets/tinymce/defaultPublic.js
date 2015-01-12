/**
 * TinyMCE configuration for public form fields. The ones that can be accessed by visitors of your website.
 * https://www.impresspages.org/docs/tinymce
 */

ipTinyMceConfigPublic = function () {
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

