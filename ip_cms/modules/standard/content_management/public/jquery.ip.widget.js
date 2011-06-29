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
                
                var data = $this.data('ipWidget');
            
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                	$this.prepend(options.widgetControllsHtml);
                	
                	var data = Object();

                	$this.find('.ipWidgetData input').each(
            			function() {
            				data[$(this).attr('name')] = $(this).val();
            			}
                	);
                	console.log(data);
                	
                    $this.data('ipWidget', data); 
                    
                }                
            });
        }
        
    };
    
    

    $.fn.ipWidget = function(method) {
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
          } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
          } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.ipWidget' );
          }    

    };
    
   

})(jQuery);