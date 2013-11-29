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
                $this.save = function(data){$(this).ipWidget('save', data);};
                var data = $this.data('ipWidgetInit');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipWidgetInit', Object());

                    var widgetName = $this.data('widgetname');
                    if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                        var widgetPluginObject;
                        eval('widgetPluginObject = new IpWidget_' + widgetName + '();');
                        if (widgetPluginObject.init) {
                            widgetPluginObject.init($this);
                        }

                    }
                }
            });
        },



        save: function (widgetData) {

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


//(function ($) {
//$.fn.save = function (method) {
//    var methods = {
//        init: function (options) {
//
//            return this.each(function () {
//                var $this = $(this);
//                $this.ipWidget('save');
//            });
//        }
//    };
//    if (methods[method]) {
//        return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
//    } else if (typeof method === 'object' || !method) {
//        return methods.init.apply(this, arguments);
//    } else {
//        $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
//    }
//
//};
//
//})(jQuery);