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
                $this.save = function(data, refresh, callback){$(this).ipWidget('save', data, refresh, callback);};
                var data = $this.data('ipWidgetInit');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipWidgetInit', Object());

                    var widgetName = $this.data('widgetname');
                    var data = $this.data('widgetdata');
                    var functionClass = 'IpWidget_' + widgetName;
                    if (typeof(window[functionClass]) == 'function') {
                        var widgetController;
                        widgetController = new window[functionClass];
                        if (widgetController.init) {
                            widgetController.init($this, data);
                        }
                        $this.data('widgetController', widgetController);
                    }
                }

                $this.find('.ipsLook').on('click', $.proxy(openLayoutModal, this));

            });
        },


        widgetController: function () {
            return this.data('widgetController');
        },

        save: function (widgetData, refresh, callback) {

            return this.each(function () {
                var $this = $(this);

                //add to queue
                var $queue = $this.data('saveQueue');
                if ($queue == null) {
                    $queue = [];
                }
                $queue.push({widgetData: widgetData, refresh: refresh, callback: callback});
                $this.data('saveQueue', $queue);

                if ($this.data('saveInProgress')) {
                    return;
                } else {
                    $.proxy(processSaveQueue, $this)();
                }

            });
        },


        changeLook: function(look) {
            return this.each(function () {
                var $this = $(this);
                var data = Object();


                data.aa = 'Content.changeLook';
                data.securityToken = ip.securityToken;
                data.instanceId = $this.data('widgetinstanceid');
                data.look = look;

                $.ajax({
                    type: 'POST',
                    url: ip.baseUrl,
                    data: data,
                    context: $this,
                    success: function(response) {
                        var $newWidget = $(response.html);
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
        }

    };


    var processSaveQueue = function() {
        var $this = this;

        if ($this.data('saveInProgress')) {
            return;
        } else {
            $this.data('saveInProgress', true);
        }

        var $queue = $this.data('saveQueue');
        $this.data('saveQueue', []);
        if ($queue == null || $queue.length == 0) {
            $this.data('saveInProgress', false);
            return;
        }


        var refresh = false;
        var callbacks = [];
        $.each($queue, function(key, value) {
            if (value.refresh) {
                refresh = true;
            }
            if (value.callback) {
                callbacks.push(value.callback);
            }
        });

        var data = Object();
        data.aa = 'Content.updateWidget';
        data.securityToken = ip.securityToken;
        data.instanceId = $this.data('widgetinstanceid');
        data.widgetData = $queue[$queue.length - 1].widgetData;

        if (refresh) {
            data.generatePreview = 1
        }

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: $this,
            success: function(response) {
                var $this = this;

                if (refresh) {
                    var newWidget = response.html;
                    var $newWidget = $(newWidget);
                    $newWidget.insertAfter($this);
                    $newWidget.trigger('reinitRequired.ipWidget');

                    // init any new blocks the widget may have created
                    $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                    $this.remove();
                }

                if (callbacks.length) {
                    if (refresh) {
                        $.each(callbacks, function(key, value){
                            value($newWidget);
                        });
                    } else {
                        $.each(callbacks, function(key, value){
                            value($this);
                        });
                    }
                }
                $this.data('saveInProgress', false);
                $.proxy(processSaveQueue, $this)();
            },
            error: function(response) {
                console.log('save error');
                console.log(response);
                $this.data('saveInProgress', false);
                $.proxy(processSaveQueue, $this)();
            },
            dataType: 'json'
        });
    }


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
            widgetObject: $this
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

})(ip.jQuery);
