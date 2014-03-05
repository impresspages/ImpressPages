/**
 * @package ImpressPages
 *
 */
console.log('load');

(function($) {
    "use strict";

    var methods = {

        init : function(options) {

            if (typeof(options) === 'undefined') {
                options = {};
            }

            return this.each(function() {

                var $this = $(this);
                var $textarea = $this.find('textarea');


                var data = $this.data('ipFormRichText');
                if (!data) {

                    $this.data('ipFormRichText', {initialized: 1});
                    var customTinyMceConfig = ipTinyMceConfig();
                    customTinyMceConfig.inline = false;
                    $textarea.tinymce(customTinyMceConfig);

                }
            });
        }
    };


    $.fn.ipFormRichtext = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormRepositoryFile');
        }

    };

    $('.ipsModuleForm .type-richtext').ipFormRichtext();

})(ip.jQuery);




