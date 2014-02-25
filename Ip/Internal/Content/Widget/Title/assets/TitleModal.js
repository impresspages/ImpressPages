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
                console.log(options);
                $this.modal();
                $this.find('.ipsConfirm').on('click', function() {console.log(saveCallback);
                    saveCallback({
                        anchor: $this.find('input[name=anchor]').val(),
                        link: $this.find('input[name=link]').val(),
                        blank: $this.find('input[name=blank]').attr('checked') ? 1 : 0
                    });
                    $this.modal('hide');
                });

                $this.find('input[name=anchor]').on('keydown', $.proxy(methods.updateAnchor, $this));
                $this.find('input[name=anchor]').on('change', $.proxy(methods.updateAnchor, $this));
                $this.find('input[name=anchor]').on('keyup', $.proxy(methods.updateAnchor, $this));

                $this.find('input[name=anchor]').val(options.anchor);
                $this.find('input[name=link]').val(options.link);
                $this.find('input[name=blank]').attr('checked', options.blank);
                $.proxy(methods.updateAnchor, $this)();
            });
        },

        updateAnchor: function() {
            this.each(function () {
                var $this = $(this);
                var $preview = $this.find('.ipsAnchorPreview');
                var curText = $preview.text();
                var newText = curText.split('#')[0] + '#' + $this.find('input[name=anchor]').val();
                $preview.text(newText);
            });
        },

        destroy: function () {
            return this.each(function () {

            });
        }
    }

    $.fn.ipWidgetTitleModal = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on ipWidgetTitleModal');
        }
    };

})(ip.jQuery);
