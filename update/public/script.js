"use strict";

if (document.images) {
    var preload_image = new Image(); 
    preload_image.src="public/cms_button_hover.gif"; 
}


$(function() {
    getStatus();
});



var getStatus = function() {
    $('#content').html();
    $('#content').append($('.noDisplay .loading').clone());
    $.ajax({
        type: 'POST',
        url: '?controller=Update&action=getStatus',
        success: successResponse
    });
};


var successResponse = function(response) {
    if (response && response.html) {
        $('#content').html(response.html);
    }
};

