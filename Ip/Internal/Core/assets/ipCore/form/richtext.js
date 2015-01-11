/**
 * @package ImpressPages
 *
 */

(function ($) {
    "use strict";

    var methods = {

        init: function (options) {


            return this.each(function () {

                var $this = $(this);
                var data = $this.data('ipFormRichText');
                if (!data) {
                    //the only reliable way to wait till TinyMCE loads is to periodically check if it has been loaded
                    var loadInterval = setInterval(function () {
                        initTinyMCE($this, loadInterval);
                    }, 300);
                }
            });
        }


    };

    var initTinyMCE = function ($field, loadInterval) {
        if (typeof(ipTinyMceConfig) == 'undefined') {
            //Wait for TinyMCE config to load
            return;

        }
        var $this = $field;

        clearInterval(loadInterval);

        var $textarea = $this.find('textarea');
        $this.data('ipFormRichText', {initialized: 1});
        var customTinyMceConfig = ipTinyMceConfig();
        customTinyMceConfig.inline = false;
        $textarea.tinymce(customTinyMceConfig);
    };


    $.fn.ipFormRichtext = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormRepositoryFile');
        }

    };

    $('.ipsModuleFormAdmin .type-richText').ipFormRichtext();

})(jQuery);




