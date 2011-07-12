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
        options.widgetControllsHtml = response.widgetControllsHtml;
        
        $('.ipBlock').ipBlock(options);
        $('.ipBlock').last().find('.ipWidget').first().ipWidget('manage');
        
        $('.ipWidgetButtonSelector').ipWidgetButton();
        
        $('.ipPageSave').bind('click', ipPageSave);
        
    }
    
}


function ipPageSave(event){
	$('.ipBlock').bind('progress', saveProgress);
	$('.ipBlock').ipBlock('save');
}


function saveProgress(event){
	console.log('save progress');
}


//function ipWidgetSave(event) {
//	console.log(event);
//    var parameters = new Object();
//    parameters['test'] = 'testvalue'; 
//    parameters['test2'] = 'testvalue2';
//	
//	$(event.currentTarget).trigger('ipSave', [parameters]);
//
//}