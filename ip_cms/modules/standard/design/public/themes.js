//create crossdomain socket connection
var remote = new easyXDM.Rpc({
        remote: $('#ipModuleDesignContainer').data('marketurl'),
        container: "ipModuleDesignContainer",
        onMessage: function(message, origin){
            //DO NOTHING
        },
        onReady: function() {
            //DO NOTHING
        }
    },
    {
        remote: {
        },
        local: {
            downloadImages: function(images){
                //do nothing. Leaving for compatibility with ImpressPages 3.4 and 3.5
            },
            processOrder: function(order){
                console.log('processOrder');
                $('body').bind('ipMarketOrderStart', function(e){
                    console.log('order start');
                });

                console.log('bind complete event');
                $('body').bind('ipMarketOrderComplete', function(e, data){
                    console.log('order complete ');
                    console.log(data);
                    if (typeof(data.themes) != "undefined" && data.themes.length) {
                        //TODOX
                        console.log('show local themes');
                    }
                });


                Market.processOrder(order);

            }
        }
    }

);