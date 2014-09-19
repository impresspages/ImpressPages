/**
 * @package ImpressPages
 *
 *
 */

/**
 *
 * Available options:
 *
 * backgroundImage - default image to be used when real image is not uploaded (not implemented)
 * backgroundColor - (not implemented)
 * image - url to image to be cropped / re-sized
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
 * enableUnderscale - allow user to minimize image without limits (false by default. Always true if autosizeType is 'fit')
 * enableFraming - allow user to frame the image
 * enableChangeWidth - allow user to change container width
 * enableChangeHeight - allow user to change container height
 * autosizeType - how to resize image after upload. Available options: crop, fit, resize. Default - resize (tries to resize container to fit in the photo. Fall back to crop if impossible)
 *
 * uploadHandler - link to PHP script that will accept uploads
 *
 */


(function ($) {
    "use strict";

    var methods = {
        init: function (options) {

            return this.each(function () {
                var $this = $(this);

                var data = $this.data('ipUploadImage');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    var defaultImage;
                    if (options.defaultImage) {
                        defaultImage = options.defaultImage;
                    } else {
                        defaultImage = ipFileUrl('Ip/Internal/Upload/assets/empty.gif');
                    }

                    var curImage;
                    if (options.image) {
                        curImage = options.image;
                    } else {
                        //curImage = defaultImage;
                    }

                    if (!options.windowWidth) {
                        options.windowWidth = $this.width();
                    }
                    if (!options.windowHeight) {
                        if ($this.height()) {
                            options.windowHeight = $this.height();
                        } else {
                            options.windowHeight = options.windowWidth;
                        }
                    }


                    if (!options.maxWindowWidth) {
                        options.maxWindowWidth = $this.width();
                    }
                    if (!options.maxWindowHeight) {
                        options.maxWindowHeight = 10000;
                    }
                    if (!options.minWindowWidth) {
                        options.minWindowWidth = 10;
                    }
                    if (!options.minWindowHeight) {
                        options.minWindowHeight = 10;
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
                    if (typeof options.aspectRatio == 'undefined') {
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
                        options.autosizeType = 'resize';
                    }

                    if (options.autosizeType == 'fit') {
                        options.enableUnderscale = true;
                    }


                    var uniqueId = Math.floor(Math.random() * 9999999999999999) + 1;
                    $this.data('ipUploadImage', {
                        windowWidth: options.windowWidth,
                        windowHeight: options.windowHeight,
                        enableChangeWidth: options.enableChangeWidth,
                        enableChangeHeight: options.enableChangeHeight,
                        enableScale: options.enableScale,
                        enableUnderscale: options.enableUnderscale,
                        enableFraming: options.enableFraming,
                        autosizeType: options.autosizeType,
                        maxWindowWidth: options.maxWindowWidth,
                        maxWindowHeight: options.maxWindowHeight,
                        minWindowWidth: options.minWindowWidth,
                        minWindowHeight: options.minWindowHeight,
                        cropX1: options.cropX1,
                        cropY1: options.cropY1,
                        cropX2: options.cropX2,
                        cropY2: options.cropY2,

                        defaultImage: defaultImage,
                        imageChanged: false,
                        coordinatesChanged: false,

                        curImage: curImage,
                        uniqueId: uniqueId
                    });


                    var data = Object();
                    data.aa = 'Content.getImageContainerHtml';

                    $.ajax({
                        type: 'GET',
                        url: ip.baseUrl,
                        data: data,
                        context: $this,
                        success: containerHtmlResponse,
                        dataType: 'json'
                    });


                    $this.on('scaleUp.ipUploadImage scaleDown.ipUploadImage resize.ipUploadImage framed.ipUploadImage addImage.ipUploadImage', function () {
                        $(this).trigger('change.ipUploadImage');
                    })

                }


            });

        },

        destroy: function () {
            return this.each(function () {
                var $this = $(this);
                $this.data('ipUploadImage', null);
                $this.data('originalWidth', null);
                $this.data('originalHeight', null);
                $this.data('firstImageLoaded', null);
                $this.html('');
                $this.off();
            });
        },

        getCurImage: function () {
            var $this = this;
            if ($this.data('ipUploadImage').curImage !== $this.data('ipUploadImage').defaultImage) {
                return $this.data('ipUploadImage').curImage;
            } else {
                return false;
            }
        },

        getNewImageUploaded: function () {
            var $this = this;
            return $this.data('ipUploadImage').imageChanged;
        },

        getCropCoordinatesChanged: function () {
            var $this = this;
            return $this.data('ipUploadImage').coordinatesChanged || this.ipUploadImage('getNewImageUploaded');
        },

        getCropCoordinates: function () {
            var $this = this;

            var $image = $this.find('.ipsImage');
            var $window = $image.parent().parent();
            var $dragContainer = $image.parent();

            var coordinates = new Object;

            var offsetX = -parseInt($dragContainer.css('margin-left')) - parseInt($image.css('left'));
            var offsetY = -parseInt($dragContainer.css('margin-top')) - parseInt($image.css('top'));


            var currentWidth = $image.width();

            var originalWidth = $image.data('originalWidth');

            var scale = currentWidth / originalWidth;

            coordinates.x1 = Math.round(offsetX / scale);
            coordinates.y1 = Math.round(offsetY / scale);
            coordinates.x2 = Math.round(coordinates.x1 + $window.width() / scale);
            coordinates.y2 = Math.round(coordinates.y1 + $window.height() / scale);
            return coordinates;
        },

        getImageWidth: function () {
            var $this = this;
            return $this.find('.ipsModuleUploadWindow').width();
        },

        getImageHeight: function () {
            var $this = this;
            return $this.find('.ipsModuleUploadWindow').height();
        },

        width: function () {
            var $this = this;
            $this.find('.ipsButtons').hide();
            var answer = $this.find('.ipsModuleUploadWindow').width();
            $this.find('.ipsButtons').show();
            return answer;
        },

        height: function () {
            var $this = this;
            $this.find('.ipsButtons').hide();
            var answer = $this.find('.ipsModuleUploadWindow').height();
            $this.find('.ipsButtons').show();
            return answer;
        }



    };


    /**
     * cofigure management tools. It just fixes everything after image or window size changes.
     * @param imageCenterXPercentage
     * @param imageCenterYPercentage
     */
    var configureManagement = function (imageCenterXPercentage, imageCenterYPercentage) {
        var $this = $(this);
        var $image = $this.find('.ipsImage');
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


        var imageCenterX = $image.width() * imageCenterXPercentage / 100;
        var imageCenterY = $image.height() * imageCenterYPercentage / 100;
        var marginHorizontal = $image.width() - $window.width();
        if (marginHorizontal < 0) {
            marginHorizontal = 0;
        }

        $dragContainer.css('margin-left', -marginHorizontal);
        $dragContainer.css('margin-right', -marginHorizontal);
        $dragContainer.width($window.width() + marginHorizontal * 2);
        $image.css('left', Math.round($dragContainer.width() / 2 - imageCenterX));
        if (parseInt($image.css('left')) < 0) {
            $image.css('left', 0);
        }
        if (parseInt($image.css('left')) > $dragContainer.width() - $image.width()) {
            $image.css('left', $dragContainer.width() - $image.width());
        }


        var marginVertical = $image.height() - $window.height();
        if (marginVertical < 0) {
            marginVertical = 0;
        }

        $dragContainer.css('margin-top', -marginVertical);
        $dragContainer.css('margin-bottom', -marginVertical);
        $dragContainer.height($window.height() + marginVertical * 2);
        $image.css('top', Math.round($dragContainer.height() / 2 - imageCenterY));
        if (parseInt($image.css('top')) < 0) {
            $image.css('top', 0);
        }
        if (parseInt($image.css('top')) > $dragContainer.height() - $image.height()) {
            $image.css('top', $dragContainer.height() - $image.height());
        }


    };


    var scaleUp = function (e) {
        var $this = $(this);
        var scaleFactor = 1.1;

        var $image = $(this).find('.ipsImage');
        var $window = $image.parent().parent();
        var $dragContainer = $image.parent();

        var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
        var imageCenterXPercentage = imageCenterX * 100 / $image.width();

        var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
        var imageCenterYPercentage = imageCenterY * 100 / $image.height();

        $image.width(Math.round($image.width() * scaleFactor));
        $image.height('auto'); //scale automatically
        $image.height(Math.round($image.height())); //set exact value made by automatic scale

        $.proxy(configureManagement, $this)(imageCenterXPercentage, imageCenterYPercentage);

        var data = $this.data('ipUploadImage');
        data.coordinatesChanged = true;
        $this.data('ipUploadImage', data);

        $this.trigger('scaleUp.ipUploadImage');
    };

    var scaleDown = function (e) {
        var $this = $(this);

        var scaleFactor = 1.1;

        var $image = $(this).find('.ipsImage');
        var $dragContainer = $image.parent();

        var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
        var imageCenterXPercentage = imageCenterX * 100 / $image.width();

        var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
        var imageCenterYPercentage = imageCenterY * 100 / $image.height();

        $image.width(Math.round($image.width())); //set exact value made by automatic scale
        $image.height('auto');
        $image.width($image.width() / scaleFactor);


        $.proxy(configureManagement, $this)(imageCenterXPercentage, imageCenterYPercentage);

        var data = $this.data('ipUploadImage');
        data.coordinatesChanged = true;
        $this.data('ipUploadImage', data);

        $this.trigger('scaleDown.ipUploadImage');

    };


    var containerHtmlResponse = function (response) {
        var $this = this;

        if (response.status != 'success') {
            return;
        }

        $this.html(response.html);

        var data = $this.data('ipUploadImage');
        var $uploadWindow = $this.find('.ipsModuleUploadWindow');

        $this.find('.ipsButtonBrowse').attr('id', 'ipUploadButton_' + data.uniqueId);

        $uploadWindow.width(data.windowWidth);
        if (data.maxWindowWidth && $uploadWindow.width() > data.maxWindowWidth) {
            $uploadWindow.width(data.maxWindowWidth);
        }
        $uploadWindow.height(data.windowHeight);
        if (data.maxWindowHeight && $uploadWindow.height() > data.maxWindowHeight) {
            $uploadWindow.height(data.maxWindowHeight);
        }

        if (data.maxWindowWidth > data.minWindowWidth || data.maxWindowHeight > data.minWindowHeight) {
            var resizableOptions = Object();
            resizableOptions.maxWidth = data.maxWindowWidth;
            resizableOptions.maxHeight = data.maxWindowHeight;
            resizableOptions.minWidth = data.minWindowWidth;
            resizableOptions.minHeight = data.minWindowHeight;
            $uploadWindow.resizable(resizableOptions);

            $uploadWindow.bind("resize", function (event, ui) {
                $.proxy(resizedWindow, $this)(event, ui);
            });
        }

        var $buttonScaleUp = $this.find('.ipsButtonScaleUp');
        if (data.enableScale) {
            $buttonScaleUp.click(function (e) {
                e.preventDefault();
                $.proxy(scaleUp, $this)(e);
            });
        } else {
            $buttonScaleUp.hide();
        }

        var $buttonScaleDown = $this.find('.ipsButtonScaleDown');
        if (data.enableScale) {
            $buttonScaleDown.click(function (e) {
                e.preventDefault();
                $.proxy(scaleDown, $this)(e);
            });
        } else {
            $buttonScaleDown.hide();
        }

        //uploaded new photo and loaded. Reinit drag container
        $this.find('.ipsImage').on('load', function () {
            $.proxy(imageLoaded, $this)();
        });

        if (data.curImage) {
            $this.find('.ipsImage').attr('src', ipFileUrl('file/repository/' + data.curImage));
        }

        $this.find('.ipsImage').draggable({
            containment: "parent",
            disabled: !data.enableFraming,
            stop: jQuery.proxy(function (event, ui) {
                $(this).trigger('framed.ipUploadImage');
            }, $this)
        });

        $this.bind("dragstop", function (event, ui) {
            $this = $(this);
            var data = $this.data('ipUploadImage');
            data.coordinatesChanged = true;
            $this.data('ipUploadImage', data);
        });

        $this.find('#ipUploadButton_' + data.uniqueId).click(function (e) {
            e.preventDefault();
            var repository = new ipRepository({preview: 'thumbnails', filter: 'image'});
            repository.bind('ipRepository.filesSelected', $.proxy(uploadedNewFile, $this));
        });

        $(this).trigger('change.ipUploadImage');
    };

    var resizedWindow = function (resizeEvent, ui) {
        var $this = $(this);
        var $image = $this.find('.ipsImage');
        var $window = $image.parent().parent();
        var $dragContainer = $image.parent();


        var imageCenterX = ($dragContainer.width() / 2) - parseInt($image.css('left'));
        var imageCenterXPercentage = imageCenterX * 100 / $image.width();

        var imageCenterY = ($dragContainer.height() / 2) - parseInt($image.css('top'));
        var imageCenterYPercentage = imageCenterY * 100 / $image.height();

        var data = $this.data('ipUploadImage');
        data.coordinatesChanged = true;
        $this.data('ipUploadImage', data);


        $.proxy(configureManagement, $this)(imageCenterXPercentage, imageCenterYPercentage);
        $this.trigger('resize.ipUploadImage');
    };

    var uploadedNewFile = function (event, files) {
        var $this = this;
        var data = $this.data('ipUploadImage');


        if (files.length == 0) {
            return;
        }

        var file = files[0];


        data.curImage = file.fileName;
        data.imageChanged = true;
        data.coordinatesChanged = true;
        $this.data('ipUploadImage', data);
        $this.find('.ipsImage').attr('src', file.originalUrl);
    };


    /**
     * img onLoad event
     */
    var imageLoaded = function () {
        var $this = $(this);
        var $image = $this.find('.ipsImage');
        var $window = $image.parent().parent();
        var $dragContainer = $image.parent();
        var data = $this.data('ipUploadImage');

        $image.width('auto');
        $image.height('auto');

        $image.data('originalWidth', $image.width());
        $image.data('originalHeight', $image.height());

        if ($window.height() == 0 || $image.height() == 0) {
            return; //to avoid division by zero.
        }

        if (!data.curImage) {//default image loaded. There is no user image specified yet.
            $.proxy(autosize, $(this))('fit', false);
            return;
        }

        if ($this.ipUploadImage('getNewImageUploaded')) { //new image uploaded. Center it.
            var data = $this.data('ipUploadImage');
            data.coordinatesChanged = true;
            $this.data('ipUploadImage', data);

            $.proxy(autosize, $(this))(data.autosizeType, true);
        } else { //current image loaded. Crop it as it was cropped before
            $.proxy(restoreOriginalDimensions, $(this))();
        }

        if (!$this.data('firstImageLoaded')) {
            $this.data('firstImageLoaded', true);
            $this.trigger('ready.ipUploadImage');
        } else {
            $this.trigger('addImage.ipUploadImage');
        }


    };


    /**
     * Autosize image and window if allowed
     */
    var autosize = function (autosizeType, allowWindowResize) {
        var $this = $(this);
        var $image = $this.find('.ipsImage');
        var $window = $image.parent().parent();
        var $dragContainer = $image.parent();
        var data = $this.data('ipUploadImage');
        var maxAspectRatio = data.maxWindowWidth / data.maxWindowHeight;
        var minAspectRatio = data.minWindowWidth / data.minWindowHeight;
        $image.height('auto');
        $image.width('auto');
        var imageAspectRatio = $image.width() / $image.height();

        //container resizing
        if (allowWindowResize) {

            switch (autosizeType) {
                case 'resize' :
                    if ($image.width() > data.maxWindowWidth || $image.height() > data.maxWindowHeight) {
                        //resize to maximum container size
                        if (maxAspectRatio < imageAspectRatio) {
                            $window.width(data.maxWindowWidth);
                            var tmpHeight = Math.floor(data.maxWindowWidth / imageAspectRatio);
                            if (tmpHeight < data.minWindowHeight) { //we are sure it is not more than max. But we need to check if it is not less than min
                                tmpHeight = data.minWindowHeight;
                            }
                            $window.height(tmpHeight);
                        } else {
                            $window.height(data.maxWindowHeight);
                            var tmpWidth = Math.floor(data.maxWindowHeight * imageAspectRatio);

                            if (tmpWidth < data.minWindowWidth) { //we are sure it is not more than max. But we need to check if it is not less than min
                                tmpWidth = data.minWindowWidth;
                            }
                            $window.width(tmpWidth);
                        }

                    } else if ($image.width() < data.minWindowWidth || $image.height() < data.minWindowHeight) {
                        //resize to minimum container size
                        if (minAspectRatio < imageAspectRatio) {
                            $window.height(data.minWindowHeight);
                            var tmpWidth = Math.floor(data.minWindowHeight * imageAspectRatio);

                            if (tmpWidth > data.maxWindowWidth) { //we are sure it is not less than min. But we need to check if it is not less than max
                                tmpWidth = data.maxWindowWidth;
                            }
                            $window.width(tmpWidth);
                        } else {
                            $window.width(data.minWindowWidth);
                            var tmpHeight = Math.floor(data.minWindowWidth / imageAspectRatio);
                            if (tmpHeight > data.maxWindowHeight) { //we are sure it is not less than min. But we need to check if it is not less than max
                                tmpHeight = data.maxWindowHeight;
                            }
                            $window.height(tmpHeight);
                        }

                    } else {
                        //resize container to exact image width / height
                        $window.width($image.width());
                        $window.height($image.height());
                    }

                    break;
                default:
            }
        }


        //image resizing
        var containerAspectRatio = $window.width() / $window.height();
        switch (data.autosizeType) {
            case 'resize' :
            case 'crop' :
                if (containerAspectRatio > imageAspectRatio) {
                    $image.width($window.width());
                    $image.height('auto');
                    $image.height(Math.ceil($image.height())); //set exact value made by automatic scale

                } else {
                    $image.height($window.height());
                    $image.width('auto');
                    $image.width(Math.ceil($image.width())); //set exact value made by automatic scale
                }
                break;
            case 'fit' :
                if (containerAspectRatio < imageAspectRatio) {
                    $image.width($window.width());
                    $image.height('auto');
                    $image.height(Math.ceil($image.height())); //set exact value made by automatic scale

                } else {
                    $image.height($window.height());
                    $image.width('auto');
                    $image.width(Math.ceil($image.width())); //set exact value made by automatic scale
                }
                break;

        }
        $.proxy(configureManagement, $this)(50, 50);
    };


    var restoreOriginalDimensions = function () {
        var $this = $(this);
        var $image = $this.find('.ipsImage');
        var $window = $image.parent().parent();
        var $dragContainer = $image.parent();
        var data = $this.data('ipUploadImage');
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
            case 'resize' :
            case 'fit' :
                var centerX = (cropX2 - cropX1) / 2 + cropX1;
                var centerY = (cropY2 - cropY1) / 2 + cropY1;
                var centerPercentageX = centerX / $image.width() * 100;
                var centerPercentageY = centerY / $image.height() * 100;
                var $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                var $windowRatio = $window.width() / $window.height();
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
                var $photoRatio = (cropX2 - cropX1) / (cropY2 - cropY1);
                var $windowRatio = $window.width() / $window.height();
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
        $.proxy(configureManagement, $this)(centerPercentageX, centerPercentageY);

    };


    $.fn.ipUploadImage = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadImage');
        }


    };

})(jQuery);
