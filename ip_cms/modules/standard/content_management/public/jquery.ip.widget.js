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

                // cancel acion
                $this.delegate('.ipWidget .ipWidgetCancel', 'click', function(event) {
                    $(this).trigger('cancelWidget.ipWidget');
                });
                $this.bind('cancelWidget.ipWidget', function(event) {
                    $(this).ipWidget('cancel');
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
            data.instanceId = $this.data('ipWidget').instanceId;

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
        console.log('manage response');
        return this.each(function() {
            $this = $(this);
            if (response.status == 'success') {
                $newWidget = $(response.managementHtml); 
                $($newWidget).insertAfter($this);
                $newWidget.trigger('reinitRequired.ipWidget');                
                $this.remove();
                
                
                widgetName = $($newWidget).data('ipWidget').name;
                console.log('manage init 0');
                if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
                    console.log('manage init 1');
                    eval('var widgetPluginObject = new ipWidget_' + widgetName + '($newWidget);');
                    $($newWidget).data('ipWidget').status = 'management';
                    console.log($($newWidget).data('ipWidget').status);
                    widgetPluginObject.manageInit();
                }              
                
            } else {
                alert(response.errorMessage);
            }
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
            data.instanceId = $this.data('ipWidget').instanceId;
            data.widgetData = widgetData;

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
            $newWidget = $(response.previewHtml); 
            $($newWidget).insertAfter($this);
            $newWidget.trigger('reinitRequired.ipWidget');
            $this.remove();            
        });
    },

    cancel : function() {
        return this.each(function() {
            $this = $(this);

            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'cancelWidget';
            data.instanceId = $this.data('ipWidget').instanceId;

            $.ajax( {
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : methods._cancelResponse,
                dataType : 'json'
            });

        });
    },

    _cancelResponse : function(response) {
        return this.each(function() {
            $this = $(this);
            $newWidget = $(response.previewHtml); 
            $($newWidget).insertAfter($this);
            $newWidget.trigger('reinitRequired.ipWidget');
            $this.remove();            
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