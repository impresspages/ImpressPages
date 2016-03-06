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

                var data = $this.data('ipBlock');

                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.delegate('.ipsWidgetDrag', 'click', function (e) {
                        e.preventDefault();
                    });

                    initWidgetDrag($this);

                    $this.data('ipBlock', {
                        name: $this.attr('id').substr(8),
                        revisionId: $this.data('revisionid'),
                        widgetControlsHtml: options.widgetControlsHtml
                    });

                    var widgetOptions = {};
                    widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
                    $this.children('.ipWidget').ipWidget(widgetOptions);

                    $this.on('ipWidgetReinit', function (event) {
                        // ignore events which bubble up from nested blocks
                        if ($(event.target).closest('.ipBlock')[0] != $this[0]) {
                            return;
                        }
                        $(this).ipBlock('reinit');
                    });
                    $this.on('click', '> .ipbExampleContent', function () {
                        var widgetOnClick = 'Heading';
                        var $block = $this;
                        var $exampleContent = $(this);
                        if (typeof ipDefaultWidget !== "undefined") {
                            widgetOnClick = ipDefaultWidget;
                        }
                        ipContent.createWidget($block.data('ipBlock').name, widgetOnClick, 0);
                        $exampleContent.remove();
                    });

                }
            });
        },

        reinit: function () {
            return this.each(function () {
                var $this = $(this);
                var widgetOptions = new Object;
                widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
                $(this).children('.ipWidget').ipWidget(widgetOptions);
                initWidgetDrag($this);
            });
        },


        destroy: function () {
            // TODO
        }




    };


    var initWidgetDrag = function ($block) {
        var $this = $block;
        $this.find('.ipWidget').not('.ipWidget-Columns').draggable({
            handle: '.ipsWidgetControls .ipsWidgetDrag',
            cursorAt: {
                left: 30, top: 30
            },
            cancel: false, // making <button> elements to work
            helper: function (e) {
                return '<div class="ipAdminWidgetDragIcon"></div>';
            },
            start: function (event, ui) {
                $(event.target).css('visibility', 'hidden');
            },
            stop: function (event, ui) {
                $(event.target).css('visibility', '');
            },
            revert: function (droppable) {
                if (droppable === false) {
                    // drop was unsuccessful
                    $this.trigger('ipWidgetMoveFailed', {
                        widgetButton: $this
                    });
                    return true;
                } else {
                    // drop was successful
                    $this.trigger('ipWidgetMove', {
                        widgetButton: $this,
                        block: droppable
                    });
                    return false;
                }
            }

        });
    };


    $.fn.ipBlock = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);
