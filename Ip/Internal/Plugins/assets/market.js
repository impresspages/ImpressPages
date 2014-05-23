var ipPluginMarket = new function () {
    "use strict";
    var isPluginPreview = false;
    var bodyClassToHideScroll = 'modal-open';

    var processOrder = function (order) {
        $('body').bind('ipMarketOrderComplete', function (e, data) {
            window.location = window.location.href.split('#')[0];
        });

        Market.processOrder(order);
    };

    var showMarketIframe = function () {

        var remote = new easyXDM.Rpc({
                remote: $('#ipsModulePluginMarketContainer').data('marketurl'),
                container: "ipsModulePluginMarketContainer",
                onMessage: function (message, origin) {
                    //DO NOTHING
                },
                onReady: function () {
                    //DO NOTHING
                }
            },
            {
                remote: {
                },
                local: {
                    downloadImages: function (images) {
                        //do nothing. Leaving for compatibility with ImpressPages 3.4 and 3.5
                    },

                    handle: function (action, data) {
                        switch (action) {
                            case 'installPlugin':
                                console.log('handle: installPlugin:', data);

                                var installPlugin = function (data) {
                                    $('body').bind('ipMarketOrderComplete', function (e, data) {
                                        location.reload();
                                    });
                                    var fakeOrder = {
                                        images: [],
                                        plugins: [data]
                                    };
                                    processOrder(fakeOrder);
                                }

                                $.ajax(ip.baseUrl, {
                                    'type': 'POST',
                                    'data': {'aa': 'Plugins.pluginExists', 'plugin': data.name, 'securityToken': ip.securityToken, 'jsonrpc': '2.0'},
                                    'dataType': 'json',
                                    'success': function (response) {
                                        if (!response || response.error) {

                                            if (response.error.message) {
                                                alert(response.error.message);
                                            } else {
                                                alert('Unknown error. Please see logs.');
                                            }
                                            return;
                                        }

                                        if (response.result === true) {
                                            alert('Plugin "' + data.name + '" already exists.');
                                        } else {
                                            installPlugin(data);
                                        }

                                    },
                                    'error': function () {
                                        alert('Unknown error. Please see logs.');
                                    }
                                });

                                break;
                            case 'processOrder':
                                processOrder(data);
                                break;
                        }
                    }
                }
            }
        );
    };

    this.openMarketWindow = function (e) {
        if (e) {
            e.preventDefault();
        }

        var $popup = $('.ipsPluginMarketPopup');

        $(document.body).addClass(bodyClassToHideScroll);
        $popup.removeClass('hidden');
        showMarketIframe();
        ipPluginMarket.resize();
        $(window).bind('resize.ipPluginMarketAll', ipPluginMarket.resize);
    };

    this.resize = function (e) {
        var $popup = $('.ipsPluginMarketPopup');
        var height = parseInt($(window).height());
        height -= 40; // leaving place for navbar
        if (isPluginPreview) {
            // do noting
        }
        $popup.height(height + 'px');
    };
};

$(document).ready(function() {
    ipPluginMarket.openMarketWindow();
});
