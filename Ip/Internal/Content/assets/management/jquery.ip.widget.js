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
                    $this.prepend(options.widgetControlls);
                    $this.save = function (data, refresh, callback) {
                        $(this).ipWidget('save', data, refresh, callback);
                    };
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
                    } else {
                        $this.data('widgetController', {});
                    }

                }

                var $widgetControls = $this.find(' > .ip > .ipsWidgetControls');

                // binding delete action
                $widgetControls.find('.ipsWidgetDelete').on('click', function (e) {
                    e.preventDefault();
                    ipContent.deleteWidget($this.data('widgetid'));
                });

                // binding z-index fix for open dropdown
                $widgetControls.find('.ipsControls')
                    .on('shown.bs.dropdown', function () {
                        // increase z-index on .ipsWidgetControls
                        $widgetControls.css('z-index', ($widgetControls.zIndex() + 11));
                    })
                    .on('hidden.bs.dropdown', function () {
                        $widgetControls.css('z-index', '');
                    });

                // binding skin change action
                $widgetControls.find('.ipsSkin').on('click', $.proxy(openLayoutModal, this));

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


        changeSkin: function (skin) {
            return this.each(function () {
                var $this = $(this);
                var data = Object();


                data.aa = 'Content.changeSkin';
                data.securityToken = ip.securityToken;
                data.widgetId = $this.data('widgetid');
                data.skin = skin;

                $.ajax({
                    type: 'POST',
                    url: ip.baseUrl,
                    data: data,
                    context: $this,
                    success: function (response) {
                        var $newWidget = $(response.html);
                        $($newWidget).insertAfter($this);
                        $newWidget.trigger('ipWidgetReinit');

                        // init any new blocks the widget may have created
                        $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                        $this.remove();
                    },
                    error: function (response) {
                        alert('Error. ' + response.responseText);
                    },
                    dataType: 'json'
                });

            });
        }

    };


    var processSaveQueue = function () {
        var $this = this;
        var $widget = $this;

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
        $.each($queue, function (key, value) {
            if (value.refresh) {
                refresh = true;
            }
            if (value.callback) {
                callbacks.push(value.callback);
            }
        });

        ipContent.updateWidget($this.data('widgetid'), $queue[$queue.length - 1].widgetData, refresh, function (newInstanceId) {
            var $this = $widget;


            var $newWidget = $('#ipWidget-' + newInstanceId);

            if (callbacks.length) {
                if (refresh) {
                    $.each(callbacks, function (key, value) {
                        value($newWidget);
                    });
                } else {
                    $.each(callbacks, function (key, value) {
                        value($newWidget);
                    });
                }
            }
            $this.data('saveInProgress', false);
            $.proxy(processSaveQueue, $this)();

        });


    };


    var openLayoutModal = function (e) {
        e.preventDefault();
        var $this = $(this);
        var $skinButton = $this.find('.ipsSkin');
        var skins = $skinButton.data('skins');
        var currentSkin = $skinButton.data('currentskin');

        var $modal = $('#ipWidgetLayoutPopup');

        $modal.ipSkinModal({
            layouts: skins,
            currentLayout: currentSkin,
            widgetObject: $this
        })
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
