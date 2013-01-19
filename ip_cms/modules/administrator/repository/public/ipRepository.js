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
    $popup.css('top', $(document).scrollTop() + 'px');
    $popup.data('originalTopFrameRows', top.document.getElementById('adminFrameset').rows);
    top.document.getElementById('adminFrameset').rows = "0px,*";
    //$popup.dialog({modal: true, width: 853, height: 450, top: 50, zIndex: 99000});


    //initialize first tab
    $popup.find('#ipModRepositoryTabUpload').ipRepositoryUploader();


    //initialize other tabs on first use
    $popup.find('.tabs').tabs({
        activate: function( event, ui ) {
            var tabHref = ui.newTab.find('a').attr('href');
            switch(tabHref) {
                case '#ipModRepositoryTabRecent':
                    $popup.find('#ipModRepositoryTabRecent').ipRepositoryRecent();
                    break;
                case '#ipModRepositoryTabBuy':
                    $popup.find('#ipModRepositoryTabBuy').ipRepositoryBuy();
                    break;
            }
        }
    });

    //$popup.find('.tabs').on( "tabsactivate", function( event, ui ) {console.log(ui.newTab);} );


    $popup.bind('ipModRepository.confirm', function(e, files) {
        $(this).trigger('ipRepository.filesSelected', [files]);
        $(this).trigger('ipModRepository.close');
    });

    $popup.bind('ipModRepository.cancel', function(e) {
        $(this).trigger('ipModRepository.close');
    });

    $popup.bind('ipModRepository.close', function(e) {
        top.document.getElementById('adminFrameset').rows = $(this).data('originalTopFrameRows');
        $(document).off('keyup', ipRepositoryESC);
        $('.ipModRepositoryPopup').remove();
        $('body').removeClass('stopScrolling');
    })

    $popup.find('.ipaClose').hover(function(){$(this).addClass('ui-state-hover');}, function(){$(this).removeClass('ui-state-hover');});

    $popup.find('.ipaClose').click(function(e){$(this).trigger('ipModRepository.cancel');  e.preventDefault();});

    $(document).on('keyup', ipRepositoryESC);

    $('body').addClass('stopScrolling');

    //$popup.bind('dialogclose', function(){$('.ipModRepositoryPopup').remove(); $('body').removeClass('stopScrolling')});

    return $popup;

    function browserPopupHtmlResponse(response) {
    }



};


var ipRepositoryESC = function(e) {
    var $popup = $('.ipModRepositoryPopup');
    if (e.keyCode == 27) {
        $popup.trigger('ipModRepository.cancel');
    }
}
