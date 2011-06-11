
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
        
        $( ".ipBlockSelector" ).sortable({
            connectWith: ".ipBlockSelector",
            revert: true
        });        
        

        
        $( ".ipWidgetAddSelector" ).draggable({ connectToSortable: ".ipBlockSelector", revert: 'invalid', helper: "clone"   });
        
    }
    
}