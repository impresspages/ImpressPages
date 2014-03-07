/**
 * @package ImpressPages
 *
 */



(function ($) {
    "use strict";

    $(document).ready(function () {
        $('body').ipContentManagement();

        //preinit TinyMCE. Without it edit focus doesn't work after adding a widget
        var $emptyDiv = $('<div contenteditable="true" style="display: none"></div>');
        $('body').append($emptyDiv);
        $emptyDiv.tinymce(ipTinyMceConfig());
        setTimeout(function(){$emptyDiv.remove()}, '10000');

    });
})(ip.jQuery);
