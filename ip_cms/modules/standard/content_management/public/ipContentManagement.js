/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

$(document).ready(function() {
    ipContentManagement = new ipContentManagement();
    
    ipContentManagement.init();
});

/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipContentManagement() {
    this.init = init;
    this.initResponse = initResponse;
    this.publish = publish;
    this.save = save;

    
    function init () {
        if ($("#ipControllPanelBg").length == 0) {
            var $controlsBgDiv = $('<div id="ipControllPanelBg" />');
            $('body').prepend($controlsBgDiv);
        }
        
        data = Object();
        data.g = 'standard';
        data.m = 'content_management';
        data.a = 'initManagementData';

        $.ajax({
            type : 'POST',
            url : document.location,
            data : data,
            context : this,
            success : initResponse,
            dataType : 'json'
        });
        
       
    }


    function initResponse(response) {
        if (response.status == 'success') {
            $('body').prepend(response.controlPanelHtml);
            
            var options = new Object;
            options.zoneName = ipZoneName;
            options.pageId = ipPageId;
            options.revisionId = ipRevisionId;
            options.widgetControlsHtml = response.widgetControlsHtml;
            
            $('.ipBlock').ipBlock(options);
            
            $('.ipWidgetButtonSelector').ipWidgetButton();
            
            $('.ipPageSave').bind('click', save);
            $('.ipPagePublish').bind('click', publish);
            
            $('#ipRevisionSelect').bind('change', selectRevision);
        }
        
    }


    function save(event){
    	console.log('PageSave');
    	$('.ipBlock').bind('progress', saveProgress);
    	$('.ipBlock').ipBlock('save');
    }

    function publish(event){
    	console.log('publish');
    }


    function saveProgress(event, progress){
    	console.log('save progress ' + progress);
    }   
    
    function selectRevision(event){
    	document.location = $(event.currentTarget).val(); 
    }
    
}


