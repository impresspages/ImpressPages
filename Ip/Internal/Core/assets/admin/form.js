/**
 * @package ImpressPages
 *
 */

// defining global variables
var ipModuleFormAdmin;

(function($){
    "use strict";


    ipModuleFormAdmin = new function () {
        this.init = function () {
            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormAdmin .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/plupload/plupload.full.js') + '"></script>'));
            }

            if ($('.ipsModuleFormAdmin .ipsColorPicker').length && !$.fn.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/spectrum/spectrum.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/admin/spectrum/spectrum.css') + '" type="text/css" />');
            }

            $('.ipsModuleFormAdmin .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormAdmin .ipsRepositoryFileContainer').ipFormRepositoryFile();
            $('.ipsModuleFormAdmin .type-richtext').ipFormRichtext();
            $('.ipsModuleFormAdmin .type-url').ipFormUrl();
            $('.ipsModuleFormAdmin .type-color').ipFormColor();


            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormAdmin').each(function(){
                var $form = $(this);
                if($form.find(":submit").length==0) {
                    $form.append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                }
            });

        };
    };
})(ip.jQuery);

