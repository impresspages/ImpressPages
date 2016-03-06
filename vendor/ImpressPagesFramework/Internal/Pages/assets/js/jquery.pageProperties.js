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

                $this.html('');

                var data = Object();
                data.pageId = options.pageId;
                data.aa = 'Pages.pagePropertiesForm';
                data.securityToken = ip.securityToken;


                $.ajax({
                    type: 'GET',
                    url: ip.baseUrl,
                    data: data,
                    encoding: "UTF-8",
                    context: $this,
                    success: formResponse,
                    dataType: 'json'
                });

            });
        },
        destroy: function () {
            // TODO
        }



    };

    var formResponse = function (response) {
        var $this = this;
        $this.html(response.html);

        // wrap fields in a div so accordion would work
        $this.find('fieldset').each(function (index, fieldset) {
            var $fieldset = $(fieldset);
            var $legend = $fieldset.find('legend');

            // if legend exist it means its option group
            if ($legend.length) {
                // adding required attributes to make collapse() to work
                $legend
                    .attr('data-toggle', 'collapse')
                    .attr('data-target', '#propertiesCollapse' + index)
                    .addClass('collapsed');
                $fieldset.find('.form-group').wrapAll('<div class="collapse" id="propertiesCollapse' + index + '" />');
            }
        });

        ipInitForms();
        $this.find('form').on('ipSubmitResponse', function (e, response) {
            if (response.status && response.status == 'success') {
                //page has been successfully updated
                if (response.newPageUrl) {
                    $this.find('input[name=urlPath]').val(response.newPageUrl);
                }
                $this.trigger('update.ipPages');
                $this.find('.ipsSave').addClass('btn-default').removeClass('btn-primary');
            }
        });

        $this.find('.ipsDelete').on('click', function (e) {
            $this.trigger('delete.ipPages');
        });

        $('html').off('keyup.pageProperties').on('keyup.pageProperties', function(e) {
            if (e.keyCode === 46 && e.ctrlKey && $this.find('.ipsDelete').is(":visible")) {
                $this.find('.ipsDelete').click();
            }
        });

        $this.find('.ipsEdit').on('click', function (e) {
            $this.trigger('edit.ipPages');
        });


        $this.find('.ipsSave').off().on('click', function () {
            $this.find("form").submit();
        });

        $this.find('input,select,textarea').off().on('change keydown input', function () {
            $this.find('.ipsSave').removeClass('btn-default').addClass('btn-primary');
        });
    };

    $.fn.ipPageProperties = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipPageProperties');
        }

    };

})(jQuery);


