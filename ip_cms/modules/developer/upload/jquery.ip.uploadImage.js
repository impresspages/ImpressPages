/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

/**
 * 
 * Available options:
 * 
 * backgroundImage - default image to be used when real image is not uploaded (not implemented)
 * backgroundColor - (not implemented)
 * image - url to image to be cropped / resized
 * cropX1 - current cropping coordinates
 * cropY1 
 * cropX2
 * cropY2 * 
 * windowWidth - width of container (100% if not set)
 * windowHeight - height of container (the same as width if not set)
 * constrainProportions - update container parameters to constrain proportions on resize (not implemented)
 * maxWindowWidth
 * maxWindowHeight
 * minWindowWidth
 * minWindowHeight
 * enableScale - allow user to scale image
 * enableUnderscale - allow user to minimize image without limits (false by default. Always true if autosizeType is 'fill')
 * enableFraming - allow user to frame the image
 * enableChangeWidth - allow user to change container width
 * enableChangeHeight - allow user to change container height
 * autosizeType - how to resize image after upload. Available options: crop, fit. Default - crop
 * 
 * uploadHandler - link to PHP script that will accept uploads
 * 
 */


(function($) {

    var methods = {
        init : function(options) {

            return this.each(function() {
                var $this = $(this);
                
                var data = $this.data('ipUploadImage');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    var defaultImage;
                    if (options.defaultImage) {
                        defaultImage = options.defaultImage;
                    } else {
                        defaultImage = ip.moduleDir + 'developer/upload/img/empty.gif';
                    }
                    
                    var curImage;
                    if (options.image) {
                        curImage = options.image;
                    } else {
                        curImage = defaultImage;
                    }
                    
                    if (!options.windowWidth) {
                        options.windowWidth = $this.width();
                    }
                    if (!options.windowHeight) {
                        options.windowHeight = $this.width();
                    }
                    
                    
                    if (!options.maxWindowWidth) {
                        options.maxWindowWidth = $this.width();
                    }
                    if (!options.maxWindowHeight) {
                        options.maxWindowHeight = 10000;
                    }
                    if (!options.minWindowWidth) {
                        options.minWindowWidth = 1;
                    }
                    if (!options.minWindowHeight) {
                        options.minWindowHeight = 1;
                    }
                    
                    if (!options.enableChangeWidth) {
                        options.enableChangeWidth = false;
                        options.minWindowWidth = options.windowWidth;
                        options.maxWindowWidth = options.windowWidth;
                    }
                    if (!options.enableChangeHeight) {
                        options.enableChangeHeight = false;
                        options.minWindowHeight = options.windowHeight;
                        options.maxWindowHeight = options.windowHeight;
                    }
                    if (typeof options.enableScale == 'undefined') {
                        options.aspectRatio = true;
                    }
                    
                    if (typeof options.enableScale == 'undefined') {
                        options.enableScale = true;
                    }
                    
                    if (typeof options.enableFraming == 'undefined') {
                        options.enableFraming = true;
                    }

                    if (typeof options.enableUnderscale == 'undefined') {
                        options.enableUnderscale = false;
                    }
                    
                    if (typeof options.autosizeType == 'undefined') {
                        options.autosizeType = 'crop';
                    }

                    if (options.autosizeType == 'fit') {
                        options.enableUnderscale = true;
                    }
                    

                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;
                    $this.data('ipUploadImage', {
                        windowWidth : options.windowWidth,
                        windowHeight : options.windowHeight,
                        enableChangeWidth : options.enableChangeWidth,
                        enableChangeHeight : options.enableChangeHeight,
                        enableScale : options.enableScale,
                        enableUnderscale : options.enableUnderscale,
                        enableFraming : options.enableFraming,
                        autosizeType : options.autosizeType,
                        maxWindowWidth : options.maxWindowWidth,
                        maxWindowHeight : options.maxWindowHeight,
                        minWindowWidth : options.minWindowWidth,
                        minWindowHeight : options.minWindowHeight,
                        cropX1 : options.cropX1,
                        cropY1 : options.cropY1,
                        cropX2 : options.cropX2,
                        cropY2 : options.cropY2,
                        
                        defaultImage : defaultImage,
                        imageChanged : false,
                        coordinatesChanged : false,
                        
                        curImage : curImage,
                        uniqueId : uniqueId,
                    }); 
                    
                    
                    var data = Object();
                    data.g = 'developer';
                    data.m = 'upload';
                    data.a = 'getImageContainerHtml';
                    
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
            
            var data = $this.data('ipUploadImage');
            var $ipUploadWindow = $this.find('.ipUploadWindow');
            
            
            $this.find('.ipUploadImage').attr('src', ip.baseUrl + data.curImage);
            $this.find('.ipUploadBrowseContainer').attr('id', 'ipUploadContainer_' + data.uniqueId);
            $this.find('.ipUploadBrowseButton').attr('id', 'ipUploadButton_' + data.uniqueId);

            $ipUploadWindow.width(data.windowWidth);
            if (data.maxWindowWidth && $ipUploadWindow.width() > data.maxWindowWidth) {
                $ipUploadWindow.width(data.maxWindowWidth);
            }
            $ipUploadWindow.height(data.windowHeight);
            if (data.maxWindowHeight && $ipUploadWindow.height() > data.maxWindowHeight) {
                $ipUploadWindow.height(data.maxWindowHeight);
            }

            if (data.maxWindowWidth > data.minWindowWidth || data.maxWindowHeight > data.minWindowHeight) {
                var resizableOptions = Object();
                resizableOptions.maxWidth = data.maxWindowWidth;
                resizableOptions.maxHeight = data.maxWindowHeight;
                resizableOptions.minWidth = data.minWindowWidth;
                resizableOptions.minHeight = data.minWindowHeight;
                $ipUploadWindow.resizable(resizableOptions);
                
                $ipUploadWindow.bind( "resize", function(event, ui) {
                    $(this).trigger('windowResize.ipUploadImage', [event, ui]);
                });                
                
            }
            $this.bind('windowResize.ipUploadImage', function(event, resizeEvent, ui) {
                $(this).ipUploadImage('_resizedWindow', resizeEvent, ui);
            });            

            
            $this.find('.ipUploadLargerButton').click(function(event) {
                event.preventDefault();
                $(this).trigger('imageScaleUp.ipUploadImage');
            });
            if (!data.enableScale) {
                $this.find('.ipUploadLargerButton').hide();
            }
            
            $this.bind('imageScaleUp.ipUploadImage', function(event) {
                $(this).ipUploadImage('_scaleUp', event);
            });

            
            
            
            
            $this.find('.ipUploadSmallerButton').click(function(event) {
                event.preventDefault();
                $(this).trigger('imageScaleDown.ipUploadImage');
            });
            if (!data.enableScale) {
                $this.find('.ipUploadSmallerButton').hide();
            }
            

            $this.bind('imageScaleDown.ipUploadImage', function(event) {
                $(this).ipUploadImage('_scaleDown', event);
            });
            
            
            //uploaded new photo and loaded. Reinit drag container
            $this.find('.ipUploadImage').load(function (){
                $this.ipUploadImage('_imageLoaded');
            });
            
            
            $this.find('.ipUploadImage').bind('imageResized.ipUploadImage', function(event, imageCenterXPercentage, imageCenterYPercentage) {
                $this.ipUploadImage('_imageResized', event, imageCenterXPercentage, imageCenterYPercentage);
            });
            
            
            
            $this.find('.ipUploadImage').draggable({ containment: "parent", disabled: !data.enableFraming });
            $this.bind( "dragstop", function(event, ui) {
                $this = $(this);
                var data = $this.data('ipUploadImage');
                data.coordinatesChanged = true;
                $this.data('ipUploadImage', data);
            });
            
            var uploader = new plupload.Uploader( {
                runtimes : 'gears,html5,flash,silverlight,browserplus',
                browse_button : 'ipUploadButton_' + data.uniqueId,
                container : 'ipUploadContainer_' + data.uniqueId,
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
                    //console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
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
                //console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
                up.refresh(); // Reposition Flash/Silverlight
            });
            
            uploader.bind('FileUploaded', function(up, file, response) {
                $this.ipUploadImage('_uploadedNewFile', up, file, response);
            });            
            
        },
        
        _resizedWindow : function (resizeEvent, ui) {
            var $this = $(this);
            var $image = $this.find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();

            
            var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
            var imageCenterXPercentage = imageCenterX * 100 / $image.width(); 
            
            var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
            var imageCenterYPercentage = imageCenterY * 100 / $image.height(); 
        
            $image.trigger('imageResized.ipUploadImage', [imageCenterXPercentage, imageCenterYPercentage]);
        },
        
        _uploadedNewFile : function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            var data = $this.data('ipUploadImage');
            data.curImage = answer.fileName;
            data.imageChanged = true;
            data.coordinatesChanged = true;
            $this.data('ipUploadImage', data);
            $this.find('.ipUploadImage').attr('src', ip.baseUrl + answer.fileName);
        },
        
        
        _imageLoaded : function() {
            var $this = $(this);
            var $image = $this.find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();
            var data = $this.data('ipUploadImage');
            
            if ($window.height() == 0 || $image.height() == 0) {
                return; //to avoid division by zero.
            }
            if ($this.ipUploadImage('getNewImageUploaded')) { //new image uploaded. Center it.
                containerAspectRatio = $window.width() / $window.height();
                $image.height('auto');
                $image.width('auto');
                imageAspectRatio = $image.width() / $image.height();
                switch (data.autosizeType) {
                    case 'fit' :
                        if (containerAspectRatio < imageAspectRatio) {
                            $image.width($window.width());
                            $image.height('auto');
                            $image.height(Math.round($image.height())); //set exact value made by automatic scale
                            
                        } else {
                            $image.height($window.height());
                            $image.width('auto');
                            $image.width(Math.round($image.width())); //set exact value made by automatic scale
                        }
                        break;
                    case 'crop' :
                    default: 
                        if (containerAspectRatio > imageAspectRatio) {
                            $image.width($window.width());
                            $image.height('auto');
                            $image.height(Math.round($image.height())); //set exact value made by automatic scale
                            
                        } else {
                            $image.height($window.height());
                            $image.width('auto');
                            $image.width(Math.round($image.width())); //set exact value made by automatic scale
                        }
                }
                
                $image.trigger('imageResized.ipUploadImage', [50, 50]);
            } else { //current image loaded. Crop it as it was cropped before
                var cropX1 = 0;
                var cropY1 = 0;
                var cropX2 = parseInt($image.width());
                var cropY2 = parseInt($image.height());
                if ($this.data('ipUploadImage').cropX1) {
                    cropX1 = parseInt($this.data('ipUploadImage').cropX1);
                }
                if ($this.data('ipUploadImage').cropY1) {
                    cropY1 = parseInt($this.data('ipUploadImage').cropY1);
                }
                if ($this.data('ipUploadImage').cropX2) {
                    cropX2 = parseInt($this.data('ipUploadImage').cropX2);
                }
                if ($this.data('ipUploadImage').cropY2) {
                    cropY2 = parseInt($this.data('ipUploadImage').cropY2);
                }
                
                switch (data.autosizeType) {
                    case 'fit' :
                            var centerX = (cropX2 - cropX1) / 2 + cropX1;
                            var centerY = (cropY2 - cropY1) / 2 + cropY1;
                            var centerPercentageX = centerX / $image.width() * 100;
                            var centerPercentageY = centerY / $image.height() * 100;
                            $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                            $windowRatio = $window.width() / $window.height();
                            if ($this.data('ipUploadImage').enableChangeHeight) {
                                $window.height(Math.round($window.width() / $photoRatio));
                            } else {
                                if ($this.data('ipUploadImage').enableChangeWidth) {
                                    $window.width(Math.round($window.height() * $photoRatio));
                                }
                            }
                            
                            if ($photoRatio < $windowRatio) {
                                var imageScale = $window.height() / (cropY2 - cropY1);
                                $image.height($image.height() * imageScale);
                                $image.width('auto');
                            } else {
                                var imageScale = $window.width() / (cropX2 - cropX1);
                                $image.width($image.width() * imageScale);
                                $image.height('auto');
                            }
                        break;
                    case 'crop' :
                            var centerX = (cropX2 - cropX1) / 2 + cropX1;
                            var centerY = (cropY2 - cropY1) / 2 + cropY1;
                            var centerPercentageX = centerX / $image.width() * 100;
                            var centerPercentageY = centerY / $image.height() * 100;
                            $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                            $windowRatio = $window.width() / $window.height();
                            if ($this.data('ipUploadImage').enableChangeHeight) {
                                $window.height(Math.round($window.width() / $photoRatio));
                            } else {
                                if ($this.data('ipUploadImage').enableChangeWidth) {
                                    $window.width(Math.round($window.height() * $photoRatio));
                                }
                            }
                            
                            if ($photoRatio > $windowRatio) {
                                var imageScale = $window.height() / (cropY2 - cropY1);
                                $image.height($image.height() * imageScale);
                                $image.width('auto');
                            } else {
                                var imageScale = $window.width() / (cropX2 - cropX1);
                                $image.width($image.width() * imageScale);
                                $image.height('auto');
                            }
                        break;
                            
                
                }
                $image.trigger('imageResized.ipUploadImage', [centerPercentageX, centerPercentageY]);

            }
            

        },
        
        _imageResized : function(e, imageCenterXPercentage, imageCenterYPercentage) {
            var $this = $(this);
            var $image = $this.find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();
            var data = $this.data('ipUploadImage');
            if (!data.enableUnderscale) {
                
                if ($image.width() < $window.width()) {
                    $image.width(Math.round($image.width())); //set exact value made by automatic scale
                    $image.height('auto');
                    $image.width($window.width());
                }
                if ($image.height() < $window.height()) {
                    $image.height(Math.round($image.height())); //set exact value made by automatic scale
                    $image.width('auto');
                    $image.height($window.height());
                }
            }
            
            
            var imageCenterX = Math.round($image.width() * imageCenterXPercentage / 100);
            var imageCenterY = Math.round($image.height() * imageCenterYPercentage / 100);
            marginHorizontal = $image.width() - $window.width();
            if (marginHorizontal < 0) {
                marginHorizontal = 0;
            }

            $dragContainer.css('margin-left', -marginHorizontal);
            $dragContainer.css('margin-right', -marginHorizontal);
            $dragContainer.width($window.width() + marginHorizontal*2);
            $image.css('left', $dragContainer.width() / 2 - imageCenterX);
            if (parseInt($image.css('left')) < 0){
                $image.css('left', 0);
            }
            if (parseInt($image.css('left')) > $dragContainer.width() - $image.width()){
                $image.css('left', $dragContainer.width() - $image.width());
            }
            
            
            
            marginVertical = $image.height() - $window.height();
            if (marginVertical < 0) {
                marginVertical = 0;
            }

            $dragContainer.css('margin-top', -marginVertical);
            $dragContainer.css('margin-bottom', -marginVertical);
            $dragContainer.height($window.height() + marginVertical*2);
            $image.css('top', $dragContainer.height() / 2 - imageCenterY);
            if (parseInt($image.css('top')) < 0){
                $image.css('top', 0);
            }
            if (parseInt($image.css('top')) > $dragContainer.height() - $image.height()){
                $image.css('top', $dragContainer.height() - $image.height());
            }
         

            
        },
        
        _uploadImage : function(e){
            var scaleFactor = 1.1;
            
            var $image = $(this).find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();                        
                
            var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
            var imageCenterXPercentage = imageCenterX * 100 / $image.width(); 
            
            var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
            var imageCenterYPercentage = imageCenterY * 100 / $image.height(); 
            
            $image.width(Math.round($image.width() / scaleFactor));
            $image.height('auto');//scale automatically
            $image.height(Math.round($image.height())); //set exact value made by automatic scale

            
            if ($image.width() < $window.width()) {
                $image.width($window.width());
                $image.height('auto');
                $image.height(Math.round($image.height())); //set exact value made by automatic scale
            }
            if ($image.height() < $window.height()) {
                $image.height($window.height());
                $image.width('auto');
                $image.height(Math.round($image.height())); //set exact value made by automatic scale
            }
            $image.trigger('imageResized.ipUploadImage', [imageCenterXPercentage, imageCenterYPercentage]);
            
          
        },
        
        _scaleUp : function(e){
            var $this = $(this);
            var scaleFactor = 1.1;
            
            var $image = $(this).find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();
            
            var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
            var imageCenterXPercentage = imageCenterX * 100 / $image.width(); 
            
            var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
            var imageCenterYPercentage = imageCenterY * 100 / $image.height(); 
            
            $image.width(Math.round($image.width() * scaleFactor));
            $image.height('auto'); //scale automatically
            $image.height(Math.round($image.height())); //set exact value made by automatic scale
            
            $image.trigger('imageResized.ipUploadImage', [imageCenterXPercentage, imageCenterYPercentage]);

            var data = $this.data('ipUploadImage');
            data.coordinatesChanged = true;
            $this.data('ipUploadImage', data);
            
        },
        
        _scaleDown : function(e){
            var $this = $(this);
            
            var scaleFactor = 1.1;
            
            var $image = $(this).find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();
            
            var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
            var imageCenterXPercentage = imageCenterX * 100 / $image.width(); 
            
            var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
            var imageCenterYPercentage = imageCenterY * 100 / $image.height(); 
            
            $image.width(Math.round($image.width())); //set exact value made by automatic scale
            $image.height('auto');
            $image.width($image.width() / scaleFactor);
            

            $image.trigger('imageResized.ipUploadImage', [imageCenterXPercentage, imageCenterYPercentage]);
            
            var data = $this.data('ipUploadImage');
            data.coordinatesChanged = true;
            $this.data('ipUploadImage', data);
            
          
        },
        
        getCurImage : function () {
            var $this = this;
            if ($this.data('ipUploadImage').curImage !== $this.data('ipUploadImage').defaultImage) {
                return $this.data('ipUploadImage').curImage;
            } else {
                return false;
            }
        },
        
        getNewImageUploaded : function () {
            var $this = this;
            return $this.data('ipUploadImage').imageChanged;
        },
        
        getCropCoordinatesChanged : function () {
            var $this = this;
            return $this.data('ipUploadImage').coordinatesChanged || this.ipUploadImage('getNewImageUploaded');
        },
        
        getCropCoordinates : function () {
            var $this = this;
            
            var $image = $this.find('.ipUploadImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();
            
            var coordinates = new Object;
            
            var offsetX = - parseInt($dragContainer.css('margin-left')) - parseInt($image.css('left'));
            var offsetY = - parseInt($dragContainer.css('margin-top')) - parseInt($image.css('top'));
            
            var $tmpImage = $image.clone();
            $tmpImage.width('auto');
            $tmpImage.height('auto');
            $this.append($tmpImage);
            
            var scale = $image.width() / $tmpImage.width();
            $tmpImage.remove();
            
            coordinates.x1 = Math.round(offsetX / scale);
            coordinates.y1 = Math.round(offsetY / scale);
            coordinates.x2 = Math.round(coordinates.x1 + $window.width() / scale);
            coordinates.y2 = Math.round(coordinates.y1 + $window.height() / scale);
            return coordinates;
        },
        
        getWindowWidth : function () {
            var $this = this;
            //return $this.find('.ipUploadWindow').width() + $this.find('.ipUploadWindow').css('border');
            return $this.find('.ipUploadWindow').outerWidth(true);
        },
        
        getWindowHeight : function () {
            var $this = this;
            return $this.find('.ipUploadWindow').height();
        }
        
        
    };
    

    $.fn.ipUploadImage = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadImage');
        }


    };
    
   

})(jQuery);