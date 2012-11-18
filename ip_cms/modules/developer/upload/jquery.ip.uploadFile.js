/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
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
                
                var data = $this.data('ipUploadFile');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                
                    if (typeof options.maxFileSize == 'undefined') {
                        options.maxFileSize = '10000mb';
                    }
                    if (typeof options.filterExtensions == 'undefined') {
                        options.filterExtensions = null 
                    }


                        
                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;
                    
                    $this.data('ipUploadFile', {
                        maxFileSize : options.maxFileSize,
                        filterExtensions : options.filterExtensions,
                        uniqueId : uniqueId
                        
                    }); 
                    
                    var data = Object();
                    data.g = 'developer';
                    data.m = 'upload';
                    data.a = 'getFileContainerHtml';
                    
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
        

        
        _containerHtmlResponse : function (response) {
            var $this = this;
            
            if (response.status != 'success') {
                return;
            }
            
            $this.html(response.html);
            var data = $this.data('ipUploadFile');
            

            $this.find('.ipUploadBrowseButton').attr('id', 'ipUploadButton_' + data.uniqueId);


            var callback = function(urls) {
                if (urls instanceof Array) {
                    for (var i in urls)
                    {
                        var file = urls[i].replace(ip.baseUrl, '');
                        $this.ipUploadFile('_uploadedNewFile', file);
                    }

                } else {
                    var file = urls.replace(ip.baseUrl, '');
                    $this.ipUploadFile('_uploadedNewFile', file);
                }

            }

            ipModuleRepositoryFileBrowser(callback);


        },
        
        _uploadedNewFile : function (file) {
            var $this = $(this);

            $this.trigger('fileUploaded.ipUploadFile', [file]);
        }
        

        
    };
    

    $.fn.ipUploadFile = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadFile');
        }


    };
    
   

})(jQuery);