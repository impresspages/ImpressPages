"use strict";

if (document.images) {
    var preload_image = new Image(); 
    preload_image.src="public/cms_button_hover.gif"; 
}


$(function() {
    getStatus();
    console.log('test');
});



var getStatus = function() {
 
    $.ajax({
        type: 'POST',
        url: '?controller=Update&action=getStatus',
        success: successResponse
      });
};


var successResponse = function(response) {
    console.log(response);
};