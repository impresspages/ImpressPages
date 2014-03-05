/**
 * @package ImpressPages
 *
 */

// defining global variables
var ipModuleFormAdmin;

(function($){
    "use strict";

    $(document).ready(function () {
        ipModuleFormAdmin.init();
    });

    ipModuleFormAdmin = new function () {
        this.init = function () {
            //TODOX on some servers files are loaded in random order. Problem when plupload and file are loaded at the same time. Or color and spectrum. #loadFormFilesBetter

            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleFormAdmin .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/plupload/plupload.full.js') + '"></script>'));
            }




            if ($('.ipsModuleFormAdmin .ipsColorPicker').length && !$.spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/spectrum/spectrum.min.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/admin/spectrum/spectrum.css') + '" type="text/css" />');
            }


            if ($.fn.ipFormFile) {
                //if ipFormFile is already loaded
                $('.ipsModuleFormAdmin .ipsFileContainer').ipFormFile();
            } else {
                //ipFormFile JS will initialize itself
            }


            if ($.fn.ipFormRepositoryFile) {
                //if ipFormRepositoryFile is already loaded
                $('.ipsModuleFormAdmin .ipsRepositoryFileContainer').ipFormRepositoryFile();
            } else {
                //ipFormRepositoryFile JS will initialize itself
            }



            if ($.fn.ipFormRichtext) {
                //if ipFormFile is already loaded
                $('.ipsModuleFormAdmin .type-richtext').ipFormRichtext();
            } else {
                //ipFormRichtext JS will initialize itself
            }


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



//            if ($('.ipsModuleFormAdmin .ipsFileContainer').length && !$.ipFormFile) {
//                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/file.js') + '"></script>'));
//            }
//
//if ($('.ipsModuleFormAdmin .type-richtext').length && !$.ipFormUrl) {
//    $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/richtext.js') + '"></script>'));
//}
//
//
//
//            if ($('.ipsModuleFormAdmin .ipsRepositoryFileContainer').length && !$.ipFormRepositoryFile) {
//                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/repositoryFile.js') + '"></script>'));
//            }
//
//            if ($('.ipsModuleFormAdmin .type-url').length && !$.ipFormUrl) {
//                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/url.js') + '"></script>'));
//            }
//$('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/admin/form/color.js') + '"></script>');
