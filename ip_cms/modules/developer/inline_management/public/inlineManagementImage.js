/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";

(function($) {

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipInlineManagementImage');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this
                    .data('ipInlineManagementImage', {
                        key: $this.data('key')
                    })
                    .ipModuleInlineManagementControls({
                        'Manage' : function() {
                            $this.trigger('ipModuleInlineManagement.openEditPopup');
                        }
                    })
                    .bind('ipModuleInlineManagement.openEditPopup', $.proxy(methods.openPopup, $this ));
                }
            });
        },
        

        openPopup : function () {
            var $this = this;

            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'getManagementPopupImage';

            data.key = $this.data('ipInlineManagementImage').key;

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];

            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._openPopupResponse,
                dataType : 'json'
            });

        },

        _openPopupResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                var $overlay = new $.ui.dialog.overlay();
                $overlay = $overlay.$el;
                $overlay.css('z-index', 2000);

                $('.ipModuleInlineManagementPopupImage').remove();
                $('body').append('<div class="ipModuleInlineManagementPopupImage" ></div>');
                var $popup = $('.ipModuleInlineManagementPopupImage');

                $popup.html(response.html);
                $popup.css('z-index', 2010);

                $popup.css('position', 'absolute');
                $popup.css($this.offset());


                var data = $this.data('ipInlineManagementImage');
                data.popup = $popup;
                data.overlay = $overlay;
                $this.data('ipInlineManagementImage', data);

                //initialize image browser
                var imageData = response.imageData;

                var options = new Object;

                options.image = imageData.imageOrig;
                options.cropX1 = imageData.x1;
                options.cropY1 = imageData.y1;
                options.cropX2 = imageData.x2;
                options.cropY2 = imageData.y2;
                if (imageData.requiredWidth) {
                    options.windowWidth = imageData.requiredWidth;
                }
                if (imageData.requiredHeight) {
                    options.windowHeight = imageData.requiredHeight;
                }

                if (imageData.x2 && !options.windowWidth) {
                    options.windowWidth = imageData.x2 - imageData.x1;
                }
                if (imageData.y2 && !options.windowHeight) {
                    options.windowHeight = imageData.y2 - imageData.y1;
                }

                if(!options.windowWidth) {
                    options.windowWidth = $this.width(); //default width;
                }
                if(!options.windowHeight) {
                    options.windowHeight= $this.height(); //default height;
                }

                options.enableChangeWidth = true;
                options.enableChangeHeight = true;
                options.enableUnderscale = true;
                options.minWindowWidth = 10;
                options.minWindowHeight = 10;
                options.maxWindowWidth = 2000;
                options.maxWindowHeight = 1000;

                var inlineOptions = $this.data('options'); //options defined in theme
                if (inlineOptions.width) {
                    options.windowWidth = inlineOptions.width;
                    options.enableChangeWidth = false;
                }
                if (inlineOptions.height) {
                    options.windowHeight = inlineOptions.height;
                    options.enableChangeHeiht = false;
                }

                //loop and assign all inline options assigned by theme author
                jQuery.each($this.data('options'), function(name, value) {
                    options[name] = value;
                });

                var $imageUploader = $('.ipModuleInlineManagementPopupImage').find('.ipaImage');
                $imageUploader.ipUploadImage(options);
                $imageUploader.bind('imageResized.ipUploadImage', jQuery.proxy(methods._preview, $this));
                $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);

            }

            //$.proxy(methods._preview, $this)();

            $('.ipModuleInlineManagementPopupImage').find('.ipaConfirm').bind('click', jQuery.proxy(methods._confirm, $this));
            $('.ipModuleInlineManagementPopupImage').find('.ipaCancel').bind('click', jQuery.proxy(methods._cancel, $this));
        },

        _preview : function(event) {
            var $this = this;

            var $popup = $('.ipModuleInlineManagementPopupImage');

            var $imageUploader = $popup.find('.ipaImage');

            var windowHeight = $imageUploader.ipUploadImage('getWindowHeight');
            var windowWidth = $imageUploader.ipUploadImage('getWindowWidth');

            $popup.find('.ipaControls').css('width', (windowWidth - 20) + 'px'); //20 - padding


            $this.css('width', windowWidth + 'px');
            $this.css('height', (windowHeight + $popup.find('.ipaControls').height() + 20) + 'px'); //20 - padding
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            var $popup = $('.ipModuleInlineManagementPopupImage');
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'saveImage';

            data.key = $this.data('ipInlineManagementImage').key;
            data.type = $popup.find('.ipaType').val();

            //IMAGE
            var ipUploadImage = $('.ipModuleInlineManagementPopupImage').find('.ipaImage');
            if (ipUploadImage.ipUploadImage('getNewImageUploaded')) {
                var newImage = ipUploadImage.ipUploadImage('getCurImage');
                if (newImage) {
                    data.newImage = newImage;
                }
            }

            if (ipUploadImage.ipUploadImage('getCropCoordinatesChanged') && ipUploadImage.ipUploadImage('getCurImage') != false) {
                var cropCoordinates = ipUploadImage.ipUploadImage('getCropCoordinates');
                if (cropCoordinates) {
                    data.cropX1 = cropCoordinates.x1;
                    data.cropY1 = cropCoordinates.y1;
                    data.cropX2 = cropCoordinates.x2;
                    data.cropY2 = cropCoordinates.y2;
                    data.windowWidth = ipUploadImage.ipUploadImage('getImageWidth');
                    data.windowHeight = ipUploadImage.ipUploadImage('getImageHeight');
                }
            }

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];

            //SAVE
            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._confirmResponse,
                dataType : 'json'
            });
        },

        _confirmResponse : function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                if (answer.imageSrc) {
                    $this.attr('src', answer.imageSrc + '?rnd=' + Math.floor((Math.random()*10000000)+1));
                }
                var data = $this.data('ipInlineManagementImage');
                data.overlay.remove();
                data.popup.remove();

                $this.css('width', 'auto');
                $this.css('height', 'auto');
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            var data = $this.data('ipInlineManagementImage');
            data.overlay.remove();
            data.popup.remove();

            $this.css('width', 'auto');
            $this.css('height', 'auto');
        }

    };

    $.fn.ipModuleInlineManagementImage = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementImage');
        }
    };

})(jQuery);
