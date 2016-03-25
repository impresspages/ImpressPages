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

                    // the only reliable way to wait till colorpicker plugin loads is to periodically check if it has been loaded

                    var loadInterval = setInterval(function () {
                        initColorpicker($this, loadInterval);
                    }, 400);
                }
            });
        }
    };

    var initColorpicker = function ($colorPicker, loadInterval) {
        if (typeof($.fn.colorpicker) == 'undefined') {
            // Wait for plugin to load
            return;

        }

        // stopping recurring check
        clearInterval(loadInterval);

        var $this = $colorPicker;

        // loading plugin
        // find option at http://mjolnic.github.io/bootstrap-colorpicker/
        $this.colorpicker({}).on('changeColor', function () {
            $this.find('.form-control').trigger('change'); // manually triggering change event on dynamic value change
        });

        // showing popup on input focus, too
        $this.find('.form-control').on('focus.colorpicker', function () {
            $this.colorpicker('show');
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

})(jQuery);


