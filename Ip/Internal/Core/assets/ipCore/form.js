/**
 * @package ImpressPages
 *
 */

// defining global variables
var ipModuleFormPublic;

(function($){
    "use strict";

    $(document).ready(function () {
        ipModuleFormPublic.init();
    });

    ipModuleFormPublic = new function () {
        this.init = function () {
            //TODOX on some servers files are loaded in random order. Problem when plupload and file are loaded at the same time. Or color and spectrum.

            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormPublic .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.full.js') + '"></script>'));
            }


            if ($('.ipsModuleFormPublic .ipsColorPicker').length && !$.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.css') + '" type="text/css" />');
            }


            $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();
            $('.ipsModuleFormPublic .type-richtext').ipFormRichtext();
            $('.ipsModuleFormPublic .type-url').ipFormUrl();
            $('.ipsModuleFormPublic .type-color').ipFormColor();


            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormPublic').each(function(){
                var $form = $(this);
                if ($form.find(":submit").length==0) {
                    $form.append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                }
            });

        };
    };
})(jQuery);


