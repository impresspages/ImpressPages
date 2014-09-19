/**
 * @package ImpressPages
 *
 */

(function ($) {
    "use strict";

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $this = $(this);
                var data = $this.data('ipInlineManagementImage');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this
                        .data('ipInlineManagementImage', {
                            key: $this.data('key'),
                            cssClass: $this.data('cssclass'),
                            options: $this.data('options'),
                            defaultValue: $this.data('defaultvalue')
                        })
                        .ipModuleInlineManagementControls({
                            'Manage': function () {
                                $this.trigger('ipModuleInlineManagement.openEditPopup');
                            }
                        })
                        .on('ipModuleInlineManagement.openEditPopup', $.proxy(methods.openPopup, $this));
                }
            });
        },


        openPopup: function () {
            var $this = this;

            $this.css('width', '');
            $this.css('height', '');

            var data = Object();
            data.aa = 'InlineManagement.getManagementPopupImage';
            data.securityToken = ip.securityToken;
            data.key = $this.data('ipInlineManagementImage').key;
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: $this,
                success: methods._openPopupResponse,
                dataType: 'json'
            });

        },

        _openPopupResponse: function (response) {
            var $this = this;
            if (response.status == 'success') {
                // removing the old instance in case it still exists
                $('.ipsModuleInlineManagementImageModal').remove();
                var $popup = $(response.html).appendTo(document.body).modal('show');

                $popup
                    .css('position', 'absolute') // should go to CSS but that requires additional classes and complicates usage
                    .css($this.offset()); // since we add this offset, above style is not that bad after all

                var data = $this.data('ipInlineManagementImage');
                data.popup = $popup;
                $this.data('ipInlineManagementImage', data);

                //initialize image browser
                var imageData = response.imageData;

                var options = {};

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

                if (!options.windowWidth) {
                    options.windowWidth = $this.width(); //default width;
                }
                if (options.windowWidth < 10) {
                    options.windowWidth = 300;
                }

                if (!options.windowHeight) {
                    options.windowHeight = $this.height(); //default height;
                }
                if (options.windowHeight < 10) {
                    options.windowHeight = 100;
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
                    options.enableChangeHeight = false;
                }

                //loop and assign all inline options assigned by theme author
                $.each($this.data('options'), function (name, value) {
                    options[name] = value;
                });

                var $imageUploader = $popup.find('.ipsImage');
                $imageUploader.ipUploadImage(options);
                $imageUploader.bind('change.ipUploadImage', $.proxy(methods._preview, $this));
                $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);

            }

            $.proxy(methods._preview, $this)();
            $popup.find('.ipsConfirm').bind('click', $.proxy(methods._confirm, $this));
            $popup.find('.ipsCancel').bind('click', $.proxy(methods._cancel, $this));
            $popup.find('.ipsRemove').bind('click', $.proxy(methods._removeImage, $this));
        },

        _removeImage: function (event) {
            event.preventDefault();
            var $this = this;
            var $popup = $('.ipsModuleInlineManagementImageModal');

            if (!confirm($popup.find('.ipsRemoveConfirm').text())) {
                return;
            }

            var data = Object();
            data.aa = 'InlineManagement.removeImage';

            data.key = $this.data('ipInlineManagementImage').key;
            data.defaultValue = $this.data('ipInlineManagementImage').defaultValue;
            data.options = $this.data('ipInlineManagementImage').options;
            data.cssClass = $this.data('ipInlineManagementImage').cssClass;
            data.securityToken = ip.securityToken;
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;

            data.key = $this.data('ipInlineManagementImage').key;

            //SAVE
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: $this,
                success: methods._removeResponse,
                dataType: 'json'
            });

        },

        _removeResponse: function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                var data = $this.data('ipInlineManagementImage');
                data.popup.modal('hide').remove();

                $this.css('width', '');
                $this.css('height', '');

                if (answer.newHtml) {
                    var $newHtml = $(answer.newHtml);
                    $this.replaceWith($newHtml);
                    $newHtml.ipModuleInlineManagementImage();
                }

            }
        },

        _preview: function (event) {
            var $this = this;
            var $popup = $('.ipsModuleInlineManagementImageModal');

            var $imageUploader = $popup.find('.ipsImage');

            var windowHeight = $imageUploader.ipUploadImage('height');
            var windowWidth = $imageUploader.ipUploadImage('width');

            $this
                .width(windowWidth)
                .height(windowHeight);
            $popup
                .width(windowWidth)
                .height(windowHeight);
        },

        _confirm: function (event) {
            event.preventDefault();
            var $this = $(this);
            var $popup = $('.ipsModuleInlineManagementImageModal');
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.aa = 'InlineManagement.saveImage';
            data.securityToken = ip.securityToken;

            data.key = $this.data('ipInlineManagementImage').key;
            data.defaultValue = $this.data('ipInlineManagementImage').defaultValue;
            data.options = $this.data('ipInlineManagementImage').options;
            data.cssClass = $this.data('ipInlineManagementImage').cssClass;
            data.type = $popup.find('.ipsType').val();
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;

            //IMAGE
            var ipUploadImage = $('.ipsModuleInlineManagementImageModal').find('.ipsImage');

            if (ipUploadImage.ipUploadImage('getCurImage') == undefined) {
                $.proxy(methods._cancel, $this)(event);
                return;
            }

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

            //SAVE
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: $this,
                success: methods._confirmResponse,
                dataType: 'json'
            });
        },

        _confirmResponse: function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                var data = $this.data('ipInlineManagementImage');
                data.popup.modal('hide').remove();

                $this.css('width', '');
                $this.css('height', '');

                if (answer.newHtml) {
                    var $newHtml = $(answer.newHtml);
                    $this.replaceWith($newHtml);
                    $newHtml.ipModuleInlineManagementImage();
                }
            }
        },

        _cancel: function (event) {
            event.preventDefault();
            var $this = this;
            var data = $this.data('ipInlineManagementImage');
            data.popup.modal('hide').remove();

            $this.css('width', '');
            $this.css('height', '');
        }

    };

    $.fn.ipModuleInlineManagementImage = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementImage');
        }
    };

})(jQuery);
