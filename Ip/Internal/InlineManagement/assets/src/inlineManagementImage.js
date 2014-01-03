/**
 * @package ImpressPages
 *
 */

(function($) {
    "use strict";

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipInlineManagementImage');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this
                    .data('ipInlineManagementImage', {
                        key: $this.data('key'),
                        cssClass: $this.data('cssclass'),
                        options: $this.data('options'),
                        defaultValue: $this.data('defaultvalue')
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



            $this.css('width', '');
            $this.css('height', '');

            var data = Object();
            data.aa = 'InlineManagement.getManagementPopupImage';
            data.securityToken = ip.securityToken;
            data.key = $this.data('ipInlineManagementImage').key;
            data.zoneName = ip.zoneName;
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;

            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._openPopupResponse,
                dataType : 'json'
            });

        },

        _openPopupResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                var $overlay = $('<div class="ui-widget-overlay"></div>').appendTo("body").css('position', 'fixed');//new $.ui.dialog.overlay();
                $overlay.css('z-index', 2000);

                $('.ipModuleInlineManagementPopup.ipmImage').remove();
                $('body').append('<div class="ipModuleInlineManagementPopup ipmImage" ></div>');
                var $popup = $('.ipModuleInlineManagementPopup.ipmImage');

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
                    options.enableChangeHeight = false;
                }

                //loop and assign all inline options assigned by theme author
                $.each($this.data('options'), function(name, value) {
                    options[name] = value;
                });

                var $imageUploader = $('.ipModuleInlineManagementPopup.ipmImage').find('.ipaImage');
                $imageUploader.ipUploadImage(options);
                $imageUploader.bind('change.ipUploadImage', $.proxy(methods._preview, $this));
                $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);

            }

            //$.proxy(methods._preview, $this)();

            $('.ipModuleInlineManagementPopup.ipmImage').find('.ipaConfirm').bind('click', $.proxy(methods._confirm, $this));
            $('.ipModuleInlineManagementPopup.ipmImage').find('.ipaCancel').bind('click', $.proxy(methods._cancel, $this));

            $('.ipModuleInlineManagementPopup.ipmImage').find('.ipaRemove').bind('click', $.proxy(methods._removeImage, $this));
        },

        _removeImage : function(event) {
            event.preventDefault();
            var $this = this;
            var $popup = $('.ipModuleInlineManagementPopup.ipmImage');


            if (!confirm($popup.find('.ipaRemoveConfirm').text())) {
                return;
            }

            var data = Object();
            data.aa = 'InlineManagement.removeImage';

            data.key = $this.data('ipInlineManagementImage').key;
            data.defaultValue = $this.data('ipInlineManagementImage').defaultValue;
            data.options = $this.data('ipInlineManagementImage').options;
            data.cssClass = $this.data('ipInlineManagementImage').cssClass;
            data.securityToken = ip.securityToken;
            data.zoneName = ip.zoneName;
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;

            data.key = $this.data('ipInlineManagementImage').key;

            //SAVE
            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._removeResponse,
                dataType : 'json'
            });

        },

        _removeResponse : function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                var data = $this.data('ipInlineManagementImage');
                data.overlay.remove();
                data.popup.remove();

                $this.css('width', '');
                $this.css('height', '');

                if (answer.newHtml) {
                    var $newHtml = $(answer.newHtml);
                    $this.replaceWith($newHtml);
                    $newHtml.ipModuleInlineManagementImage();
                }

            }
        },


        _preview : function(event) {
            var $this = this;

            var $popup = $('.ipModuleInlineManagementPopup.ipmImage');

            var $imageUploader = $popup.find('.ipaImage');

            var windowHeight = $imageUploader.ipUploadImage('height');
            var windowWidth = $imageUploader.ipUploadImage('width');

            $popup.find('.ipaControls').css('width', (windowWidth - 20) + 'px'); //20 - padding


            $this.css('width', windowWidth + 'px');
            $this.css('height', (windowHeight + $popup.find('.ipaControls').height() + 20) + 'px'); //20 - padding
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            var $popup = $('.ipModuleInlineManagementPopup.ipmImage');
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.aa = 'InlineManagement.saveImage';
            data.securityToken = ip.securityToken;

            data.key = $this.data('ipInlineManagementImage').key;
            data.defaultValue = $this.data('ipInlineManagementImage').defaultValue;
            data.options = $this.data('ipInlineManagementImage').options;
            data.cssClass = $this.data('ipInlineManagementImage').cssClass;
            data.type = $popup.find('.ipaType').val();
            data.zoneName = ip.zoneName;
            data.pageId = ip.pageId;
            data.languageId = ip.languageId;


            //IMAGE
            var ipUploadImage = $('.ipModuleInlineManagementPopup.ipmImage').find('.ipaImage');


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
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._confirmResponse,
                dataType : 'json'
            });
        },

        _confirmResponse : function (answer) {
            var $this = this;

            if (answer && answer.status == 'success') {
                var data = $this.data('ipInlineManagementImage');
                data.overlay.remove();
                data.popup.remove();

                $this.css('width', '');
                $this.css('height', '');

                if (answer.newHtml) {
                    var $newHtml = $(answer.newHtml);
                    $this.replaceWith($newHtml);
                    $newHtml.ipModuleInlineManagementImage();
                }
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            var data = $this.data('ipInlineManagementImage');
            data.overlay.remove();
            data.popup.remove();

            $this.css('width', '');
            $this.css('height', '');
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

})(ip.jQuery);
