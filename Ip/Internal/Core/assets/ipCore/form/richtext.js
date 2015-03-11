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
                    $this.data('ipFormRichText', 1);
                    if (isPublic($this)) {
                        if (typeof(ipTinyMceConfigPublic) == 'undefined') {
                            //Wait for TinyMCE config to load
                            var loadInterval = setInterval(function () {
                                if (typeof(ipTinyMceConfigPublic) == 'undefined') {
                                    return; //Wait for TinyMCE config to load
                                }
                                clearInterval(loadInterval);
                                initTinyMCE($this, ipTinyMceConfigPublic());
                            }, 300);

                        } else {
                            initTinyMCE($this, ipTinyMceConfigPublic());
                        }
                    } else {
                        //the only reliable way to wait till TinyMCE loads is to periodically check if it has been loaded
                        if (typeof(ipTinyMceConfig) == 'undefined') {
                            //Wait for TinyMCE config to load
                            var loadInterval = setInterval(function () {
                                if (typeof(ipTinyMceConfig) == 'undefined') {
                                    return; //Wait for TinyMCE config to load
                                }
                                clearInterval(loadInterval);
                                var customTinyMceConfig = ipTinyMceConfig();
                                customTinyMceConfig.inline = false;
                                initTinyMCE($this, customTinyMceConfig);
                            }, 300);

                        } else {
                            var customTinyMceConfig = ipTinyMceConfig();
                            customTinyMceConfig.inline = false;
                            initTinyMCE($this, customTinyMceConfig);
                        }
                    }
                }
            });
        }


    };

    var isPublic = function ($field) {
        var $form = $field.closest('form');
        return $form.hasClass('ipsModuleFormPublic');
    };

    var initTinyMCE = function ($field, config) {
        var $this = $field;
        var $textarea = $this.find('textarea');
        $this.data('ipFormRichText', {initialized: 1});
        $textarea.tinymce(config);
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




