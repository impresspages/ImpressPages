var ipDesignThemeMarket = new function () {

    /**
     * @see this.openMarketWindow(), this.closeMarketWindow()
     */
    var adminFramesetRows = 0;

    /**
     * @see showMartketingIframe()
     */
    var isIframeCreated = false;

    var processOrder = function (order) {
        console.log('processOrder');
        $('body').bind('ipMarketOrderStart', function (e) {
            console.log('order start');
        });

        console.log('bind complete event');
        $('body').bind('ipMarketOrderComplete', function (e, data) {
            console.log('order complete ');
            console.log(data);
            if (typeof(data.themes) != "undefined" && data.themes.length) {
                //TODOX
                console.log('show local themes');
            }
        });

        Market.processOrder(order);
    };

    var navigateBackToMyTheme = function () {
        ipDesignThemeMarket.closeMarketWindow();
    };


    var showMarketIframe = function () {

        if (isIframeCreated) {
            return;
        }

        var remote = new easyXDM.Rpc({
                remote: $('#ipsThemeMarketContainer').data('marketurl'),
                container: "ipsThemeMarketContainer",
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
                        console.log('handle(' + action + ')');
                        switch (action) {
                            case 'processOrder':
                                processOrder(data);
                                break;
                            case 'navigateBackToMyTheme':
                                navigateBackToMyTheme();
                                break;
                        }
                    }
                }
            }
        );

        isIframeCreated = true;
    };


    this.openMarketWindow = function () {

        var $popup = $('.ipModuleDesign .ipsThemeMarketPopup');
        $popup.css('top', $(document).scrollTop() + 'px');
        if (top.document.getElementById('adminFrameset')) {
            adminFramesetRows = top.document.getElementById('adminFrameset').rows;
            top.document.getElementById('adminFrameset').rows = "0px,*";
        }


        $popup.show();
        showMarketIframe();
    };

    this.closeMarketWindow = function (e) {

        if (e != null) {
            e.preventDefault();
        }

        var $popup = $('.ipModuleDesign .ipsThemeMarketPopup');
        $popup.hide();

        if (top.document.getElementById('adminFrameset')) {
            top.document.getElementById('adminFrameset').rows = adminFramesetRows;
        }

//        $(document).off('keyup', ipRepositoryESC);
//        $('.ipModuleRepositoryPopup').remove();
//        $('body').removeClass('stopScrolling');
    };
};