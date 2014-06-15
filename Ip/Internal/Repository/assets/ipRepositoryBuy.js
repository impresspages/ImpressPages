/**
 * @package ImpressPages
 *
 */

(function ($) {
    "use strict";

    var methods = {

        init: function (options) {

            return this.each(function () {
                var $this = $(this);
                var buyTab = this;

                var data = $this.data('ipRepositoryBuy');
                if (!data) {
                    $this.data('ipRepositoryBuy', {});

                    var $popup = $('.ipsModuleRepositoryPopup');

                    $(window).bind("resize.ipRepositoryBuy", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));

                    //create crossdomain socket connection
                    var remote = new easyXDM.Rpc({
                            remote: $('#ipsModuleRepositoryTabBuy').data('marketurl'),
                            container: "ipsModuleRepositoryTabBuyContainer",
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
                                            $(document.body).bind('ipMarketOrderComplete', function (e, data) {
                                                location.reload();
                                            });
                                            break;
                                        case 'processOrder':
                                            $(document.body).bind('ipMarketOrderComplete', function (e, data) {
                                                if (typeof (data.images) != "undefined" && data.images.length) {
                                                    $.proxy(methods._confirm, buyTab, data.images)();
                                                } else {
                                                    $.proxy(methods._confirm, buyTab, [])();
                                                }
                                            });

                                            Market.processOrder(data);
                                            break;
                                    }
                                }

                            }
                        }

                    );

                    $.proxy(methods._resize, this)();


                }
            });
        },


        _confirm: function (files) {
            var $this = $(this);
            $this.trigger('ipModuleRepository.confirm', [files]);
        },

        // set back our element
        _teardown: function () {
            $(window).unbind('resize.ipRepositoryBuy');
        },

        _resize: function (e) {
            var $popup = $('.ipsModuleRepositoryPopup');
            var tabsHeight = parseInt($popup.find('.ipsTabs').outerHeight());
            var $this = $(this);
            $this.find('iframe').outerHeight((parseInt($(window).height()) - tabsHeight)); // leaving place for tabs
        }

    };

    $.fn.ipRepositoryBuy = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryBuy');
        }

    };

})(jQuery);
