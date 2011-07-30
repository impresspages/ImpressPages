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
    this.blockSaveFinish = blockSaveFinish;
    


    
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
            
            $('.ipPageSave').bind('click', saveStart);
            $('.ipPagePublish').bind('click', publish);
            
            $('#ipRevisionSelect').bind('change', selectRevision);
        }
        
    }


    function saveStart(event){

    	//$('.ipBlock').bind('progress', saveProgress);

    	$(this).data('ipContentManagement', {pendingBlocks : $('.ipBlock').length});
    	console.log('Steps: ' + this.pendingBlocks );
    	$(this).bind('saveFinish.ipBlock', function(event){console.log('AAA'); this.blockSaveFinish(event);});
    	
    	$('.ipBlock').ipBlock('save');
    	
    	
    }

    function blockSaveFinish (event) {
    	this.pendingBlocks--;
    	console.log('left ' + this.pendingBlocks );
    	
    }
    
    
    function publish(event){
    	console.log('publish');
    }

//
//    function saveProgress(event, progress){
//    	console.log('save progress ' + progress);
//    }   
    
    function selectRevision(event){
    	document.location = $(event.currentTarget).val(); 
    }
    
}


