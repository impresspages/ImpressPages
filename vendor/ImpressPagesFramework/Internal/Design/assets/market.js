var ipDesignThemeMarket = new function () {
    "use strict";
    var isThemePreview = false;
    var bodyClassToHideScroll = 'modal-open';

    var processOrder = function (order) {
        $('body').bind('ipMarketOrderComplete', function (e, data) {
            ipDesignThemeMarket.closeMarketWindow();
            window.location = window.location.href.split('#')[0];
        });

        Market.processOrder(order);
    };

    var navigateBackToMyTheme = function () {
        ipDesignThemeMarket.closeMarketWindow();
    };

    var beforeOpenThemePreview = function () {
        isThemePreview = true;
        ipDesignThemeMarket.resize();
    };

    var afterCloseThemePreview = function () {
        isThemePreview = false;
        ipDesignThemeMarket.resize();
    };

    var showMarketIframe = function () {

        var remote = new easyXDM.Rpc({
                remote: $('#ipsModuleThemeMarketContainer').data('marketurl'),
                container: "ipsModuleThemeMarketContainer",
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
                            case 'installTheme':
                                $('body').bind('ipMarketOrderComplete', function (e, data) {
                                    location.reload();
                                });
                                var fakeOrder = {
                                    images: [],
                                    themes: [data]
                                };
                                processOrder(fakeOrder);
                                break;
                            case 'processOrder':
                                processOrder(data);
                                break;
                            case 'navigateBackToMyTheme':
                                navigateBackToMyTheme();
                                break;
                            case 'beforeOpenThemePreview':
                                beforeOpenThemePreview();
                                break;
                            case 'afterCloseThemePreview':
                                afterCloseThemePreview();
                                break;
                            case 'closeThemeMarket':
                                ipDesignThemeMarket.closeMarketWindow();
                                break;
                        }
                    }
                }
            }
        );
    };

    /**
     * Event to handle ESC to close ThemeMarket window
     */
    var onMarketKeyUp = function (e) {
        if (e.keyCode == 27) { // ESC pressed
            ipDesignThemeMarket.closeMarketWindow();
        }
    };

    this.openMarketWindow = function (e) {
        e.preventDefault();

        var $popup = $('.ipModuleDesign .ipsThemeMarketPopup');

        $(document.body).addClass(bodyClassToHideScroll);
        $popup.removeClass('hidden');
        showMarketIframe();
        ipDesignThemeMarket.resize();
        $(window).bind('resize.ipThemeMarketAll', ipDesignThemeMarket.resize);

        $(document).on('keyup', onMarketKeyUp);
    };

    this.closeMarketWindow = function (e) {

        if (e != null) {
            e.preventDefault();
        }

        $(document).off('keyup', onMarketKeyUp);

        var $popup = $('.ipModuleDesign .ipsThemeMarketPopup');
        $popup.addClass('hidden');

        $('#ipsModuleThemeMarketContainer').find('iframe').remove();

        if(!$('.modal[aria-hidden=false]').length) {
            $(document.body).removeClass(bodyClassToHideScroll);
        }
    };

    this.resize = function (e) {
        var $popup = $('.ipsThemeMarketPopup');
        var height = parseInt($(window).height());
        height -= 40; // leaving place for navbar
        if (isThemePreview) {
            // do noting
        }
        $popup.height(height + 'px');
    };
};
