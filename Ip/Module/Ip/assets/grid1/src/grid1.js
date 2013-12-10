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
                var data = $this.data('ipGrid1Init');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipGrid1Init', Object());

                    //window.location.hash

                    $.ajax({
                        type: 'GET',
                        url: $this.data('gateway'),
                        data: {
                            jsonrpc: '2.0',
                            method: 'init',
                            params: {}
                        },
                        context: $this,
                        success: initResponse,
                        error: function(response) {
                            if (ip.debugMode || ip.developmentEnvironment) {
                                alert(response);
                            }
                        },
                        dataType: 'json'
                    });


                }



            });
        }





    };


    var initResponse = function(response) {
        var $this = this;
        $this.html(response.html);
    };

    $.fn.ipGrid1 = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };

})(jQuery);