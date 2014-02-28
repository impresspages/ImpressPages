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

        $this.find('.ipsFieldsetSeo .form-group').addClass('hidden');
        $this.find('.ipsFieldsetSeo legend').off().on('click', function () {
            $this.find('.ipsFieldsetSeo .form-group').toggleClass('hidden');
        });

        $this.find('.ipsFieldsetOther .form-group').addClass('hidden');
        $this.find('.ipsFieldsetOther legend').off().on('click', function () {
            $this.find('.ipsFieldsetOther .form-group').toggleClass('hidden');
        });

        $this.find('form').validator(validatorConfig);
        $this.find('form').submit(function(e) {
            var form = $(this);

            // client-side validation OK.
            if (!e.isDefaultPrevented()) {
                $.ajax({
                    url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
                    dataType: 'json',
                    type : 'POST',
                    data: form.serialize(),
                    success: function (response){
                        if (response.status && response.status == 'success') {
                            //page has been successfully updated
                            $this.trigger('update.ipPages');
                        } else {
                            //PHP controller says there are some errors
                            if (response.errors) {
                                form.data("validator").invalidate(response.errors);
                            }
                        }
                    }
                });
            }
            e.preventDefault();
        });

        $this.find('.ipsDelete').on('click', function(e) {
            $this.trigger('delete.ipPages');
        });

        $this.find('.ipsEdit').on('click', function(e) {
            $this.trigger('edit.ipPages');
        });

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

})(ip.jQuery);


