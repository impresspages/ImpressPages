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

                var data = $this.data('ipAdminWidgetButton');

                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.draggable({
                        revert: function (droppable) {
                            if (droppable === false) {
                                // drop was unsuccessful
                                $this.trigger('ipWidgetAddFailed', {
                                    widgetButton: $this
                                });
                                return true;
                            } else {
                                // drop was successful
                                $this.trigger('ipWidgetAdded', {
                                    widgetButton: $this,
                                    block: droppable
                                });
                                return false;
                            }
                        },
                        helper: function (e) {
                            var $button = $(e.currentTarget);
                            var $result = $button.clone();
                            $result.find('span').remove();
                            $result.css('padding-top', '15px');
                            return $result;
                        },
                        opacity: 0.45,
                        cursorAt: {left: 30, top: 45},
                        stop: function (event, ui) {
                        },
                        start: function (event, ui) {
                            // fixing dragging from fixed element while scrolling
                            $(this).data("startingScrollTop", $('body').scrollTop());
                        },
                        drag: function (event, ui) {
                            // fixing dragging from fixed element while scrolling
                            var start = parseInt($(this).data("startingScrollTop"));
                            ui.position.top -= $('body').scrollTop() - start;
                        }
                    });

                    $this.data('ipAdminWidgetButton', {
                        name: $this.attr('id').substr(20)
                    });

                }

                $this.find('a').bind('click', function () {
                    return false;
                });


            });
        },
        destroy: function () {
            // TODO
        }


    };

    $.fn.ipAdminWidgetButton = function (method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);
