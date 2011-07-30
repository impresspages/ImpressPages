/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */


(function($) {

    var methods = {
        init : function(options) {


            return this.each(function() {
                
                var $this = $(this);
                
                var data = $this.data('ipBlock');
            
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
 
                    $this.data('ipContentManagement', {
                        pendingBlocks : 0
                        
                    }); 
                    
                    
                    
                    $this.delegate('.ipWidget .ipWidgetManage', 'click', function(event){$(this).trigger('manageClick.ipBlock');});
                    
                    $this.bind('manageWidget.ipBlock', function(event, widgetId){$(this).ipBlock('manageWidget', widgetId);});
                    
                }                
            });
        },
        

        
    

        
    };
    
    

    $.fn.ipContentManagement = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidgetButton');
        }


    };
    
   

})(jQuery);