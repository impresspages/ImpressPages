/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";

function ipModuleRepositoryFileBrowser(callback) {


    function browserPopupHtmlResponse(response) {
        $('body').append(response.html);
        var $popup = $('.ipModRepositoryPopup');
        $popup.dialog({modal: true, width: 800, height: 450, top: 50});
        $popup.find('.tabs').tabs();


        $('#ipModRepositoryTabUpload').ipRepositoryUploader();

        $('#ipModRepositoryTabRecent').ipRepositoryRecent();


        $('body').addClass('stopScrolling');
        $popup.bind('dialogclose', function(){$('.ipModRepositoryPopup').remove(); $('body').removeClass('stopScrolling')});



        //$('body').addClass('stopScrolling');

    }

    $.ajax({
        type : 'POST',
        url : ip.baseUrl,
        data : {
            g: 'administrator',
            m: 'repository',
            a: 'browserPopupHtml'
        },
        success : browserPopupHtmlResponse,
        error : function(e, x) {
            alert(e.responseText);
            console.log(e);
            console.log(x);
        },
        dataType : 'json'
    });


}
