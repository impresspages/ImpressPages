/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

(function($) {

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);
            var data = $this.data('ipWidget');
            // If the plugin hasn't been initialized yet
            if (!data) {
                var data = Object();
                $this.find('.ipWidgetData input').each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });
                data.state = 'preview'; // possible values: preview, management
                data.widgetControlsHtml = options.widgetControlsHtml;
                $this.data('ipWidget', data);

                $this.delegate('.ipWidget .ipWidgetManage', 'click', function(event) {
                    $(this).trigger('manageClick.ipWidget');
                });
                $this.bind('manageClick.ipWidget', function(event) {
                    $(this).ipWidget('manage');
                });
                
                
                
                
                $this.delegate('.ipWidget .ipWidgetSave', 'click', function(event) {
                    $(this).trigger('saveClick.ipWidget');
                });
                $this.bind('saveWidget.ipBlock', function(event) {
                    $(this).ipBlock('save');
                });
            }
        });
    },

    manage : function() {
        return this.each(function() {
            $this = $(this);
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'manageWidget';
            data.widgetId = $this.data('ipWidget').id;

            $.ajax( {
            type : 'POST',
            url : ipBaseUrl,
            data : data,
            context : $this,
            success : methods._manageWidgetResponse,
            dataType : 'json'
            });
        });
    },

    _manageWidgetResponse : function(response) {
        return this.each(function() {
            $this = $(this);
            $this.ipWidget('_replaceContent', response.managementHtml);
        });
    },

    fetchManaged : function() {
        // $answer =
        // return this.each(function() {
        // $block = $this.parent();
        // $this.replaceWith(response.previewHtml);
        // console.log('preview');
        // });

    },

    _replaceContent : function(newContent) {
        return this.each(function() {
            $this = $(this);
            $this.html(newContent);
            $this.ipWidget('_initManagement');
        });

    },

    _initManagement : function() {
        return this.each(function() {
            $this = $(this);
            $this.prepend($this.data('ipWidget').widgetControlsHtml)
        });
    }

    // 2011-08-08
    // preview : function () {
    // console.log('preview2');
    // return this.each(function() {
    // $this = $(this);
    //
    // data = Object();
    // data.g = 'standard';
    // data.m = 'content_management';
    // data.a = 'previewWidget';
    // data.widgetId = $this.data('ipWidget').id;
    //	        
    // $.ajax({
    // type : 'POST',
    // url : ipBaseUrl,
    // data : data,
    // context : $this,
    // success : methods._previewResponse,
    // dataType : 'json'
    // });
    //	        	
    // alert($this.data('ipWidget').id);
    // });
    // },
    //        
    // _previewResponse : function(response) {
    //
    // return this.each(function() {
    // $block = $this.parent();
    // $this.replaceWith(response.previewHtml);
    // console.log('preview');
    // });
    // }

    };

    $.fn.ipWidget = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };

})(jQuery);