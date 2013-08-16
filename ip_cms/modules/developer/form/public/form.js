/**
 * @package ImpressPages

 *
 */

"use strict";

$(document).ready(function() {

    ipModuleForm.init();


});


var ipModuleForm = new function() {
    this.init = function() {
        //if interactive file upload input found, load file upload javascript
        if ($('.ipsModuleForm .ipmFileContainer').length && (typeof(plupload) === "undefined")) {
            $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.full.js"></script>'));
            $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.moduleDir + 'developer/form/public/file.js"></script>'));
        }

        if ($('.ipsModuleForm .ipmType-color').length && !jQuery().spectrum) {
            $('body').append('<script type="text/javascript" src="' + ip.baseUrl + ip.libraryDir + 'js/spectrum/spectrum.min.js"></script>');
            $('body').append('<script type="text/javascript" src="' + ip.baseUrl + ip.moduleDir + 'developer/form/public/color.js"></script>');
            $('head').append('<link rel="stylesheet" href="' + ip.baseUrl + ip.libraryDir + 'js/spectrum/spectrum.css" type="text/css" />');
        }

    }
};
