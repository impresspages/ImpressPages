"use strict";




$(function() {
    $('#content').delegate('.actProceed', 'click', proceed);
    $('#content').delegate('.actResetLock', 'click', resetLock);
    
    getStatus();
    
    
    if (document.images) {
        var preload_image = new Image(); 
        preload_image.src="public/cms_button_hover.gif"; 
    }    
});



var getStatus = function() {
    loading();
    $.ajax({
        type: 'POST',
        url: '?controller=Update&action=getStatus',
        success: successResponse,
        error: errorResponse
    });
};


var proceed = function(event) {
    loading();
    $.ajax({
        type: 'POST',
        url: '?controller=Update&action=proceed',
        success: successResponse,
        error: errorResponse
    });
}

var resetLock = function(event) {
    loading();
    $.ajax({
        type: 'POST',
        url: '?controller=Update&action=resetLock',
        success: successResponse,
        error: errorResponse
    });
}



var successResponse = function(response) {
    if (response && response.html) {
        $('#content').html(response.html);
    }
    
    if (response && response.action) {
        switch(response.action) {
            case 'reload':
                var url = parent.window.location.href.split('#');
                window.location = url[0];
            break;
            default:
                if (!response.html) {
                    //this is an error. Response has no reload option nor HTML to display. Let's put the answer itself
                    var $paragraph = $('<p>Error</p>');
                    $paragraph.text(JSON.stringify(response));

                    $('#content').html($paragraph);
                }
            break;

        }
    }
    
};

var errorResponse = function(response) {
    console.log('error');
    console.log(response);
    if (response.responseText) {
        $('#content').text(response.responseText);
    }

};


var loading = function() {
    $('#content').html('');
    $('#content').append($('.noDisplay .loading').clone());
}
