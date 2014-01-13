/**
 * @package ImpressPages
 *
 *
 */

(function($) {

    "use strict";

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipBlock');

            // If the plugin hasn't been initialized yet
            if (!data) {
                $this.delegate('.ipActionWidgetMove', 'click', function(e){e.preventDefault();});
                $this.sortable( {
                    connectWith : '.ipBlock, .ipsWidgetDropPlaceholder',
                    revert : true,
                    dropOnEmpty : true,
                    forcePlaceholderSize : false,
                    placeholder: 'ipAdminWidgetPlaceholder',
                    handle : '.ipAdminWidgetControls .ipActionWidgetMove',
                    start : function (event, ui) {
                        ui.item.addClass('ipAdminWidgetDrag');
                        ui.item.width(ui.item.find('.ipAdminWidgetMoveIcon').outerWidth());
                        ui.item.height(ui.item.find('.ipAdminWidgetMoveIcon').outerHeight());
                    },
                    stop : function (event, ui) {
                        ui.item.removeClass('ipAdminWidgetDrag');
                        ui.item.width('auto');
                        ui.item.height('auto');
                    },
                    
                    // this event is fired twice by both blocks, when element is moved from one block to another.
                    update : function(event, ui) {
                        if (!$(ui.item).data('widgetinstanceid')) {
                            return;
                        }
    
                        // item is dragged out of the block. This action will be handled by the receiver using "receive"
                        if ($(ui.item).parent().data('ipBlock').name != $this.data('ipBlock').name) {
                            return;
                        }
    
                        var instanceId = $(ui.item).data('widgetinstanceid');
                        var position = $(ui.item).index();
                        var block = $this.data('ipBlock').name;
                        var revisionId = $this.data('ipBlock').revisionId;

                        ipContent.moveWidget(instanceId, position, block, revisionId);
    
                    },
    
                    receive : function(event, ui) {
                        var $element = $(ui.item);
                        // if received element is AdminWidgetButton (insert new widget)
                        if ($element && $element.is('.ipActionWidgetButton')) {
                            var $duplicatedDragItem = $('.ipBlock .ipActionWidgetButton');
                            var position = $duplicatedDragItem.index();
                            var newWidgetName = $element.data('ipAdminWidgetButton').name;
                            $duplicatedDragItem.remove();
                            var $block = $this;
                            var $revisionId = $block.data('ipBlock').revisionId;
                            var blockName = $block.data('ipBlock').name;
                            ipContent.createWidget($revisionId, blockName, newWidgetName, position);
                        }
    
                    }
                });
                
                $this.data('ipBlock', {
                    name : $this.attr('id').substr(8),
                    revisionId : $this.data('revisionid'),
                    widgetControlsHtml : options.widgetControlsHtml
                });

                var widgetOptions = new Object;
                widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
                $this.children('.ipWidget').ipWidget(widgetOptions);

                $this.bind('reinitRequired.ipWidget', function(event) {
                    // ignore events which bubble up from nested blocks
                    if ( $(event.target).closest('.ipBlock')[0] != $this[0] )
                        return;
                    $(this).ipBlock('reinit');
                });
                $this.on('click', '> .ipbExampleContent', function () {
                    var $block = $this;
                    var $exampleContent = $(this);
                    ipContent.createWidget(ip.revisionId, $block.data('ipBlock').name, 'Text', 0);
                    $exampleContent.remove();
                });

            }
        });
    },

    reinit : function() {
        return this.each(function() {
            var $this = $(this);
            var widgetOptions = new Object;
            widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
            $(this).children('.ipWidget').ipWidget(widgetOptions);
        });
    },


    destroy : function() {
        // TODO
    }




    };
    


    $.fn.ipBlock = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(ip.jQuery);
