/**
 * @package ImpressPages
 *
 */

// defining global variables
var ipModuleForm;

(function($){
    "use strict";

    $(document).ready(function () {
        ipModuleForm.init();
    });

    ipModuleForm = new function () {
        this.init = function () {
            //if interactive file upload input found, load file upload javascript
            if ($('.ipsModuleForm .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/js/plupload/plupload.full.js') + '"></script>'));
            }


            if ($('.ipsModuleForm .ipsFileContainer').length && !ip.jQuery().ipFormFile) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/form/file.js') + '"></script>'));
            }

            if ($('.ipsModuleForm .ipsRepositoryFileContainer').length && !ip.jQuery().ipFormRepositoryFile) {
                $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/form/repositoryFile.js') + '"></script>'));
            }

            if ($('.ipsModuleForm .ipsColorPicker').length && !ip.jQuery().spectrum) {
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/js/spectrum/spectrum.min.js') + '"></script>');
                $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Internal/Core/assets/form/color.js') + '"></script>');
                $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Internal/Core/assets/js/spectrum/spectrum.css') + '" type="text/css" />');
            }


            if (ip.jQuery().ipFormFile) {
                //if ipFormFile is already loaded
                $('.ipsModuleForm .ipsFileContainer').ipFormFile();
            } else {
                //ipFormFile JS will initialize itself
            }


            if (ip.jQuery().ipFormRepositoryFile) {
                //if ipFormRepositoryFile is already loaded
                $('.ipsModuleForm .ipsRepositoryFileContainer').ipFormRepositoryFile();
            } else {
                //ipFormRepositoryFile JS will initialize itself
            }

        };
    };
})(ip.jQuery);
