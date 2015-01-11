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

                var data = $this.data('ipFormColor');
                if (!data) {
                    $this.data('ipFormColor', {});

                    //the only reliable way to wait till Spectrum loads is to periodically check if it has been loaded

                    var loadInterval = setInterval(function () {
                        initSpectrum($this, loadInterval);
                    }, 400);
                }
            });
        }
    };

    var initSpectrum = function ($colorPicker, loadInterval) {
        if (typeof($.fn.spectrum) == 'undefined') {
            //Wait for spectrum to load
            return;

        }
        var $this = $colorPicker;
        var lastColor = $this.find('.ipsColorPicker').val();

        clearInterval(loadInterval);

        $this.find('.ipsColorPicker').spectrum({
            preferredFormat: 'RGB',
            showInput: true,
            cancelText: $this.find('.ipsColorPicker').data('canceltext'),
            chooseText: $this.find('.ipsColorPicker').data('confirmtext'),
            move: function (color) {
                $this.find('.ipsColorPicker').spectrum("set", color.toHexString());
            },
            show: function (color) {
                lastColor = color.toHexString();
                $('.sp-cancel').on('click', function () {
                    $this.find('.ipsColorPicker').spectrum("set", lastColor);
                });
                $('.sp-choose').on('click', function () {
                    lastColor = $this.find('.ipsColorPicker').val();
                });
            }

        });
    };


    $.fn.ipFormColor = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormFile');
        }

    };

    $('.ipsModuleFormAdmin .ipsFileContainer').ipFormColor();

})(jQuery);


