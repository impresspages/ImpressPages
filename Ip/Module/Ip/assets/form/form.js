/**
 * @package ImpressPages

 *
 */


$(document).ready(function () {
    "use strict";
    ipModuleForm.init();
});


var ipModuleForm = new function () {
    "use strict";
    this.init = function () {
        //if interactive file upload input found, load file upload javascript
        if ($('.ipsModuleForm .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
            $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.full.js') + '"></script>'));
        }

        //TODOX check if not loaded
        $('body').append($('<script type="text/javascript" src="' + ipFileUrl('Ip/Module/Ip/assets/form/file.js') + '"></script>'));

        if ($('.ipsModuleForm .ipmType-color').length && !jQuery().spectrum) {
            $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Module/Ip/assets/js/spectrum/spectrum.min.js') + '"></script>');
            $('body').append('<script type="text/javascript" src="' + ipFileUrl('Ip/Module/Ip/assets/form/color.js') + '"></script>');
            $('head').append('<link rel="stylesheet" href="' + ipFileUrl('Ip/Module/Ip/assets/js/spectrum/spectrum.css') + '" type="text/css" />');
        }

    };
};
