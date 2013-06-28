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
                        stop: function(event, ui) { }
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