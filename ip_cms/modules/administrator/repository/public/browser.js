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
        var $elFinder = $popup.find('.ipmElFinder');

        $('#ipModRepositoryTabUpload').ipRepositoryFileContainer();



        var elf = $elFinder.elfinder({
            url : ip.baseUrl + ip.moduleDir + 'administrator/repository/elfinder/php/connector.php',  // connector URL (REQUIRED)
            commandsOptions : {
                getfile : {
                    multiple : true,
                    oncomplete : 'destroy'
                }
            },
            commands : [
                'upload', 'search', 'sort'
            ],
            resizable: false,
            ui : ['toolbar'],
            contextmenu : false,
            height: 330,
            getFileCallback: callback
        }).elfinder('instance');


        $elFinder.bind('upload', __ipModuleRepositoryFileBrowserDestroy);

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




/**
 * Destroys elfinder instance. Don't execute manually
 * @param event
 * @private
 */
function __ipModuleRepositoryFileBrowserDestroy(event) {
    alert('test');
    $('body').removeClass('stopScrolling');

}


function elFinderBrowser (field_name, url, type, win) {
    var elfinder_url = ip.baseUrl + ip.moduleDir + 'administrator/repository/public/elfinder/elfinder.html';    // use an absolute path!
    tinyMCE.activeEditor.windowManager.open({
        file: elfinder_url,
        title: 'elFinder 2.0',
        width: 900,
        height: 450,
        resizable: 'yes',
        inline: 'yes',    // This parameter only has an effect if you use the inlinepopups plugin!
        popup_css: false, // Disable TinyMCE's default popup CSS
        close_previous: 'no'
    }, {
        window: win,
        input: field_name
    });
    return false;
}