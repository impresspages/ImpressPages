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
                var data = $this.data('ipInlineManagementLogo');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipInlineManagementLogo', {
                        'originalLogo' : $this.clone(),
                        'imageUploadInitialized' : false,
                        'cssClass' : $this.data('cssClass')
                    })

                    $this.bind('ipModuleInlineManagementLogo.openEditPopup', $.proxy(methods.openEditPopup, $this ));

                    $this.ipModuleInlineManagementControls({
                        'Manage' : function() {
                            $this.trigger('ipModuleInlineManagementLogo.openEditPopup');
                        }
                    });
                }
            });
        },

        openEditPopup:function(event) {
            event.preventDefault();
            var $this = $(this);

            $('.ipModuleInlineManagementPopup.ipmLogo').remove();

            $('body').append('<div class="ipModuleInlineManagementPopup ipmLogo" ></div>');

            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');
            $popup.dialog({width: 800, height : 450, modal: true});

            var options = {
                cssClass: $this.data('cssclass')
            };

            var $this = this;
            var data = Object();
            data.aa = 'InlineManagement.getManagementPopupLogo';
            data.securityToken = ip.securityToken;
            data.cssClass = $this.data('cssclass');



            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._popupContentResponse,
                dataType : 'json'
            });

        },

        _popupContentResponse:function(response) {

            var $this = this;

            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');

            if (response.status == 'success') {
                $popup.html(response.html);
            }

            var data = $this.data('ipInlineManagementLogo');

            $this.data('typeSelectText', $popup.find('.ipmTypeSelect input[value=text]'));
            $this.data('typeSelectImage', $popup.find('.ipmTypeSelect input[value=image]'));
            $this.data('textManagement', $popup.find('.ipmTextManagement'));
            $this.data('imageManagement', $popup.find('.ipmImageManagement'));
            $this.data('fontSelect', $popup.find('.ipmFontSelect'));
            $this.data('colorPicker', $popup.find('.ipmColorPicker'));
            $this.data('logoText', $popup.find('.ipmLogoText'));
            $this.data('previewText', $(response.textPreview));
            $this.data('previewImage', $(response.imagePreview));
            $this.after($this.data('previewText'));
            $this.after($this.data('previewImage'));
            $this.data('previewImage').hide();
            $this.data('previewText').hide();

            var curColor = $this.data('previewText').find('a').css('color');
            var curText = $.trim($this.data('previewText').find('a').text());
            var curFont = $this.data('previewText').find('a').css('font-family');
            $this.data('previewText').find('a').css('font-family', ''); //remove font
            var defaultFont = $this.data('previewText').find('a').css('font-family'); //get default font
            $this.data('previewText').find('a').css('font-family', curFont); //restore font
            $popup.find('ul li.ipmDefaultFont').css('font-family', defaultFont);
            if (curFont.indexOf(',') == false) {
                curFont = curFont + ',sans-serif';
            }

            //init image editing
            var options = new Object;

            var logoData = response.logoData;

            $this.data('curData', logoData);

            options.image = logoData.imageOrig;
            options.cropX1 = logoData.x1;
            options.cropY1 = logoData.y1;
            options.cropX2 = logoData.x2;
            options.cropY2 = logoData.y2;
            options.windowWidth = logoData.requiredWidth;
            options.windowHeight = logoData.requiredHeight;
            if (logoData.x2 && !options.windowWidth) {
                options.windowWidth = logoData.x2 - logoData.x1;
            }
            if (logoData.y2 && !options.windowHeight) {
                options.windowHeight = logoData.y2 - logoData.y1;
            }
            if(!options.windowWidth) {
                options.windowWidth = 400; //default width;
            }
            if(!options.windowHeight) {
                options.windowHeight= 100; //default height;
            }
            options.enableChangeHeight = true;
            options.enableChangeWidth = true;
            options.enableUnderscale = true;
            options.minWindowWidth = 10;
            options.minWindowHeight = 10;
            options.maxWindowWidth = 774;
            options.maxWindowHeight = 310;

            $this.data('ipUploadImageOptions', options);

            //init text management
            $this.data('logoText').val(curText);
            $this.data('logoText').bind('keyup', $.proxy(methods._preview, $this));

            $this.data('fontSelect').ipInlineManagementFontSelector({
                'hide_fallbacks' : true,
                'initial' : curFont,
                'selected' : function(style) {$.proxy(methods._preview, $this)();}
            });

            $this.data('colorPicker').css('backgroundColor', curColor);
            $this.data('colorPicker').ColorPicker({
                color: curColor,
                onShow: function (colpkr) {
                    $(colpkr).css('zIndex', 2000);
                    $(colpkr).fadeIn(300);
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(300);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    $this.data('colorPicker').css('backgroundColor', '#' + hex);
                    $.proxy(methods._preview, $this)();
                }
            });

            //type selection
            if (logoData.type == 'text') {
                $this.data('typeSelectText').attr('checked', 'checked');
            } else {
                $this.data('typeSelectImage').attr('checked', 'checked');
            }

            $this.find('.ipmType').buttonset();

            $.proxy(methods._updateType, $this)(); //initialize current type tab

            $this.data('typeSelectText').bind('change', $.proxy(methods._updateType, $this));
            $this.data('typeSelectImage').bind('change', $.proxy(methods._updateType, $this));
            $popup.find('.ipsConfirm').bind('click', $.proxy(methods._confirm, $this));
            $popup.find('.ipsCancel').bind('click', function(event){$popup.dialog('close');});
            $popup.bind('dialogclose', $.proxy(methods._cancel, $this));

        },

        _preview : function() {
            var $this = this;
            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');

            $this.hide();

            if ($this.data('typeSelectText').is(':checked')) {
                $this.data('previewImage').hide();
                $this.data('previewText').show();
                $this.data('previewText').find('a').text($this.data('logoText').val());
                $this.data('previewText').find('a').css('color', $this.data('colorPicker').css('background-color'));
                $this.data('previewText').find('a').css('font-family', $this.data('font-select').find('span').css('font-family'));
                $this.data('logoText').css('font-family', $this.data('font-select').find('span').css('font-family'));
            } else {
                $this.data('previewText').hide();
                $this.data('previewImage').show();
                var $imageUploader = $popup.find('.ipsImage');
                $this.data('previewImage').html('');
                $this.data('previewImage').append($imageUploader.find('.ipUploadWindow').clone());
                $this.data('previewImage').find('.ipUploadButtons').remove();
                $this.data('previewImage').find('.ui-resizable-handle').remove();
            }
        },

        _updateType : function() {
            var $this = this;
            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');
            if ($this.data('typeSelectText').is(':checked')) {
                $this.data('textManagement').show();
                $this.data('imageManagement').hide();
            } else {
                $this.data('textManagement').hide();
                $this.data('imageManagement').show();

                if (!$this.data('ipInlineManagementLogo').imageUploadInitialized) {
                    var $imageUploader = $popup.find('.ipsImage');
                    $imageUploader.ipUploadImage($this.data('ipUploadImageOptions'));
                    $popup.bind('error.ipUploadImage', {widgetController: this}, methods._addError);
                    var data = $this.data('ipInlineManagementLogo');
                    data.imageUploadInitialized = true;
                    $this.data('ipInlineManagementLogo', data);

                    $imageUploader.bind('change.ipUploadImage', $.proxy(methods._preview, $this));
                }
            }
            $.proxy(methods._preview, $this)();
        },

        _addError : function(event, errorMessage) {
            $(this).trigger('error.ipContentManagement', errorMessage);
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.aa = 'InlineManagement.saveLogo';
            data.securityToken = ip.securityToken;

            data.cssClass = $this.data('ipInlineManagementLogo').cssClass;

            if ($this.data('typeSelectText').is(':checked')) {
                data.type = 'text';
            } else {
                data.type = 'image';
            }

            //TEXT LOGO
            data.text = $this.data('logoText').val();
            data.color = $this.data('colorPicker').css('background-color');
            data.font = $this.data('fontSelect').ipInlineManagementFontSelector('getFont');
            //IMAGE LOGO
            if ($this.data('ipInlineManagementLogo').imageUploadInitialized) {
                var ipUploadImage = $popup.find('.ipsImage');
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
                        data.windowWidth = ipUploadImage.ipUploadImage('width');
                        data.windowHeight = ipUploadImage.ipUploadImage('height');
                    }
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
            var $popup = $('.ipModuleInlineManagementPopup.ipmLogo');
            if (answer && answer.status == 'success') {
                $popup.dialog('close');

                if (answer.logoHtml) {
                    var $newLogo = $(answer.logoHtml);
                    $this.replaceWith($newLogo);
                    $newLogo.ipModuleInlineManagementLogo();

                }
                $this.trigger('ipInlineManagement.logoConfirm');

            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            $this.show();
            $this.data('previewText').remove();
            $this.data('previewImage').remove();
            var data = $this.data('ipInlineManagementLogo');
            data.imageUploadInitialized = false;
            $this.data('ipInlineManagementLogo', data);
            $this.trigger('ipInlineManagement.logoCancel');
        }

    };

    $.fn.ipModuleInlineManagementLogo = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementLogo');
        }
    };

})(ip.jQuery);
