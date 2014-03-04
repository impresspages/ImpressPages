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


            if ($('.ipsModuleFormPublic .ipsFileContainer').length && !$.ipFormFile) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/form/file.js') + '"></script>'));
            }

            if ($('.ipsModuleFormPublic .ipsRepositoryFileContainer').length && !$.ipFormRepositoryFile) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/form/repositoryFile.js') + '"></script>'));
            }


            if ($('.ipsModuleFormPublic .type-richtext').length && !$.ipFormUrl) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/richtext.js') + '"></script>'));
            }

            if ($('.ipsModuleFormPublic .ipsColorPicker').length && !$.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.min.js') + '"></script>');
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/form/color.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/ipCore/spectrum/spectrum.css') + '" type="text/css" />');
            }


            if ($.ipFormFile) {
                //if ipFormFile is already loaded
                $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();
            } else {
                //ipFormFile JS will initialize itself
            }


            if ($.ipFormRepositoryFile) {
                //if ipFormRepositoryFile is already loaded
                $('.ipsModuleFormPublic .ipsRepositoryFileContainer').ipFormRepositoryFile();
            } else {
                //ipFormRepositoryFile JS will initialize itself
            }

            // adding dumb submit element for 'enter' to trigger form submit
            $('.ipsModuleFormPublic').each(function(){
                var $form = $(this);
                if($form.find(":submit").length==0) {
                    $form.append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                }
            });

        };
    };
})(jQuery);
