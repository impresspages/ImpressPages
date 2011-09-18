/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */



(function($) {

    var methods = {
        init : function(options) {

            return this.each(function() {
                
                var $this = $(this);
                var defaultPicture = ipModuleDir + 'developer/upload/img/empty_picture.png';
                
                
                var data = $this.data('ipUploadPicture');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    var curPicture;
                    if (options.curPicture) {
                        curPicture = options.curPicture;
                    } else {
                        curPicture = defaultPicture;
                    }
                        
                    
                    
                    $this.data('ipUploadPicture', {
                        crop : options.crop, //allow to crop? true / false
                        curPicture : curPicture,
                        defaultPicture : defaultPicture,
                        aspectRatio : options.aspectRatio //eg 1024/768
                    }); 
                    
                    $this.html('<div style="border: 1px red solid; width: 100%; height: 200px; overflow: hidden;"><div id="ipUploadButton" style="position: absolute; z-index: 100;">Upload new</div><div class="ipUploadDragContainer"><img class="preview" src="' + curPicture + '" alt="picture"/></div></div>');
                    
                    
                    //uploaded new photo and loaded. Reinit drag container
                    $this.find('.preview').load(function() { 
                        var picture = $(this);
                        container = $(this).parent().parent();
                        dragContainer = $(this).parent();
                        
                        containerAspectRatio = container.width() / container.height();
                        pictureAspectRatio = picture.width() / picture.height();
                        console.log(containerAspectRatio + ' ' + pictureAspectRatio);
                        if (containerAspectRatio > pictureAspectRatio) {
                            picture.height('auto');
                            picture.width(container.width());
                        } else {
                            picture.width('auto');
                            picture.height(container.height());
                        }
                        
                        

                        
                        marginVertical = picture.height() - container.height();
                        if (marginVertical < 0) {
                            marginVertical = 0;
                        }

                        dragContainer.css('margin-top', -marginVertical);
                        dragContainer.css('margin-bottom', -marginVertical);
                        dragContainer.height(container.height() + marginVertical*2);
                        picture.css('top', marginVertical/2);
                        
                        
                        marginHorizontal = picture.width() - container.width();
                        if (marginHorizontal < 0) {
                            marginHorizontal = 0;
                        }

                        dragContainer.css('margin-left', -marginHorizontal);
                        dragContainer.css('margin-right', -marginHorizontal);
                        dragContainer.width(container.width() + marginHorizontal*2);
                        picture.css('left', marginHorizontal/2);
                        
                        
                    });
                    
                    $this.find('.preview').draggable({ containment: "parent" });
                    
                    var uploader = new plupload.Uploader( {
                        runtimes : 'gears,html5,flash,silverlight,browserplus',
                        browse_button : 'ipUploadButton',
                        container : 'ipUploadButton',
                        max_file_size : '100mb',            
                        url : ipBaseUrl, //website root (available globaly in ImpressPages environment)
                        multipart_params : {
                            g : 'developer',
                            m : 'upload',
                            a : 'upload'
                        },
                        
                        
                        flash_swf_url : ipBaseUrl + ipLibraryDir + 'plupload/js/plupload.flash.swf',
                        silverlight_xap_url : ipBaseUrl + ipLibraryDir + '/plupload/js/plupload.silverlight.xap'
                    });

                    uploader.bind('Init', function(up, params) {
                    });

//                    $('#uploadfiles').click(function(e) {
//                        uploader.start();
//                        e.preventDefault();
//                    });
                    
                    uploader.init();

                    uploader.bind('FilesAdded', function(up, files) {
                        
                        $.each(files, function(i, file) {
                            console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
                        });
                        up.refresh(); // Reposition Flash/Silverlight
                        up.start();
                    });
//
                    uploader.bind('UploadProgress', function(up, file) {
                        //console.log(file);
                        //$('#' + file.id + " b").html(file.percent + "%");
                    });

                    uploader.bind('Error', function(up, err) {
                        console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
                        up.refresh(); // Reposition Flash/Silverlight
                    });

                    
                    
                    
                    
                    
                    uploader.bind('FileUploaded', function(up, file, response) {
                        var answer = jQuery.parseJSON(response.response);
                        var data = $this.data('ipUploadPicture');
                        data.curPicture = answer.fileName;
                        $this.data('ipUploadPicture', data);
                        $this.find('.preview').attr('src', ipBaseUrl + answer.fileName);
                    });

                    
                }                
            });
            
            
            
            
        },
        
        getCurPicture : function () {            
            var curPicture = this.data('ipUploadPicture').curPicture;
            console.log('get cur picture ' + curPicture);
            if (curPicture == this.data('ipUploadPicture').defaultPicture) {
                return false;
            } else {
                return curPicture;
            }
        }
        

        

    
        
        
    };
    

    $.fn.ipUploadPicture = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadPicture');
        }


    };
    
   

})(jQuery);