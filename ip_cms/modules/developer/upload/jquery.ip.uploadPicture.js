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
                    
                    $this.html('<div style="border: 1px red solid; width: 100%; height: 400px; overflow: hidden;"><div style="position: absolute; z-index: 100;"><em id="testContainer"><div style="cursor: pointer;" id="ipUploadButton">Upload new </div></em><div class="ipUploadLargerButton">Larger </div><div class="ipUploadSmallerButton">Smaller</div></div><div class="ipUploadDragContainer"><img class="preview" src="' + curPicture + '" alt="picture"/></div></div>');
                    
                    $this.find('.ipUploadLargerButton').click(function() {
                        $(this).trigger('pictureScaleUp.ipUploadPicture');
                    });
                    
                    $this.bind('pictureScaleUp.ipUploadPicture', function(e){
                        console.log('scale up');
                        var scaleFactor = 1.1;
                        
                        var picture = $(this).find('.preview');
                        var container = picture.parent().parent();
                        var dragContainer = picture.parent();                        
                        
                        var pictureCenterX = (dragContainer.width() / 2) - parseInt(picture.css('left'));
                        var pictureCenterXPercentage = pictureCenterX * 100 / picture.width(); 
                        
                        var pictureCenterY = (dragContainer.height() / 2) - parseInt(picture.css('top'));
                        var pictureCenterYPercentage = pictureCenterY * 100 / picture.height(); 
                        
                        picture.height('auto');
                        picture.width(picture.width() * scaleFactor);
                        
                        picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);

                    });
                    
                    $this.find('.ipUploadSmallerButton').click(function() {
                        $(this).trigger('pictureScaleDown.ipUploadPicture');
                    });
                    $this.bind('pictureScaleDown.ipUploadPicture', function(e){
                        console.log('scale down');
                        var scaleFactor = 1.1;
                        
                        var picture = $(this).find('.preview');
                        var container = picture.parent().parent();
                        var dragContainer = picture.parent();                        
                        
                        var pictureCenterX = (dragContainer.width() / 2) - parseInt(picture.css('left'));
                        var pictureCenterXPercentage = pictureCenterX * 100 / picture.width(); 
                        
                        var pictureCenterY = (dragContainer.height() / 2) - parseInt(picture.css('top'));
                        var pictureCenterYPercentage = pictureCenterY * 100 / picture.height(); 
                        
                        picture.height('auto');
                        picture.width(picture.width() / scaleFactor);
                        
                        if (picture.width() < container.width()) {
                            picture.height('auto');
                            picture.width(container.width());
                        }
                        if (picture.height() < container.height()) {
                            picture.width('auto');
                            picture.height(container.height());
                        }
                        picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
                        
                      
                    });
                    
                    
                    //uploaded new photo and loaded. Reinit drag container
                    $this.find('.preview').load(function() {
                        var picture = $(this);
                        var container = $(this).parent().parent();
                        var dragContainer = $(this).parent();
                        
                        containerAspectRatio = container.width() / container.height();
                        pictureAspectRatio = picture.width() / picture.height();
                        if (containerAspectRatio > pictureAspectRatio) {
                            picture.height('auto');
                            picture.width(container.width());
                        } else {
                            picture.width('auto');
                            picture.height(container.height());
                        }
                        picture.trigger('pictureResized.ipUploadPicture', [50, 50]);
                    });
                    
                    
                    $this.find('.preview').bind('pictureResized.ipUploadPicture', function(e, pictureCenterXPercentage, pictureCenterYPercentage) {
                        var picture = $(this);
                        var container = $(this).parent().parent();
                        var dragContainer = $(this).parent();
                        
                        var pictureCenterX = picture.width() * pictureCenterXPercentage / 100;
                        var pictureCenterY = picture.height() * pictureCenterYPercentage / 100;
                        
                        marginHorizontal = picture.width() - container.width();
                        if (marginHorizontal < 0) {
                            marginHorizontal = 0;
                        }

                        dragContainer.css('margin-left', -marginHorizontal);
                        dragContainer.css('margin-right', -marginHorizontal);
                        dragContainer.width(container.width() + marginHorizontal*2);
                        picture.css('left', dragContainer.width() / 2 - pictureCenterX);                          
                        if (parseInt(picture.css('left')) < 0){
                            picture.css('left', 0);
                        }
                        if (parseInt(picture.css('left')) > dragContainer.width() - picture.width()){
                            picture.css('left', dragContainer.width() - picture.width());
                        }
                        
                        
                        
                        marginVertical = picture.height() - container.height();
                        if (marginVertical < 0) {
                            marginVertical = 0;
                        }

                        dragContainer.css('margin-top', -marginVertical);
                        dragContainer.css('margin-bottom', -marginVertical);
                        dragContainer.height(container.height() + marginVertical*2);
                        picture.css('top', dragContainer.height() / 2 - pictureCenterY);
                        if (parseInt(picture.css('top')) < 0){
                            picture.css('top', 0);
                        }
                        if (parseInt(picture.css('top')) > dragContainer.height() - picture.height()){
                            picture.css('top', dragContainer.height() - picture.height());
                        }
                     
                        
                        
                    });
                    
                    
                    $this.find('.preview').draggable({ containment: "parent" });
                    
                    var uploader = new plupload.Uploader( {
                        runtimes : 'gears,html5,flash,silverlight,browserplus',
                        browse_button : 'ipUploadButton',
                        container : 'testContainer',
                        container : 'ipUploadButton',
                        max_file_size : '100mb',            
                        url : ipBaseUrl, //website root (available globaly in ImpressPages environment)
                        multipart_params : {
                            g : 'developer',
                            m : 'upload',
                            a : 'upload'
                        },
                        
                        
                        flash_swf_url : ipBaseUrl + ipLibraryDir + 'js/plupload/plupload.flash.swf',
                        silverlight_xap_url : ipBaseUrl + ipLibraryDir + 'js/plupload/plupload.silverlight.xap'
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