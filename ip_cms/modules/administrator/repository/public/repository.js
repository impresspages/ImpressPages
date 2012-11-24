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
    $popup.find('.tabs').tabs();


    $popup.find('#ipModRepositoryTabUpload').ipRepositoryUploader({returnFunction: function(){}});

    $popup.find('#ipModRepositoryTabRecent').ipRepositoryRecent({returnFunction: function(){}});


    $popup.bind('ipModRepository.confirm', function(e, files) {$(this).trigger('ipRepository.filesSelected', [files]);});


    $('body').addClass('stopScrolling');
    $popup.bind('dialogclose', function(){$('.ipModRepositoryPopup').remove(); $('body').removeClass('stopScrolling')});

    return $popup;

    function browserPopupHtmlResponse(response) {
    }



};
