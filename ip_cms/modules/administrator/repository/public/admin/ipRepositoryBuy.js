/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 *
 */

"use strict";

(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {
                var $this = $(this);
                var buyTab = this;

                var data = $this.data('ipRepositoryBuy');
                if (!data) {
                    $this.data('ipRepositoryBuy', {});

                    var $popup = $('.ipModuleRepositoryPopup');

                    $(window).bind("resize.ipRepositoryBuy", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));

                    //create crossdomain socket connection
                    var remote = new easyXDM.Rpc({
                        remote: $('#ipModuleRepositoryTabBuy').data('marketurl'),
                        container: "ipModuleRepositoryTabBuy",
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
                                var toDownload = new Array();

                                for (var i = 0; i < images.length; i++) {
                                    toDownload.push({
                                        url: images[i].downloadUrl,
                                        name: 'image.jpg'
                                    });
                                }

                                $.ajax(ip.baseUrl, {
                                    'type': 'POST',
                                    // TODOX save good filename
                                    'data': {'g': 'administrator', 'm': 'repository', 'a': 'addFromUrl', 'files': toDownload},
                                    'dataType': 'json',
                                    'success': function (data) {
                                        $.proxy(methods._confirm, buyTab, data)();
                                    },
                                    'error': function () { alert('Download failed.'); }
                                });


                            }
                        }
                    }

                    );

                    $.proxy(methods._resize, this)();


                }
            });
        },


        _confirm : function (files) {
            var $this = $(this);
            $this.trigger('ipModuleRepository.confirm', [files]);
        },

        // set back our element
        _teardown: function() {
            $(window).unbind('resize.ipRepositoryBuy');
        },

        _resize: function(e) {
            var $this = $(this);
            $this.find('iframe').height((parseInt($(window).height()) - 55) + 'px');
        }

    };

    $.fn.ipRepositoryBuy = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryBuy');
        }

    };

})(jQuery);
