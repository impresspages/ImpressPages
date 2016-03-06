/**
 * @package ImpressPages
 *
 */

(function ($) {
    "use strict";

    var hideTimer = 0,
        controlsClass = 'ipsModuleInlineManagementControls',
        hiliteClass = '_hilite',
        methods = {
            init: function (options) {
                return this.each(function () {
                    var $this = $(this);

                    // Creating global controls block
                    if (!$('.' + controlsClass).length) {
                        $('body').append(ipModuleInlineManagementControls);
                    }
                    var $controls = $('.' + controlsClass);
                    $controls
                        .off('mouseenter').on('mouseenter', function (event) {
                            clearTimeout(hideTimer);
                        })
                        .off('mouseleave').on('mouseleave', function (event) {
                            hideTimer = setTimeout(function () {
                                $controls.hide();
                                $('.' + hiliteClass).removeClass(hiliteClass);
                            }, 30);
                        });

                    var data = $this.data('ipModuleInlineManagementControls');
                    // If the plugin hasn't been initialized yet
                    if (!data) {
                        $this
                            .data('ipModuleInlineManagementControls', {
                                initiated: 1
                            })
                            .mouseenter(function (e) {
                                clearTimeout(hideTimer);
                                $('.' + hiliteClass).removeClass(hiliteClass); // additional hack for removing class if you roll mouse from controls onto other controllable object
                                $this.addClass(hiliteClass);
                                var objOffset = $this.offset();
                                $controls
                                    .css({
                                        left: objOffset.left,
                                        top: objOffset.top
                                    })
                                    .show();
                                for (var key in options) {
                                    $controls
                                        .find('.ipActionWidget' + key).show().off().on('click', function (event) {
                                            event.preventDefault();
                                            if ($.isFunction(options[key])) {
                                                options[key]();
                                            }
                                        });
                                }
                            })
                            .mouseleave(function (e) {
                                hideTimer = setTimeout(function () {
                                    $controls.hide();
                                    $('.' + hiliteClass).removeClass(hiliteClass);
                                }, 30);
                            });
                    }
                });
            }
        };

    $.fn.ipModuleInlineManagementControls = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipInlineManagementControls');
        }
    };

})(jQuery);
