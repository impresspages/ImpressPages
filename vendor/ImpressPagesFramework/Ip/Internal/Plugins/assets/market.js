var ipPluginMarket = new function () {
    "use strict";
    var isPluginPreview = false;
    var bodyClassToHideScroll = 'modal-open';
    var redirectUrl = '';

    var processOrder = function (order) {
        $('body').bind('ipMarketOrderComplete', function (e, data) {
            if (redirectUrl) {
                window.location = redirectUrl;
            } else {
                window.location = ip.baseUrl + '?aa=Plugins.index';
            }
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

                                var installPlugin = function (data) {
                                    $('body').bind('ipMarketOrderComplete', function (e, data) {
                                        location.reload();
                                    });
                                    var fakeOrder = {
                                        images: [],
                                        plugins: [data]
                                    };
                                    processOrder(fakeOrder);
                                };


                                redirectUrl = ip.baseUrl + '?aa=Plugins.index#/hash=&plugin=' + encodeURIComponent(data.name);

                                installPlugin(data);

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

$(document).ready(function () {
    ipPluginMarket.openMarketWindow();
});
