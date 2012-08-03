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

            //init image editing
            var options = new Object;

            options.image = 'ip_themes/lt_pagan/img/icon_down.gif';
            options.cropX1 = 0;
            options.cropY1 = 0;
            options.cropX2 = 5;
            options.cropY2 = 5;
            options.windowWidth = 200;
            options.enableChangeHeight = true;
            options.enableChangeWidth = true;
            options.enableUnderscale = true;

            var $imageUploader = $this.find('.ipaImage');
            $imageUploader.ipUploadImage(options);
            $this.bind('error.ipUploadImage', {widgetController: this}, methods._addError);


            $('.ipaConfirm').bind('click', jQuery.proxy(methods._confirm, $this));
            $('.ipaCancel').bind('click', jQuery.proxy(methods._cancel, $this));
        },

        _addError : function(event, errorMessage) {
            $(this).trigger('error.ipContentManagement', errorMessage);
        },

        _confirm : function (event) {
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoConfirm');
        },
        
        _cancel : function (event) {
            var $this = $(this);
            $this.trigger('ipInlineManagement.logoCancel');
            $this.dialog('close');
        }
        
        
//        getPageOptions : function () {
//
//            var data = Object();
//
//            data.buttonTitle = $('#formGeneral input[name="buttonTitle"]').val();
//            data.visible = $('#formGeneral input[name="visible"]').attr('checked') ? 1 : 0;
//            data.createdOn = $('#formGeneral input[name="createdOn"]').val();
//            data.lastModified = $('#formGeneral input[name="lastModified"]').val();
//
//            data.pageTitle = $('#formSEO input[name="pageTitle"]').val();
//            data.keywords = $('#formSEO textarea[name="keywords"]').val();
//            data.description = $('#formSEO textarea[name="description"]').val();
//            data.url = $('#formSEO input[name="url"]').val();
//            data.type = $('#formAdvanced input:checked[name="type"]').val();
//            data.redirectURL = $('#formAdvanced input[name="redirectURL"]').val();
//            data.rss = $('#formAdvanced input[name="rss"]').attr('checked') ? 1 : 0;
//
//            return data;
//        }
        
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