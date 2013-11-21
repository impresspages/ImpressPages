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
            $('body').append($('<script type="text/javascript" src="' + Form_pluploadJsFile + '"></script>'));
            $('body').append($('<script type="text/javascript" src="' + Form_fileFieldJs + '"></script>'));
        }

        if ($('.ipsModuleForm .ipmType-color').length && !jQuery().spectrum) {
            $('body').append('<script type="text/javascript" src="' + Form_spectrumJsFile + '"></script>');
            $('body').append('<script type="text/javascript" src="' + Form_colorFieldJs + '"></script>');
            $('head').append('<link rel="stylesheet" href="' + Form_spectrumCssFile + '" type="text/css" />');
        }
    };
};
