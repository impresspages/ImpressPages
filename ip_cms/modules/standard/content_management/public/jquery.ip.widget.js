/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

(function($) {
    IP_WIDGET_STATE_MANAGEMENT = 'management';
    IP_WIDGET_STATE_SAVE_PROGRESS = 'save_progress';
    IP_WIDGET_STATE_PREVIEW = 'preview';
    IP_WIDGET_STATE_WAITING_MANAGEMENT = 'aquiring_management';
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
                var instanceDataInput = $this.find('.ipWidgetData input')
                if (instanceDataInput){
                    data = $.parseJSON(instanceDataInput.val());
                    
                    if (!data.data) {
                        data.data = new Array(); //widgets don't need to worry if data variable is null or not. It is always an array
                    }                    
                }else {
                    data = new Array();
                    data.data = new Array();  //widgets don't need to worry if data variable is null or not. It is always an array
                }
                
                if ($this.hasClass('ipWidgetManagement')) {
                    data.state = IP_WIDGET_STATE_MANAGEMENT;
                } else {
                    data.state = IP_WIDGET_STATE_PREVIEW;
                }
                
                $this.data('ipWidget', data);

                if (data.state == IP_WIDGET_STATE_MANAGEMENT) {
                    widgetName = data.name;
                    if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
                        eval('var widgetPluginObject = new ipWidget_' + widgetName + '($this);');
                        data = $this.data('ipWidget');
                        data.state = IP_WIDGET_STATE_MANAGEMENT;
                        $this.data('ipWidget', data);
                        widgetPluginObject.manageInit();
                    }
                }
                
                
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
                    console.log(this);
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
                
                $this.bind('saveProgress.ipWidget', function(event, progress, timeLeft) {
                    $(this).ipWidget('_saveProgress', progress, timeLeft);
                });
                
            }
        });
    },

    //return all instances that are in mangement state
    fetchManaged : function () {
        var answer = new Array();
        this.each(function() {
            console.log('STATE: ' + $(this).data('ipWidget').state);
            if ($(this).data('ipWidget').state == IP_WIDGET_STATE_MANAGEMENT){
                answer.push($(this));
            }
        });
        return $(answer);
    },
    
    manage : function() {
        return this.each(function() {
            
            
            var $this = $(this);
            
            if ($this.data('ipWidget').state != IP_WIDGET_STATE_PREVIEW) {
                return;
            }   
            
            var tmpData = $this.data('ipWidget');
            tmpData.state = IP_WIDGET_STATE_WAITING_MANAGEMENT;
            $this.data('ipWidget', tmpData);
            
            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'manageWidget';
            data.instanceId = $this.data('ipWidget').instanceId;
            


            $.ajax( {
            type : 'POST',
            url : ip.baseUrl,
            data : data,
            context : $this,
            success : methods._manageWidgetResponse,
            dataType : 'json'
            });
        });
    },

    _manageWidgetResponse : function(response) {
        return this.each(function() {
            var $this = $(this);
            if (response.status == 'success') {
                $newWidget = $(response.managementHtml); 
                $newWidget.insertAfter($this);
                $newWidget.trigger('reinitRequired.ipWidget');

                $this.remove();
                
//                //change state to managed
//                var tmpData = $newWidget.data('ipWidget');
//                tmpData.state = IP_WIDGET_STATE_MANAGEMENT;
//                $newWidget.data('ipWidget', tmpData);
//                
//
//                
//                
//                widgetName = $($newWidget).data('ipWidget').name;
//                if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
//                    eval('var widgetPluginObject = new ipWidget_' + widgetName + '($newWidget);');
//                    $($newWidget).data('ipWidget').status = IP_WIDGET_STATE_MANAGEMENT;
//                    widgetPluginObject.manageInit();
//                }
                
            } else {
                alert(response.errorMessage);
                var tmpData = $this.data('ipWidget');
                tmpData.state = IP_WIDGET_STATE_PREVIEW;
                $this.data('ipWidget', tmpData);                
            }
        });
    },





    save : function() {
        return this.each(function() {
            var $this = $(this);
            
            if ($this.data('ipWidget').state != IP_WIDGET_STATE_MANAGEMENT) {
                return;
            }
            
            
            widgetName = $this.data('ipWidget').name;
            if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
                var saveJob = new ipSaveJob(widgetName, 1);
                $this.trigger('addSaveJob.ipContentManagement', ['widget_' + $(this).data('ipWidget').instanceId, saveJob]);
                
                
                eval('var widgetPluginObject = new ipWidget_' + widgetName + '($this);');
                $this.data('ipWidget').status = IP_WIDGET_STATE_SAVE_PROGRESS;
                widgetPluginObject.prepareData();
            } else {
                var widgetInputs = $this.find('.ipWidgetManagementBody').find(':input');
                var data = Object();
                widgetInputs.each(function(index) {
                    data[$(this).attr('name')] = $(this).val();
                }); 
                $this.ipWidget('_saveData', data);
            }

        });
    },

    _saveProgress : function(progress, timeLeft) {

        return this.each(function() {
            var $this = $(this);
            var saveJob = new ipSaveJob(widgetName, timeLeft + 1);
            saveJob.setProgress(progress);
            $this.trigger('addSaveJob.ipContentManagement', ['widget_' + $(this).data('ipWidget').instanceId, saveJob]);
        });
    },    
    
    _saveData : function(widgetData) {

        return this.each(function() {
            var $this = $(this);
            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'updateWidget';
            data.instanceId = $this.data('ipWidget').instanceId;
            data.widgetData = widgetData;
            data.layout = $this.find('.ipWidgetLayoutSelect').val();

            $.ajax( {
            type : 'POST',
            url : ip.baseUrl,
            data : data,
            context : $this,
            success : methods._saveDataResponse,
            dataType : 'json'
            });

        });
    },

    _saveDataResponse : function(response) {
        return this.each(function() {
            var $this = $(this);
            $newWidget = $(response.previewHtml); 
            $($newWidget).insertAfter($this);
            $newWidget.trigger('reinitRequired.ipWidget');
            
            //change state to managed
            var tmpData = $newWidget.data('ipWidget');
            tmpData.state = IP_WIDGET_STATE_PREVIEW;
            $newWidget.data('ipWidget', tmpData);            
            
            var instanceId = $(this).data('ipWidget').instanceId; 
            $this.remove();
            
            $this.trigger('removeSaveJob.ipContentManagement', ['widget_' + instanceId]);
            
        });
    },

    cancel : function() {
        return this.each(function() {
            var $this = $(this);
            
            if ($this.data('ipWidget').state != IP_WIDGET_STATE_MANAGEMENT) {
                return;
            }

            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'cancelWidget';
            data.instanceId = $this.data('ipWidget').instanceId;

            $.ajax( {
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : methods._cancelResponse,
                dataType : 'json'
            });

        });
    },

    _cancelResponse : function(response) {
        return this.each(function() {
            var $this = $(this);
            $newWidget = $(response.previewHtml); 
            $($newWidget).insertAfter($this);
            $newWidget.trigger('reinitRequired.ipWidget');
            
            //change state to managed
            var tmpData = $newWidget.data('ipWidget');
            tmpData.state = IP_WIDGET_STATE_PREVIEW;
            $newWidget.data('ipWidget', tmpData);

            
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