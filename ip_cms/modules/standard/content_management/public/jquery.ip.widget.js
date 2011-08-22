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
                // initialize data array
                var data = Object();
                
                //parse widget record data
                $this.find('.ipWidgetData input').each(function() {
                    data[$(this).attr('name')] = $(this).val();
                });
                data.state = 'preview'; // possible values: preview, management
                data.widgetControlsHtml = options.widgetControlsHtml;
                $this.data('ipWidget', data);

                // mange action
                $this.delegate('.ipWidget .ipWidgetManage', 'click', function(event) {
                    $(this).trigger('manageClick.ipWidget');
                });
                $this.bind('manageClick.ipWidget', function(event) {
                    $(this).ipWidget('manage');
                });

                // save acion
                $this.delegate('.ipWidget .ipWidgetSave', 'click', function(event) {
                    $(this).trigger('saveWidget.ipWidget');
                });
                $this.bind('saveWidget.ipWidget', function(event) {
                    $(this).ipWidget('save');
                });


                $this.bind('preparedWidgetData.ipWidget', function(event, widgetData) {
                    $(this).ipWidget('_saveData', widgetData);
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



    save : function() {
        return this.each(function() {
            console.log('widget save');
            $this = $(this);
            widgetName = $this.data('ipWidget').name;
            if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
                eval('var widgetPluginObject = new ipWidget_' + widgetName + '($this);');
                $this.data('ipWidget').status = 'test';
                console.log($this.data('ipWidget').status);
                widgetPluginObject.prepareData();
            } else {
                $this.ipWidget('preview');
            }

        });
    },

    _saveData : function(widgetData) {

        return this.each(function() {
            $this = $(this);
            console.log(widgetData);
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'updateWidget';
            data.widgetId = $this.data('ipWidget').id;
            data.widgetData = widgetData;
            console.log(widgetData);

            $.ajax( {
            type : 'POST',
            url : ipBaseUrl,
            data : data,
            context : $this,
            success : methods._saveDataResponse,
            dataType : 'json'
            });

        });
    },

    _saveDataResponse : function(response) {
        return this.each(function() {
            $this = $(this);
            $this.ipWidget('_replaceContent', response.previewHtml);
        });
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
            console.log('init management');
            $this = $(this);
            $this.prepend($this.data('ipWidget').widgetControlsHtml)
        });
    }

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