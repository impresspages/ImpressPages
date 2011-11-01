/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

/**
 * 
 * Available options:
 * 
 * 
 * uploadHandler - link to PHP script that will accept uploads (not implemented)
 * 
 */


(function($) {

    var methods = {
        init : function(options) {

            return this.each(function() {
                var $this = $(this);
                
                var data = $this.data('ipUploadPicture');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                

                        
                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;
                    
                    $this.data('ipUploadPicture', {
                        
                    }); 
                    
                    var photoHeight = Math.round($this.width() / $this.data('ipUploadPicture').aspectRatio);
                    
                    
                    var data = Object();
                    data.g = 'developer';
                    data.m = 'upload';
                    data.a = 'getContainerHtml';
                    
                    $.ajax({
                        type : 'POST',
                        url : ip.baseUrl,
                        data : data,
                        context : $this,
                        success : methods._containerHtmlResponse,
                        dataType : 'json'
                    });

                }
            });

        },
        

        

        
        _uploadedNewPhoto : function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            var data = $this.data('ipUploadPicture');
            data.curPicture = answer.fileName;
            data.changed = true;
            $this.data('ipUploadPicture', data);
            $this.find('.ipUploadImage').attr('src', ip.baseUrl + answer.fileName);
        },
        

        
    };
    

    $.fn.ipUploadFile = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadPicture');
        }


    };
    
   

})(jQuery);