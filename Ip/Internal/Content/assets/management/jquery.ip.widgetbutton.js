/**
 * @package ImpressPages
 *
 *
 */

(function($) {

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);
                
                var data = $this.data('ipAdminWidgetButton');
            
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.draggable({
                        connectToSortable : '.ipBlock',
                        revert : function(droppable) {
                            if(droppable === false) {
                                // drop was unsuccessful
                                $this.trigger('unsuccessfulDrop.ipWidgetButton',{
                                    widgetButton: $this
                                });
                                return true;
                            } else {
                                // drop was successful
                                $this.trigger('successfulDrop.ipWidgetButton',{
                                    widgetButton: $this,
                                    block: droppable
                                });
                                return false;
                            }
                        },
                        helper : 'clone',
                        stop: function(event, ui) {
                            if (widgetOnDroppable) {
                                //jQuery-ui droppable is buggy and fire fake drop event. So we better handle stop event on draggable. This is just for widget side drops
                                $(widgetOnDroppable).css('backgroundColor', 'gray');
                            }

                        },
                        start: function (event, ui) {
                            $('.ipWidget').each(function(key, value) {
                                var $droppable = $('<div style="width: 10px; background-color: #000;"></div>');
                                $('body').append($droppable);

                                $droppable.css('position', 'absolute');
                                $droppable.css('left', $(value).offset().left - Math.round($droppable.width() / 2) + 'px');
                                $droppable.css('top', $(value).offset().top + 10 + 'px');
                                $droppable.css('height', $(value).height() - 20 + 'px');
                                $droppable.droppable({
                                    accept: ".ipActionWidgetButton, .ipWidget",
                                    activeClass: "ui-state-hover",
                                    hoverClass: "ui-state-active",
                                    over: function(event,ui) {
                                        widgetOnDroppable = this;
                                    },
                                    out: function(event, ui) {
                                        widgetOnDroppable = false;
                                    },
                                    drop: function( event, ui ) {
                                        //this method on jQuery-ui is buggy and fires fake drop events. So we better handle stop event on draggable. This is just for widget side drops.
                                    }
                                });


                            });
                            console.log('start');
                        }
                    });
                    
                    $this.data('ipAdminWidgetButton', {
                        name : $this.attr('id').substr(20)
                    });

                }
                    
                $this.find('a').bind('click', function () {return false;} );
                
                

            });
        },
        destroy : function() {
            // TODO
        }

        
    };

    $.fn.ipAdminWidgetButton = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);