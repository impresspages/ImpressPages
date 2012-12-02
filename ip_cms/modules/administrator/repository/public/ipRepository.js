/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";

var ipRepository = function () {
    if ($('.ipModRepositoryPopup').length) {
        return; //repository window is already open. Do nothing.
    }


    $('body').append(ipRepositoryHtml);
    var $popup = $('.ipModRepositoryPopup');
    $popup.dialog({modal: true, width: 800, height: 450, top: 50});
    console.log('gtest');
    $popup.find('.tabs').tabs({
        activate: function( event, ui ) {
            console.log('gtest');
            console.log(ui.newTab);
        }
    });

    //$popup.find('.tabs').on( "tabsactivate", function( event, ui ) {console.log(ui.newTab);} );

    $popup.find('#ipModRepositoryTabUpload').ipRepositoryUploader();

    $popup.find('#ipModRepositoryTabRecent').ipRepositoryRecent();


    $popup.bind('ipModRepository.confirm', function(e, files) {$(this).trigger('ipRepository.filesSelected', [files]); $(this).dialog('close');});

    $popup.bind('ipModRepository.cancel', function(e) {$(this).dialog('close');});

    $('body').addClass('stopScrolling');

    $popup.bind('dialogclose', function(){$('.ipModRepositoryPopup').remove(); $('body').removeClass('stopScrolling')});

    return $popup;

    function browserPopupHtmlResponse(response) {
    }



};
