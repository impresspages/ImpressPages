/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


function sleep(milliSeconds){
    var startTime = new Date().getTime(); // get the current time
    while (new Date().getTime() < startTime + milliSeconds); // hog cpu
}


(function($) {

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipInlineManagementLogo');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipInlineManagementLogo', {
                    }); 
                }
            });
        },
        
        
        refresh : function (pageId, zoneName) {
            var $this = this;
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'getManagementPopup';

            $.ajax({
                type : 'POST',
                url : document.location,
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

            $this.data('typeSelect', $this.find('.ipmType'));
            $this.data('textManagement', $this.find('.ipmTextManagement'));
            $this.data('imageManagement', $this.find('.ipmImageManagement'));
            $this.data('fontSelect', $this.find('.ipmFontSelect'));
            $this.data('colorPicker', $this.find('.ipmColorPicker'));
            $this.data('logoText', $this.find('.ipmLogoText'));
            $this.data('previewText', $('.ipmLogo'));

            var curColor = $this.data('previewText').css('color');
            var curFont = $this.data('previewText').css('font-family');
            var curText = $this.data('previewText').html();


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
            if (logoData.x2 && logoData.y2) {
                var width = logoData.x2 - logoData.x1;
                var height = logoData.y2 - logoData.y1;
                var ratio = width / logoData.requiredWidth;
                options.windowHeight = height * ratio;
            } else {
                options.windowHeight = 100;
            }
            options.enableChangeHeight = true;
            options.enableChangeWidth = true;
            options.enableUnderscale = true;
            options.minWindowWidth = 10;
            options.minWindowHeight = 10;

            var $imageUploader = $this.find('.ipaImage');
            $imageUploader.ipUploadImage(options);
            $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);

            //init text management
            $this.data('logoText').val(curText);
            $this.data('logoText').bind('keyup', $.proxy(methods._preview, $this));

            $this.data('fontSelect').ipInlineManagementFontSelector({
                'hide_fallbacks' : true,
                'initial' : 'Courier New,Courier New,Courier,monospace',
                'selected' : function(style) {$.proxy(methods._preview, $this)();}
            });

            $this.data('colorPicker').css('backgroundColor', curColor);
            $this.data('colorPicker').ColorPicker({
                color: curColor,
                onShow: function (colpkr) {
                    console.log('show');
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
                $this.data('typeSelect').val('text');
            } else {
                $this.data('typeSelect').val('image');
            }

            jQuery.proxy(methods._updateType, $this)();

            $this.data('typeSelect').bind('change', jQuery.proxy(methods._updateType, $this));


            $this.find('.ipaConfirm').bind('click', jQuery.proxy(methods._confirm, $this));
            $this.find('.ipaCancel').bind('click', jQuery.proxy(methods._cancel, $this));
        },

        _preview : function() {
            $this = this;
            $('.ipmLogo').text($this.data('logoText').val());
            $('.ipmLogo').css('color', $this.data('colorPicker').css('background-color'));
            $('.ipmLogo').css('font-family', $this.data('font-select').find('span').css('font-family'));
        },


        _updateType : function() {
            $this = this;
            if ($this.data('typeSelect').val() == 'text') {
                $this.data('textManagement').show();
                $this.data('imageManagement').hide();
            } else {
                $this.data('textManagement').hide();
                $this.data('imageManagement').show();
            }
        },

        _addError : function(event, errorMessage) {
            $(this).trigger('error.ipContentManagement', errorMessage);
        },

        _confirm : function (event) {
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
            var data = Object();
            data.g = 'developer';
            data.m = 'inline_management';
            data.a = 'saveLogo';
            data.text = $this.data('logoText').val();
            data.color = $this.data('colorPicker').css('background-color');
            data.font = $this.data('fontSelect').css('background-color');

            $.ajax({
                type : 'POST',
                url : document.location,
                data : data,
                context : $this,
                success : methods._refreshResponse,
                dataType : 'json'
            });
        },
        
        _cancel : function (event) {
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoCancel');
            $this.dialog('close');
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