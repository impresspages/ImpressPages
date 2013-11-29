/**
 * @package ImpressPages
 *
 *
 */


(function ($) {
    "use strict";

    var methods = {
        init: function (options) {

            return this.each(function () {

                var $this = $(this);
                var data = $this.data('ipWidgetInit');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipWidgetInit', Object());

                    var widgetName = $this.data('widgetname');
                    if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                        var widgetPluginObject;
                        eval('widgetPluginObject = new IpWidget_' + widgetName + '($this);');
                        if (widgetPluginObject.init) {
                            widgetPluginObject.init();
                        }

                        var widgetContext = this;
//                        $this.on('focusin', function() {
//                            console.log('Widget focusIn: ' + $this.data('widgetname'));
//                            var autosaveInterval = setInterval($.proxy(function() {$(this).ipWidget('save')}, widgetContext), 1000);
//                            $this.data('widgetautosaveinterval', autosaveInterval);
//                            if (widgetPluginObject.focusIn) {
//                                $.proxy(widgetPluginObject.focusIn, widgetPluginObject)
//                            }
//                        });
//                        $this.on('focusout', function() {
//                            console.log('Widget focusOut: ' + $this.data('widgetname'));
//                            clearInterval($this.data('widgetautosaveinterval'));
//                            if (widgetPluginObject.focusOut) {
//                                $.proxy(widgetPluginObject.focusOut, widgetPluginObject)
//                            }
//                        });
                    }
                }
            });
        },


        save: function () {
            return this.each(function () {
                console.log('save init');
                var $this = $(this);
                var widgetName = $this.data('widgetname');
                var widgetPluginObject = null;
                eval('widgetPluginObject = new IpWidget_' + widgetName + '($this);');
                var newData = widgetPluginObject.getSaveData();
                var curData = $this.data('widgetdata');
                if (JSON.stringify(newData) != JSON.stringify(curData)) {
                    console.log('Save widget data ' + JSON.stringify(newData));
                    curData = newData;
                    $this.data('widgetdata', newData);
                    $(this).ipWidget('_saveData', newData);
                }
            });
        },


        _saveData: function (widgetData) {

            return this.each(function () {
                var $this = $(this);
                var data = Object();
                data.aa = 'Content.updateWidget';
                data.securityToken = ip.securityToken;
                data.instanceId = $this.data('widgetinstanceid');
                data.widgetData = widgetData;
                data.layout = 'default'; //TODOX reimplement layouts$this.find('.ipaLayouts').val();

                $.ajax({
                    type: 'POST',
                    url: ip.baseUrl,
                    data: data,
                    context: $this,
                    success: function() {
                        //do nothing
                    },
                    error: function(response) {
                        console.log(response);
                    },
                    dataType: 'json'
                });

            });
        }



    };

    $.fn.ipWidget = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };

})(jQuery);