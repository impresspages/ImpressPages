/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

/**
 * 
 * Available options:
 * 
 * backgroundPicture - defautl picture to be used when real picture is not uploaded
 * backgroundColor - 
 * picture - url to image to be cropped / resized
 * cropX1 - current cropping coordinates
 * cropY1 
 * cropX2
 * cropY2 * 
 * containerWidth - width of container (100% if not set)
 * containerHeight - height of container (the same as width if not set)
 * changeWidth - allow user to change container width
 * changeHeight - allow user to change container height
 * constrainProportions - update container parameters to constrain proportions on resize
 * maxContainerWidth - 
 * maxContainerHeight -
 * minContainerWidth
 * minContainerHeight
 * 
 * uploadHandler - link to PHP script that will accept uploads
 * 
 */


(function($) {

    var methods = {
        init : function(options) {

            return this.each(function() {
                
                var $this = $(this);
                
                var defaultPicture;
                if (options.defaultPicture) {
                    defaultPicture = options.defaultPicture;
                } else {
                    defaultPicture = ip.moduleDir + 'developer/upload/img/empty_picture.png';
                }
                
                
                
                
                var data = $this.data('ipUploadPicture');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    var curPicture;
                    if (options.picture) {
                        curPicture = options.picture;
                    } else {
                        curPicture = defaultPicture;
                    }
                    
                    if (options.width)
                    
                    if (options.aspectRatio && options.aspectRatio == 0) {
                        options.aspectRatio = null;
                    }
                        
                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;
                    
                    $this.data('ipUploadPicture', {
                        defaultPicture : defaultPicture,
                        changed : false,
                        curPicture : curPicture,
                        uniqueId : uniqueId,
                        defaultPicture : defaultPicture,
                        aspectRatio : options.aspectRatio //eg 1024/768
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
        
        
        _containerHtmlResponse : function (response) {
            $this = this;
            if (response.status != 'success') {
                return;
            }
            
            //'<div style="border: 1px black solid; width: 100%; height: ' + photoHeight + 'px; overflow: hidden;"><div style="position: absolute; z-index: 100;"><div id="ipUploadContainer_' + uniqueId + '"><div style="cursor: pointer;" id="ipUploadButton_' + uniqueId + '">Upload new </div></div><div class="ipUploadLargerButton">Larger </div><div class="ipUploadSmallerButton">Smaller</div></div><div class="ipUploadDragContainer"><img class="preview" src="' + curPicture + '" alt="picture"/></div></div>'
            $this.html(response.html);
            
            $this.find('ipUploadImage').attr('src', curPicture);
            
            
            $this.find('.ipUploadLargerButton').click(function() {
                $(this).trigger('pictureScaleUp.ipUploadPicture');
            });
            
            $this.bind('pictureScaleUp.ipUploadPicture', function(event) {
                $(this).ipUploadPicture('_scaleUp', event);
            });
            
            
            
            $this.find('.ipUploadSmallerButton').click(function() {
                $(this).trigger('pictureScaleDown.ipUploadPicture');
            });

            $this.bind('pictureScaleDown.ipUploadPicture', function(event) {
                $(this).ipUploadPicture('_scaleDown', event);
            });
            
            
            //uploaded new photo and loaded. Reinit drag container
            $this.find('.preview').load(function (){
                $this.ipUploadPicture('_pictureLoaded');
            });
            
            
            $this.find('.preview').bind('pictureResized.ipUploadPicture', function(event, pictureCenterXPercentage, pictureCenterYPercentage) {
                $this.ipUploadPicture('_pictureResized', event, pictureCenterXPercentage, pictureCenterYPercentage);
            });
            
            
            
            
            $this.find('.preview').draggable({ containment: "parent" });
            
            var uploader = new plupload.Uploader( {
                runtimes : 'gears,html5,flash,silverlight,browserplus',
                browse_button : 'ipUploadButton_' + uniqueId,
                container : 'ipUploadContainer_' + uniqueId,
                max_file_size : '100mb',
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

//            $('#uploadfiles').click(function(e) {
//                uploader.start();
//                e.preventDefault();
//            });
            
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
                $this.ipUploadPicture('_uploadedNewPhoto', up, file, response);
            });            
            
        },
        
        _uploadedNewPhoto : function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            var data = $this.data('ipUploadPicture');
            data.curPicture = answer.fileName;
            data.changed = true;
            $this.data('ipUploadPicture', data);
            $this.find('.preview').attr('src', ip.baseUrl + answer.fileName);
        },
        
        
        _pictureLoaded : function() {
            var $this = $(this);
            var $picture = $this.find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            
            if ($container.height() == 0 || $picture.height() == 0) {
                return; //to avoid division by zero.
            }
            
            containerAspectRatio = $container.width() / $container.height();
            pictureAspectRatio = $picture.width() / $picture.height();
            if (containerAspectRatio > pictureAspectRatio) {
                $picture.width($container.width());
                $picture.height('auto');
                $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
                
            } else {
                $picture.height($container.height());
                $picture.width('auto');
                $picture.width(Math.round($picture.width())); //set exact value made by automatic scale
            }
            $picture.trigger('pictureResized.ipUploadPicture', [50, 50]);
        },
        
        _pictureResized : function(e, pictureCenterXPercentage, pictureCenterYPercentage) {
            var $this = $(this);
            var $picture = $this.find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            
            var pictureCenterX = Math.round($picture.width() * pictureCenterXPercentage / 100);
            var pictureCenterY = Math.round($picture.height() * pictureCenterYPercentage / 100);
            marginHorizontal = $picture.width() - $container.width();
            if (marginHorizontal < 0) {
                marginHorizontal = 0;
            }

            $dragContainer.css('margin-left', -marginHorizontal);
            $dragContainer.css('margin-right', -marginHorizontal);
            $dragContainer.width($container.width() + marginHorizontal*2);
            $picture.css('left', $dragContainer.width() / 2 - pictureCenterX);                          
            if (parseInt($picture.css('left')) < 0){
                $picture.css('left', 0);
            }
            if (parseInt($picture.css('left')) > $dragContainer.width() - $picture.width()){
                $picture.css('left', $dragContainer.width() - $picture.width());
            }
            
            
            
            marginVertical = $picture.height() - $container.height();
            if (marginVertical < 0) {
                marginVertical = 0;
            }

            $dragContainer.css('margin-top', -marginVertical);
            $dragContainer.css('margin-bottom', -marginVertical);
            $dragContainer.height($container.height() + marginVertical*2);
            $picture.css('top', $dragContainer.height() / 2 - pictureCenterY);
            if (parseInt($picture.css('top')) < 0){
                $picture.css('top', 0);
            }
            if (parseInt($picture.css('top')) > $dragContainer.height() - $picture.height()){
                $picture.css('top', $dragContainer.height() - $picture.height());
            }
         

            
        },
        
        _uploadPicture : function(e){
            var scaleFactor = 1.1;
            
            var $picture = $(this).find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();                        
                
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.width(Math.round($picture.width() / scaleFactor));
            $picture.height('auto');//scale automatically
            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale

            
            if ($picture.width() < $container.width()) {
                $picture.width($container.width());
                $picture.height('auto');
                $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            }
            if ($picture.height() < $container.height()) {
                $picture.height($container.height());
                $picture.width('auto');
                $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            }
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
            
          
        },
        
        _scaleUp : function(e){
            var scaleFactor = 1.1;
            
            var $picture = $(this).find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();                        
            
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.width(Math.round($picture.width() * scaleFactor));
            $picture.height('auto'); //scale automatically
            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);

        },
        
        _scaleDown : function(e){
            var scaleFactor = 1.1;
            
            var $picture = $(this).find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();                          
            
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.height('auto');
            $picture.width($picture.width() / scaleFactor);
            
            if ($picture.width() < $container.width()) {
                $picture.height('auto');
                $picture.width($container.width());
            }
            if ($picture.height() < $container.height()) {
                $picture.width('auto');
                $picture.height($container.height());
            }
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
            
          
        },
        
        getCurPicture : function () {
            var $this = $(this);
            if ($this.data('ipUploadPicture').curPicture !== $this.data('ipUploadPicture').defaultPicture) {
                return $this.data('ipUploadPicture').curPicture;
            } else {
                return false;
            }
        },
        
        getNewPictureUploaded : function () {            
            var $this = $(this);
            return $this.data('ipUploadPicture').changed;
        },
        
        getCropCoordinatesChanged : function () {
            return true || this.ipUploadPicture('getNewPictureUploaded');
        },
        
        getCropCoordinates : function () {      
            $this = this;
            
            var $picture = $this.find('.preview');
            var $container = $picture.parent().parent();
            var $dragContainer = $picture.parent();            
            
            var coordinates = new Object;
            
            var offsetX = - parseInt($dragContainer.css('margin-left')) - parseInt($picture.css('left'));            
            var offsetY = - parseInt($dragContainer.css('margin-top')) - parseInt($picture.css('top'));
            
            var $tmpPicture = $picture.clone();
            $tmpPicture.width('auto');
            $tmpPicture.height('auto');
            $this.append($tmpPicture);
            
            var scale = $picture.width() / $tmpPicture.width();
            $tmpPicture.remove();
            
            coordinates.x1 = Math.round(offsetX / scale);
            coordinates.y1 = Math.round(offsetY / scale);
            coordinates.x2 = Math.round(coordinates.x1 + $container.width() / scale);
            coordinates.y2 = Math.round(coordinates.y1 + $container.height() / scale);
            return coordinates;
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