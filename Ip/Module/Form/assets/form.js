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
        alert($('.ipsModuleForm .ipsFileContainer').length );
        //if interactive file upload input found, load file upload javascript
        if ($('.ipsModuleForm .ipsFileContainer').length && (typeof(plupload) === "undefined")) {
            //TODOX new path
            alert(ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.full.js');
            $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.full.js"></script>'));
            $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.moduleDir + 'developer/form/public/file.js"></script>'));
        }

        if ($('.ipsModuleForm .ipmType-color').length && !jQuery().spectrum) {
            //TODOX new path
            $('body').append('<script type="text/javascript" src="' + ip.baseUrl + ip.libraryDir + 'js/spectrum/spectrum.min.js"></script>');
            $('body').append('<script type="text/javascript" src="' + ip.baseUrl + ip.moduleDir + 'developer/form/public/color.js"></script>');
            $('head').append('<link rel="stylesheet" href="' + ip.baseUrl + ip.libraryDir + 'js/spectrum/spectrum.css" type="text/css" />');
        }

    };
};
