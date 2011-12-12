/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

/**
 * 
 * Available options:
 * 
 * backgroundPicture - default picture to be used when real picture is not uploaded (not implemented)
 * backgroundColor - (not implemented)
 * picture - url to image to be cropped / resized
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
 * enableScale - allow user to scale picture
 * enableUnderscale - allow user to minimize picture without limits (false by default. Always true if autosizeType is 'fill')
 * enableFraming - allow user to frame the picture
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
                
                var data = $this.data('ipUploadPicture');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    var defaultPicture;
                    if (options.defaultPicture) {
                        defaultPicture = options.defaultPicture;
                    } else {
                        defaultPicture = ip.moduleDir + 'developer/upload/img/empty.gif';
                    }
                    
                    var curPicture;
                    if (options.picture) {
                        curPicture = options.picture;
                    } else {
                        curPicture = defaultPicture;
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
                    $this.data('ipUploadPicture', {
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
                        
                        defaultPicture : defaultPicture,
                        pictureChanged : false,
                        coordinatesChanged : false,
                        
                        curPicture : curPicture,
                        uniqueId : uniqueId,
                    }); 
                    
                    
                    var data = Object();
                    data.g = 'developer';
                    data.m = 'upload';
                    data.a = 'getPictureContainerHtml';
                    
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
            
            var data = $this.data('ipUploadPicture');
            var $ipUploadWindow = $this.find('.ipUploadWindow');
            
            
            $this.find('.ipUploadImage').attr('src', ip.baseUrl + data.curPicture);
            console.log('uniqueId: ' + data.uniqueId);
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
                    $(this).trigger('windowResize.ipUploadPicture', [event, ui]);
                });                
                
            }
            $this.bind('windowResize.ipUploadPicture', function(event, resizeEvent, ui) {
                $(this).ipUploadPicture('_resizedWindow', resizeEvent, ui);
            });            

            
            $this.find('.ipUploadLargerButton').click(function(event) {
                event.preventDefault();
                $(this).trigger('pictureScaleUp.ipUploadPicture');
            });
            if (!data.enableScale) {
                $this.find('.ipUploadLargerButton').hide();
            }
            
            $this.bind('pictureScaleUp.ipUploadPicture', function(event) {
                $(this).ipUploadPicture('_scaleUp', event);
            });

            
            
            
            
            $this.find('.ipUploadSmallerButton').click(function(event) {
                event.preventDefault();
                $(this).trigger('pictureScaleDown.ipUploadPicture');
            });console.log(data);
            if (!data.enableScale) {console.log('hide');
                $this.find('.ipUploadSmallerButton').hide();
            }
            

            $this.bind('pictureScaleDown.ipUploadPicture', function(event) {
                $(this).ipUploadPicture('_scaleDown', event);
            });
            
            
            //uploaded new photo and loaded. Reinit drag container
            $this.find('.ipUploadImage').load(function (){
                $this.ipUploadPicture('_pictureLoaded');
            });
            
            
            $this.find('.ipUploadImage').bind('pictureResized.ipUploadPicture', function(event, pictureCenterXPercentage, pictureCenterYPercentage) {
                $this.ipUploadPicture('_pictureResized', event, pictureCenterXPercentage, pictureCenterYPercentage);
            });
            
            
            
            $this.find('.ipUploadImage').draggable({ containment: "parent", disabled: !data.enableFraming });
            $this.bind( "dragstop", function(event, ui) {
                $this = $(this);
                var data = $this.data('ipUploadPicture');
                data.coordinatesChanged = true;
                $this.data('ipUploadPicture', data);
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
                $this.ipUploadPicture('_uploadedNewFile', up, file, response);
            });            
            
        },
        
        _resizedWindow : function (resizeEvent, ui) {
            var $this = $(this);
            var $picture = $this.find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();

            
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
        
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
        },
        
        _uploadedNewFile : function (up, file, response) {
            console.log('uploaded new file');
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            var data = $this.data('ipUploadPicture');
            data.curPicture = answer.fileName;
            data.pictureChanged = true;
            data.coordinatesChanged = true;
            $this.data('ipUploadPicture', data);
            $this.find('.ipUploadImage').attr('src', ip.baseUrl + answer.fileName);
        },
        
        
        _pictureLoaded : function() {
            var $this = $(this);
            var $picture = $this.find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            var data = $this.data('ipUploadPicture');
            
            if ($window.height() == 0 || $picture.height() == 0) {
                return; //to avoid division by zero.
            }
            console.log('new picture loaded');
            console.log($this.ipUploadPicture('getNewPictureUploaded'));
            if ($this.ipUploadPicture('getNewPictureUploaded')) { //new picture uploaded. Center it.
                console.log('new picture');
                containerAspectRatio = $window.width() / $window.height();
                $picture.height('auto');
                $picture.width('auto');
                pictureAspectRatio = $picture.width() / $picture.height();
                switch (data.autosizeType) {
                    case 'fit' :
                        if (containerAspectRatio < pictureAspectRatio) {
                            $picture.width($window.width());
                            $picture.height('auto');
                            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
                            
                        } else {
                            $picture.height($window.height());
                            $picture.width('auto');
                            $picture.width(Math.round($picture.width())); //set exact value made by automatic scale
                        }
                        break;
                    case 'crop' :
                    default: 
                        if (containerAspectRatio > pictureAspectRatio) {
                            $picture.width($window.width());
                            $picture.height('auto');
                            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
                            
                        } else {
                            $picture.height($window.height());
                            $picture.width('auto');
                            $picture.width(Math.round($picture.width())); //set exact value made by automatic scale
                        }
                }
                
                $picture.trigger('pictureResized.ipUploadPicture', [50, 50]);
            } else { //current picture loaded. Crop it as it was cropped before
                var cropX1 = 0;
                var cropY1 = 0;
                var cropX2 = parseInt($picture.width());
                var cropY2 = parseInt($picture.height());
                if ($this.data('ipUploadPicture').cropX1) {
                    cropX1 = parseInt($this.data('ipUploadPicture').cropX1);
                }
                if ($this.data('ipUploadPicture').cropY1) {
                    cropY1 = parseInt($this.data('ipUploadPicture').cropY1);
                }
                if ($this.data('ipUploadPicture').cropX2) {
                    cropX2 = parseInt($this.data('ipUploadPicture').cropX2);
                }
                if ($this.data('ipUploadPicture').cropY2) {
                    cropY2 = parseInt($this.data('ipUploadPicture').cropY2);
                }
                
                switch (data.autosizeType) {
                    case 'fit' :
                        console.log('NOT IMPLEMENTED');
                            console.log('FIT');
                            var centerX = (cropX2 - cropX1) / 2 + cropX1;
                            var centerY = (cropY2 - cropY1) / 2 + cropY1;
                            var centerPercentageX = centerX / $picture.width() * 100;
                            var centerPercentageY = centerY / $picture.height() * 100;
                            $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                            $windowRatio = $window.width() / $window.height();
                            if ($this.data('ipUploadPicture').enableChangeHeight) {
                                $window.height(Math.round($window.width() / $photoRatio));
                            } else {
                                if ($this.data('ipUploadPicture').enableChangeWidth) {
                                    $window.width(Math.round($window.height() * $photoRatio));
                                }
                            }
                            
                            if ($photoRatio < $windowRatio) {
                                var pictureScale = $window.height() / (cropY2 - cropY1);
                                $picture.height($picture.height() * pictureScale);
                                $picture.width('auto');
                            } else {
                                var pictureScale = $window.width() / (cropX2 - cropX1);
                                $picture.width($picture.width() * pictureScale);
                                $picture.height('auto');
                            }
                        break;
                    case 'crop' :
                            console.log('crop');
                            var centerX = (cropX2 - cropX1) / 2 + cropX1;
                            var centerY = (cropY2 - cropY1) / 2 + cropY1;
                            var centerPercentageX = centerX / $picture.width() * 100;
                            var centerPercentageY = centerY / $picture.height() * 100;
                            $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                            $windowRatio = $window.width() / $window.height();
                            if ($this.data('ipUploadPicture').enableChangeHeight) {
                                $window.height(Math.round($window.width() / $photoRatio));
                            } else {
                                if ($this.data('ipUploadPicture').enableChangeWidth) {
                                    $window.width(Math.round($window.height() * $photoRatio));
                                }
                            }
                            
                            if ($photoRatio > $windowRatio) {
                                var pictureScale = $window.height() / (cropY2 - cropY1);
                                $picture.height($picture.height() * pictureScale);
                                $picture.width('auto');
                            } else {
                                var pictureScale = $window.width() / (cropX2 - cropX1);
                                $picture.width($picture.width() * pictureScale);
                                $picture.height('auto');
                            }
                        break;
                            
                
                }
                $picture.trigger('pictureResized.ipUploadPicture', [centerPercentageX, centerPercentageY]);

            }
            

        },
        
        _pictureResized : function(e, pictureCenterXPercentage, pictureCenterYPercentage) {
            var $this = $(this);
            var $picture = $this.find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            var data = $this.data('ipUploadPicture');
            if (!data.enableUnderscale) {
                
                if ($picture.width() < $window.width()) {
                    $picture.width(Math.round($picture.width())); //set exact value made by automatic scale
                    $picture.height('auto');
                    $picture.width($window.width());
                }
                if ($picture.height() < $window.height()) {
                    $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
                    $picture.width('auto');
                    $picture.height($window.height());
                }
            }
            
            
            var pictureCenterX = Math.round($picture.width() * pictureCenterXPercentage / 100);
            var pictureCenterY = Math.round($picture.height() * pictureCenterYPercentage / 100);
            marginHorizontal = $picture.width() - $window.width();
            if (marginHorizontal < 0) {
                marginHorizontal = 0;
            }

            $dragContainer.css('margin-left', -marginHorizontal);
            $dragContainer.css('margin-right', -marginHorizontal);
            $dragContainer.width($window.width() + marginHorizontal*2);
            $picture.css('left', $dragContainer.width() / 2 - pictureCenterX);
            if (parseInt($picture.css('left')) < 0){
                $picture.css('left', 0);
            }
            if (parseInt($picture.css('left')) > $dragContainer.width() - $picture.width()){
                $picture.css('left', $dragContainer.width() - $picture.width());
            }
            
            
            
            marginVertical = $picture.height() - $window.height();
            if (marginVertical < 0) {
                marginVertical = 0;
            }

            $dragContainer.css('margin-top', -marginVertical);
            $dragContainer.css('margin-bottom', -marginVertical);
            $dragContainer.height($window.height() + marginVertical*2);
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
            
            var $picture = $(this).find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();                        
                
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.width(Math.round($picture.width() / scaleFactor));
            $picture.height('auto');//scale automatically
            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale

            
            if ($picture.width() < $window.width()) {
                $picture.width($window.width());
                $picture.height('auto');
                $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            }
            if ($picture.height() < $window.height()) {
                $picture.height($window.height());
                $picture.width('auto');
                $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            }
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
            
          
        },
        
        _scaleUp : function(e){
            var $this = $(this);
            var scaleFactor = 1.1;
            
            var $picture = $(this).find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.width(Math.round($picture.width() * scaleFactor));
            $picture.height('auto'); //scale automatically
            $picture.height(Math.round($picture.height())); //set exact value made by automatic scale
            
            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);

            var data = $this.data('ipUploadPicture');
            data.coordinatesChanged = true;
            $this.data('ipUploadPicture', data);
            
        },
        
        _scaleDown : function(e){
            var $this = $(this);
            
            var scaleFactor = 1.1;
            
            var $picture = $(this).find('.ipUploadImage');
            var $window = $picture.parent().parent();
            var $dragContainer = $picture.parent();
            
            var pictureCenterX = ($dragContainer.width() / 2) - parseInt($picture.css('left'));
            var pictureCenterXPercentage = pictureCenterX * 100 / $picture.width(); 
            
            var pictureCenterY = ($dragContainer.height() / 2) - parseInt($picture.css('top'));
            var pictureCenterYPercentage = pictureCenterY * 100 / $picture.height(); 
            
            $picture.width(Math.round($picture.width())); //set exact value made by automatic scale
            $picture.height('auto');
            $picture.width($picture.width() / scaleFactor);
            

            $picture.trigger('pictureResized.ipUploadPicture', [pictureCenterXPercentage, pictureCenterYPercentage]);
            
            var data = $this.data('ipUploadPicture');
            data.coordinatesChanged = true;
            $this.data('ipUploadPicture', data);
            
          
        },
        
        getCurPicture : function () {
            var $this = this;
            if ($this.data('ipUploadPicture').curPicture !== $this.data('ipUploadPicture').defaultPicture) {
                return $this.data('ipUploadPicture').curPicture;
            } else {
                return false;
            }
        },
        
        getNewPictureUploaded : function () {
            var $this = this;
            return $this.data('ipUploadPicture').pictureChanged;
        },
        
        getCropCoordinatesChanged : function () {
            var $this = this;
            return $this.data('ipUploadPicture').coordinatesChanged || this.ipUploadPicture('getNewPictureUploaded');
        },
        
        getCropCoordinates : function () {
            var $this = this;
            
            var $picture = $this.find('.ipUploadImage');
            var $window = $picture.parent().parent();
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