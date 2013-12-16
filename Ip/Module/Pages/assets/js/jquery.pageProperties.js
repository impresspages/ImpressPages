/**
 * @package ImpressPages
 *
 *
 */

(function($) {
    "use strict";

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);

                $this.html('');

                var data = Object();
                data.pageId = options.pageId;
                data.zoneName = options.zoneName;
                data.aa = 'Pages.pagePropertiesForm';
                data.securityToken = ip.securityToken;


                $.ajax({
                    type: 'GET',
                    url: ip.baseUrl,
                    data: data,
                    context: $this,
                    success: formResponse,
                    dataType: 'json'
                });

            });
        },
        destroy : function() {
            // TODO
        }



    };

    var formResponse = function (response) {
        var $this = this;
        $this.html(response.html);
    }


    $.fn.ipPageProperties = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);


