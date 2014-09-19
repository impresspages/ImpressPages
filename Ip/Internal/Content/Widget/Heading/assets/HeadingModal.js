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
                var $context = $this;
                var saveCallback = options.saveCallback;
                $this.modal();

                ipInitForms();
                $this.find('form').append('<input type="submit" style="position: absolute; left: -999999px; width: 1px; height: 1px; visibility: hidden;" tabindex="-1" />');
                $this.find('form').off().on('submit', function (e) {
                    e.preventDefault();
                    saveCallback({
                        anchor: $context.find('input[name=anchor]').val(),
                        link: $context.find('input[name=link]').val(),
                        blank: $context.find('input[name=blank]').prop('checked') ? 1 : 0
                    });
                    $this.modal('hide');
                });
                $this.find('.ipsConfirm').off().on('click', function() {
                    $context.find('form').submit();
                });

                $this.find('input[name=anchor]').off().on('keydown', $.proxy(methods.updateAnchor, $this));
                $this.find('input[name=anchor]').off().on('change', $.proxy(methods.updateAnchor, $this));
                $this.find('input[name=anchor]').off().on('keyup', $.proxy(methods.updateAnchor, $this));

                $this.find('input[name=anchor]').val(options.anchor);
                $this.find('input[name=link]').val(options.link);
                if (options.blank) {
                    $this.find('input[name=blank]').attr('checked', 'checked');
                } else {
                    $this.find('input[name=blank]').removeAttr('checked');
                }

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
    };

    $.fn.ipWidgetHeadingModal = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on ipWidgetHeadingModal');
        }
    };

})(jQuery);
