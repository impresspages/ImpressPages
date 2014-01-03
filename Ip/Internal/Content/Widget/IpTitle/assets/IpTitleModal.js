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
                var saveCallback = options.saveCallback;
                $this.modal();
                $this.find('.ipsConfirm').on('click', function() {
                    saveCallback({anchor: $this.find('.ipsAnchor').val()});
                    $this.modal('hide');
                });

                $this.find('.ipsAnchor').on('keydown', $.proxy(methods.updateAnchor, $this));
                $this.find('.ipsAnchor').on('change', $.proxy(methods.updateAnchor, $this));
                $this.find('.ipsAnchor').on('keyup', $.proxy(methods.updateAnchor, $this));

                $this.find('.ipsAnchor').val(options.anchor);
            });
        },

        updateAnchor: function() {
            this.each(function () {
                var $this = $(this);
                var $preview = $this.find('.ipsAnchorPreview');
                var curText = $preview.text();
                var newText = curText.split('#')[0] + '#' + $this.find('.ipsAnchor').val();
                $preview.text(newText);
            });
        },


        destroy: function () {
            return this.each(function () {

            });
        }
    }


    $.fn.ipWidgetIpTitleModal = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on ipWidgetIpTitleModal');
        }
    };



})(jQuery);