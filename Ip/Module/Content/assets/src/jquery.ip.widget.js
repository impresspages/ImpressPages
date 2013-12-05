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
                $this.prepend(options.widgetControlls);
                $this.save = function(data, refresh){$(this).ipWidget('save', data, refresh);};
                var data = $this.data('ipWidgetInit');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipWidgetInit', Object());

                    var widgetName = $this.data('widgetname');
                    var data = $this.data('widgetdata');
                    if (eval("typeof IpWidget_" + widgetName + " == 'function'")) {
                        var widgetPluginObject;
                        eval('widgetPluginObject = new IpWidget_' + widgetName + '();');
                        if (widgetPluginObject.init) {
                            widgetPluginObject.init($this, data);
                        }

                    }
                }

                $this.find('.ipsLook').on('click', $.proxy(openLayoutModal, this));

            });
        },



        save: function (widgetData, refresh) {

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
                    success: function(response) {
                        if (!refresh) {
                            return;
                        }
                        var $newWidget = $(response.previewHtml);
                        $($newWidget).insertAfter($this);
                        $newWidget.trigger('reinitRequired.ipWidget');

                        // init any new blocks the widget may have created
                        $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                        $this.remove();
                    },
                    error: function(response) {
                        console.log(response);
                    },
                    dataType: 'json'
                });

            });
        },


        changeLayout: function(layout) {
            alert(layout);
        },

        refresh: function (widgetData) {
            return this.each(function () {

            });
        }

    };

    var openLayoutModal = function(e) {
        e.preventDefault();
        var $this = $(this);
        var $layoutButton = $this.find('.ipsLook');
        var layouts = $layoutButton.data('layouts');
        var currentLayout = $layoutButton.data('currentlayout');

        var $modal = $('#ipWidgetLayoutPopup');

        $modal.ipLayoutModal({
            layouts: layouts,
            currentLayout: currentLayout,
            changeCallback: function(layout){
                $(this).ipWidget('changeLayout', layout);
            },
            widgetObject: this
        })
    }

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


