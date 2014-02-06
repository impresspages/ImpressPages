var ipDesignThemeMarket;


(function ($) {
    "use strict";
    ipDesignThemeMarket = new function () {

        /**
         * @see this.openMarketWindow(), this.closeMarketWindow()
         */
        var adminFramesetRows = 0;
        var isThemePreview = false;

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

        var beforeOpenThemePreview = function() {
            isThemePreview = true;
            $('.ipsThemeMarketPopup').addClass('ipmPreviewOpen');
            ipDesignThemeMarket.resize();
        };

        var afterCloseThemePreview = function() {
            isThemePreview = false;
            $('.ipsThemeMarketPopup').removeClass('ipmPreviewOpen');
            ipDesignThemeMarket.resize();
        };

        var showMarketIframe = function () {

            var remote = new easyXDM.Rpc({
                    remote: $('#ipModuleThemeMarketContainer').data('marketurl'),
                    container: "ipModuleThemeMarketContainer",
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
                                    var fakeOrder = {
                                        images: [],
                                        themes: [data]
                                    };
                                    processOrder(fakeOrder);
                                    $('body').bind('ipMarketOrderComplete', function (e, data) {
                                        if (top.document.getElementById('adminFrameset')) {
                                            top.document.getElementById('adminFrameset').rows = adminFramesetRows;
                                        }
                                        location.reload();
                                    });
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

        this.openMarketWindow = function () {

            var $popup = $('.ipModuleDesign .ipsThemeMarketPopup');

            $('body').addClass('ipgStopScrolling');
            $popup.show();
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
            $popup.hide();

            if (top.document.getElementById('adminFrameset')) {
                top.document.getElementById('adminFrameset').rows = adminFramesetRows;
            }

            $('#ipModuleThemeMarketContainer iframe').remove();

            $('body').removeClass('ipgStopScrolling');
        };

        this.resize = function(e) {
            var $popup = $('#ipModuleThemeMarketContainer');
            var height = parseInt($(window).height());
            if (!isThemePreview) { height -= 40; } // leaving place for tabs
            $popup.find('iframe').height(height + 'px');
        };
    };
})(ip.jQuery);
