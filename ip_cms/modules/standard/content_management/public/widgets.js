/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

$(document).ready(function() {
    ipInitManagement();
});


function ipInitManagement () {
    if ($("#ipControllPanelBg").length == 0) {
        var $controllsBgDiv = $('<div id="ipControllPanelBg" />');
        $('body').prepend($controllsBgDiv);
    }
    
    data = Object();
    data.g = 'standard';
    data.m = 'content_management';
    data.a = 'initManagementData';

    $.ajax({
        type : 'POST',
        url : ipBaseUrl,
        data : data,
        success : ipInitManagementResponse,
        dataType : 'json'
    });
    
   
}


function ipInitManagementResponse(response) {
    if (response.status == 'success') {
        $('body').prepend(response.controlPanelHtml);
        
        var options = new Object;
        options.zoneName = ipZoneName;
        options.pageId = ipPageId;
        options.revisionId = ipRevisionId;
        
        $('.ipBlock').ipBlock(options);
        

        $('.ipWidgetButtonSelector').ipWidgetButton();
        
        
        
        
    }
    
}