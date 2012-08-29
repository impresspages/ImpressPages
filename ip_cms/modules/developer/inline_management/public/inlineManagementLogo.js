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
                var data = $this.data('ipInlineManagementLogo');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipInlineManagementLogo', {
                        'originalLogoHtml' : $('.ipModuleInlineManagement').clone(),
                        'imageUploadInitialized' : false,
                        'cssClass' : options.cssClass
                    });
                }
            });
        },
        
        
        refresh : function () {
            var $this = this;
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'getManagementPopupLogo';

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];

            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._refreshResponse,
                dataType : 'json'
            });
        },

        _refreshResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                $this.html(response.html);
            }

            var data = $this.data('ipInlineManagementLogo');

            $this.data('typeSelectText', $this.find('.ipmTypeSelect input[value=text]'));
            $this.data('typeSelectImage', $this.find('.ipmTypeSelect input[value=image]'));
            $this.data('textManagement', $this.find('.ipmTextManagement'));
            $this.data('imageManagement', $this.find('.ipmImageManagement'));
            $this.data('fontSelect', $this.find('.ipmFontSelect'));
            $this.data('colorPicker', $this.find('.ipmColorPicker'));
            $this.data('logoText', $this.find('.ipmLogoText'));
            $this.data('previewText', $('.ipModuleInlineManagement .ipmText .sitename'));
            $this.data('previewImage', $('.ipModuleInlineManagement .ipmImage .sitename'));

            var curColor = $this.data('previewText').css('color');
            var curText = $.trim($this.data('previewText').html());
            var curFont = $this.data('previewText').css('font-family');
            $this.data('previewText').css('font-family', ''); //remove font
            var defaultFont = $this.data('previewText').css('font-family'); //get default font
            $this.data('previewText').css('font-family', curFont); //restore font
            $this.find('ul li.ipmDefaultFont').css('font-family', defaultFont);
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

            jQuery.proxy(methods._updateType, $this)(); //initialize current type tab

            $this.data('typeSelectText').bind('change', jQuery.proxy(methods._updateType, $this));
            $this.data('typeSelectImage').bind('change', jQuery.proxy(methods._updateType, $this));
            $this.find('.ipaConfirm').bind('click', jQuery.proxy(methods._confirm, $this));
            $this.find('.ipaCancel').bind('click', function(event){$this.dialog('close');});
            $this.bind('dialogclose', jQuery.proxy(methods._cancel, $this));
        },

        _preview : function() {
            var $this = this;

            if ($this.data('typeSelectText').attr("checked") != undefined) {
                $this.data('previewText').text($this.data('logoText').val());
                $this.data('previewText').css('color', $this.data('colorPicker').css('background-color'));
                $this.data('previewText').css('font-family', $this.data('font-select').find('span').css('font-family'));
            } else {
                var $imageUploader = $this.find('.ipaImage');
                $this.data('previewImage').html('');
                $this.data('previewImage').append($imageUploader.find('.ipUploadWindow').clone());
                $this.data('previewImage').find('.ipUploadButtons').remove();
                $this.data('previewImage').find('.ui-resizable-handle').remove();
            }

        },


        _updateType : function() {
            var $this = this;
            if ($this.data('typeSelectText').attr("checked") != undefined) {
                $this.data('textManagement').show();
                $this.data('imageManagement').hide();
                $this.data('previewText').parent().show();
                $this.data('previewImage').parent().hide();
            } else {
                $this.data('textManagement').hide();
                $this.data('imageManagement').show();
                $this.data('previewText').parent().hide();
                $this.data('previewImage').parent().show();

                if (!$this.data('ipInlineManagementLogo').imageUploadInitialized) {
                    var $imageUploader = $this.find('.ipaImage');
                    $imageUploader.ipUploadImage($this.data('ipUploadImageOptions'));
                    $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);
                    var data = $this.data('ipInlineManagementLogo');
                    data.imageUploadInitialized = true;
                    $this.data('ipInlineManagementLogo', data);

                    $imageUploader.bind('imageResized.ipUploadImage', jQuery.proxy(methods._preview, $this));
                    $imageUploader.bind('imageFramed.ipUploadImage', jQuery.proxy(methods._preview, $this));
                    $imageUploader.bind('imageScaleUp.ipUploadImage', jQuery.proxy(methods._preview, $this));
                    $imageUploader.bind('imageScaleDown.ipUploadImage', jQuery.proxy(methods._preview, $this));
                    }
            }
        },


        _addError : function(event, errorMessage) {
            $(this).trigger('error.ipContentManagement', errorMessage);
        },

        _confirm : function (event) {
            event.preventDefault();
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'saveLogo';

            data.cssClass = $this.data('ipInlineManagementLogo').cssClass;

            if ($this.data('typeSelectText').attr("checked") != undefined) {
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
                var ipUploadImage = $this.find('.ipaImage');
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
                        data.windowWidth = ipUploadImage.ipUploadImage('getWindowWidth');
                        data.windowHeight = ipUploadImage.ipUploadImage('getWindowHeight');
                    }
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
            $this = this;

            if (answer && answer.status == 'success') {
                if (answer.logoHtml) {
                    $('.ipModuleInlineManagement').replaceWith(answer.logoHtml);
                }
                $('.ipModuleInlineManagement').ipModuleInlineManagement();
                $this.trigger('ipInlineManagement.logoConfirm');
                $this.dialog('close');
            }
        },

        _cancel : function (event) {
            event.preventDefault();
            var $this = this;
            $('.ipModuleInlineManagement').replaceWith($this.data('ipInlineManagementLogo').originalLogoHtml);
            $this.trigger('ipInlineManagement.logoCancel');
            $('.ipModuleInlineManagement').ipModuleInlineManagement();

        }
        
        

        
    };
    
    

    $.fn.ipInlineManagementLogo = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementLogo');
        }
    };
    
    

})(jQuery);