/**
 * @package ImpressPages
 *
 *
 */


(function ($) {
    "use strict";

    var autosaveInterval = null;
    var curData = null;

    var methods = {
        init: function (options) {

            return this.each(function () {

                var $this = $(this);
                var data = $this.data('ipWidget');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    // initialize data array
                    var data = Object();

                    $this.prepend(options.widgetControlls);

                    //parse widget record data
                    var instanceDataInput = $this.find('.ipAdminWidgetData');
                    if (instanceDataInput) {
                        data = $.parseJSON(instanceDataInput.val());
                        if (!data) {
                            data = Object();
                        }

                        if (!data.data) {
                            data.data = new Array(); //widgets don't need to worry if data variable is null or not. It is always an array
                        }
                    } else {
                        data = new Array();
                        data.data = new Array();  //widgets don't need to worry if data variable is null or not. It is always an array
                    }

                    $this.data('ipWidget', data);

                    var widgetName = data.name;
                    if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                        var $content = $this.find('.ipaBody');
                        var widgetPluginObject;
                        eval('widgetPluginObject = new IpWidget_' + widgetName + '($this, $content);');
                        widgetPluginObject.manageInit();

                        var widgetContext = this;
                        if (widgetPluginObject.focusIn) {
                            $this.on('focusin', function() {
                                autosaveInterval = setInterval($.proxy(function() {$(this).ipWidget('save')}, widgetContext), 3000);
                                $.proxy(widgetPluginObject.focusIn, widgetPluginObject)
                            });
                        }
                        if (widgetPluginObject.focusOut) {
                            $this.on('focusout', function() {
                                clearInterval(autosaveInterval);
                                $.proxy(widgetPluginObject.focusOut, widgetPluginObject)
                            });
                        }
                    }
                }
            });
        },


        save: function () {
            return this.each(function () {
                var $this = $(this);

                var widgetName = $this.data('ipWidget').name;
                var widgetPluginObject = null;
                eval('widgetPluginObject = new IpWidget_' + widgetName + '($this);');
                var widgetData = widgetPluginObject.getSaveData();
                $(this).ipWidget('_saveData', widgetData);

            });
        },


        _saveData: function (widgetData) {

            return this.each(function () {
                var $this = $(this);
                var data = Object();
                data.aa = 'Content.updateWidget';
                data.securityToken = ip.securityToken;
                data.instanceId = $this.data('ipWidget').instanceId;
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