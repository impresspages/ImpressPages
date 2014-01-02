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
                            console.log('Drop');
                            console.log(ui);
                            if (draggableOnDroppable) {
                                //jQuery-ui droppable is buggy and fire fake drop event. So we better handle stop event on draggable. This is just for widget side drops
                                $(draggableOnDroppable).css('backgroundColor', 'black');
                            }

                        },
                        start: function (event, ui) {
                            $('.ipBlock').each(function(key, value) {
                                var $droppable = $('<div style="">DROPPABLE</div>');
                                $('body').append($droppable);

                                $droppable.css('position', 'absolute');
                                $droppable.css('left', $(value).offset().left - $droppable.width() + 'px');
                                $droppable.css('top', $(value).offset().top + 100 + 'px');
                                $droppable.droppable({
                                    accept: ".ipActionWidgetButton, .ipWidget",
                                    activeClass: "ui-state-hover",
                                    hoverClass: "ui-state-active",
                                    over: function(event,ui) {
                                        draggableOnDroppable = this;
                                    },
                                    out: function(event, ui) {
                                        draggableOnDroppable = false;
                                    },
                                    drop: function( event, ui ) {
                                        $( this )
                                            .addClass( "ui-state-highlight" )
                                            .html( "Dropped!" );
                                        console.log(this);
                                        console.log(event);
                                        console.log(ui);
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