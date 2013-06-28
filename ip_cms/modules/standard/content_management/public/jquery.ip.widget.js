/**
 * @package ImpressPages
 *
 *
 */

"use strict";

(function($) {
    var IP_WIDGET_STATE_MANAGEMENT = 'management';
    var IP_WIDGET_STATE_SAVE_PROGRESS = 'save_progress';
    var IP_WIDGET_STATE_PREVIEW = 'preview';
    var IP_WIDGET_STATE_WAITING_MANAGEMENT = 'aquiring_management';
    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);
            var data = $this.data('ipWidget');
            // If the plugin hasn't been initialized yet
            if (!data) {
                // initialize data array
                var data = Object();
                
                $this.prepend(options.widgetControlls);
                
                //parse widget record data
                var instanceDataInput = $this.find('.ipAdminWidgetData');
                if (instanceDataInput){
                    data = $.parseJSON(instanceDataInput.val());
                    if (!data) {
                        data = Object();
                    }
                    
                    if (!data.data) {
                        data.data = new Array(); //widgets don't need to worry if data variable is null or not. It is always an array
                    }
                }else {
                    data = new Array();
                    data.data = new Array();  //widgets don't need to worry if data variable is null or not. It is always an array
                }
                
                if ($this.hasClass('ipAdminWidget')) {
                    data.state = IP_WIDGET_STATE_MANAGEMENT;
                } else {
                    data.state = IP_WIDGET_STATE_PREVIEW;
                }
                
                $this.data('ipWidget', data);

                if (data.state == IP_WIDGET_STATE_MANAGEMENT) {
                    var widgetName = data.name;
                    if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                        var $content = $this.find('.ipaBody');
                        var widgetPluginObject;
                        eval('widgetPluginObject = new IpWidget_' + widgetName + '($this, $content);');
                        data = $this.data('ipWidget');
                        data.state = IP_WIDGET_STATE_MANAGEMENT;
                        $this.data('ipWidget', data);
                        widgetPluginObject.manageInit();
                    }
                }
                
                
                // mange action
                $this.delegate('.ipWidget .ipActionWidgetManage', 'click', function(event) {
                    event.preventDefault();
                    $(this).trigger('manageClick.ipWidget');
                });
                $this.bind('manageClick.ipWidget', function(event) {
                    $(this).ipWidget('manage');
                });

                // save acion
                $this.delegate('.ipWidget .ipActionWidgetSave', 'click', function(event) {
                    event.preventDefault();
                    $(this).trigger('saveWidget.ipWidget');
                });
                $this.bind('saveWidget.ipWidget', function(event) {
                    $(this).ipWidget('save');
                });

                // cancel acion
                $this.delegate('.ipWidget .ipActionWidgetCancel', 'click', function(event) {
                    event.preventDefault();
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

    //return all instances that are in management state
    fetchManaged : function () {
        var answer = new Array();
        this.each(function() {
            if ($(this).data('ipWidget')) { //if we are browsing older revision, widget might be not initialized
                if ( $(this).data('ipWidget').state == IP_WIDGET_STATE_MANAGEMENT){
                    answer.push($(this));
                }
            }
        });
        return $(answer);
    },


    managementState : function() {
        return $(this).data('ipWidget').state == IP_WIDGET_STATE_MANAGEMENT;
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


            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];
            $.ajax( {
            type : 'POST',
            url : postUrl,
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
                var $newWidget = $(response.managementHtml);
                $newWidget.insertAfter($this);
                $this.remove();
                $newWidget.trigger('reinitRequired.ipWidget');
                $newWidget.trigger('stateManagement.ipWidget',{
                    'instanceId': response.newInstanceId
                });
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
            
            
            var widgetName = $this.data('ipWidget').name;
            if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                var saveJob = new ipSaveJob(widgetName, 1);
                $this.trigger('addSaveJob.ipContentManagement', ['widget_' + $(this).data('ipWidget').instanceId, saveJob]);
                
                var $content = $this.find('.ipaBody');

                var widgetPluginObject;
                eval('widgetPluginObject = new IpWidget_' + widgetName + '($this, $content);');
                $this.data('ipWidget').status = IP_WIDGET_STATE_SAVE_PROGRESS;
                widgetPluginObject.prepareData();
            } else {
                var widgetInputs = $this.find('.ipaBody').find(':input');
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
            data.layout = $this.find('.ipaLayouts').val();

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];
            $.ajax( {
            type : 'POST',
            url : postUrl,
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
            var $newWidget = $(response.previewHtml);
            $($newWidget).insertAfter($this);
            $newWidget.trigger('reinitRequired.ipWidget');
            $newWidget.trigger('statePreview.ipWidget',{
                'instanceId': response.instanceId
            });
            
            var tmpData = $newWidget.data('ipWidget');
            tmpData.state = IP_WIDGET_STATE_PREVIEW;
            $newWidget.data('ipWidget', tmpData);
            
            var instanceId = $(this).data('ipWidget').instanceId; 
            $this.trigger('removeSaveJob.ipContentManagement', ['widget_' + instanceId]);
            $this.remove();
            
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

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];
            $.ajax( {
                type : 'POST',
                url : postUrl,
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
            if (response.status == 'success') {
                if (response.oldInstanceId){
                    var $newWidget = $(response.previewHtml);
                    $($newWidget).insertAfter($this);
                    $newWidget.trigger('reinitRequired.ipWidget');
                    $newWidget.trigger('statePreview.ipWidget',{
                        'instanceId': response.oldInstanceId
                    });
                    //change state to preview
                    var tmpData = $newWidget.data('ipWidget');
                    tmpData.state = IP_WIDGET_STATE_PREVIEW;
                    $newWidget.data('ipWidget', tmpData);
                } else {
                    $this.trigger('deleteWidget.ipBlock', {
                        'instanceId': response.instanceId
                    });
                }
                var $block = $this.parent('.ipBlock');
                $this.remove();
                if ($block.children('.ipWidget').length == 0) {
                    $block.addClass('ipbEmpty');
                }
            } else {
                //do nothing
            }
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