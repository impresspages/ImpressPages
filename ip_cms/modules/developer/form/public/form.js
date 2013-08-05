/**
 * @package ImpressPages

 *
 */

"use strict";

$(document).ready(function() {

    //if interactive file upload input found, load file upload javascript
    if ($('.ipsModuleForm .ipmFileContainer').length) {
        $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.full.js"></script>'));
        $('body').append($('<script type="text/javascript" src="' + ip.baseUrl + ip.moduleDir + 'developer/form/public/file.js"></script>'));
    }

    $.each($('.ipsModuleForm .ipmType-farbtastic'), function(){
        var $this = $(this);
        $this.find('.ipsFarbtasticPopup').farbtastic($this.find('.ipsControlInput'));
    });


});