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
                $this.data('pluginName', options.pluginName);

                var data = Object();
                data.pluginName = options.pluginName;
                data.aa = 'Plugins.pluginPropertiesForm';
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


        ipInitForms();
        $this.find('form').on('ipSubmitResponse', function (e, response) {
            if (response.result) {
                //plugin has been successfully updated
                $this.trigger('update.ipPlugins');
                $this.find('.ipsSave').addClass('btn-default').removeClass('btn-primary');
            }
        });

        $this.find('.ipsDeactivate').on('click', function (e) {
            $this.trigger('deactivate.ipPlugins');
        });

        $this.find('.ipsActivate').on('click', function (e) {
            $this.trigger('activate.ipPlugins');
        });

        $this.find('.ipsDelete').on('click', function (e) {
            if (confirm(ipTranslationAreYouSure)) {
                $this.trigger('delete.ipPlugins');
            }
        });


        $this.find('input,select,textarea').off().on('change keydown input', function () {
            $this.find('.ipsSave').removeClass('btn-default').addClass('btn-primary');
        });

        $this.trigger('pluginSelected.ipPlugins', [$this.data('pluginName')]);

    };

    $.fn.ipPluginProperties = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipPluginProperties');
        }

    };

})(jQuery);


