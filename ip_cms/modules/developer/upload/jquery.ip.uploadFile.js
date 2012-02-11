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
                
                var data = $this.data('ipUploadFile');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                
                    if (typeof options.maxFileSize == 'undefined') {
                        options.maxFileSize = '10000mb';
                    }

                        
                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;
                    
                    $this.data('ipUploadFile', {
                        maxFileSize : options.maxFileSize,
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
            $this = this;
            
            if (response.status != 'success') {
                return;
            }
            
            $this.html(response.html);
            var data = $this.data('ipUploadFile');
            

            $this.find('.ipUploadBrowseButton').attr('id', 'ipUploadButton_' + data.uniqueId);
            
            
            var uploader = new plupload.Uploader( {
                runtimes : 'gears,html5,flash,silverlight,browserplus',
                browse_button : 'ipUploadButton_' + data.uniqueId,
                max_file_size : data.maxFileSize,
                url : ip.baseUrl, //website root (available globaly in ImpressPages environment)
                multipart_params : {
                    g : 'developer',
                    m : 'upload',
                    a : 'upload'
                },
                
                
                flash_swf_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.flash.swf',
                silverlight_xap_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.silverlight.xap'
            });

            
            uploader.bind('Init', function(up, params) {
            });
            
            
            uploader.init();

            
            uploader.bind('FilesAdded', function(up, files) {
                
                $.each(files, function(i, file) {
                    $this.trigger('fileAdded.ipUploadFile', file);
                    console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
                });
                up.refresh(); // Reposition Flash/Silverlight
                up.start();
            });

            uploader.bind('UploadProgress', function(up, file) {
                $this.trigger('uploadProgress.ipUploadFile', file);
                //$('#' + file.id + " b").html(file.percent + "%");
            });

            uploader.bind('Error', function(up, err) {
                console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
                var errorMessage = err.message + (err.file ? " \"" + err.file.name + "\"" : "");
                $this.trigger('error.ipUploadFile', errorMessage);
                up.refresh(); // Reposition Flash/Silverlight
            });
            
            uploader.bind('FileUploaded', function(up, file, response) {
                $this.ipUploadFile('_uploadedNewFile', up, file, response);
            });

        },
        
        
        _uploadedNewFile : function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            
            if (answer.error) {
                $this.trigger('error.ipUploadFile', answer.error.message);
            } else {
                var data = $this.data('ipUploadFile');
                $this.data('ipUploadFile', data);
                $this.trigger('fileUploaded.ipUploadFile', [answer.fileName]);
            }
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