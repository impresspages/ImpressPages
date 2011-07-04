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

                    $this.data('ipWidget', data); 
                    
                    $this.delegate('.ipWidgetEdit', 'click', methods.editPressed);
                    $this.delegate('.ipWidgetSave', 'click', methods.savePressed);
                    
                    $this.bind('edit.ipWidget', methods.edit);
                    $this.bind('save.ipWidget', methods.save);
                }                
            });
        },
        
        editPressed : function(event) {
        	$(this).trigger('edit.ipWidget');
        },
        savePressed : function(event) {
        	$(this).trigger('save.ipWidget');
        },
        
        edit : function(event){
        	$this = $(this);
        	
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'manageWidget';
            data.widgetId = $this.data('ipWidget').id;
        
            $.ajax({
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : methods.manage,
                dataType : 'json'
            });     
        },
        
        manage : function(response) {
        	$this = $(this);
        	$this.replaceWith(response.managementHtml);
        },
        
        save : function(event){
        	$this = $(this);
        	
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'manageWidget';
            data.widgetId = $this.data('ipWidget').id;
        
            $.ajax({
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : methods.saveSuccess,
                dataType : 'json'
            });                	
        	
        	alert('super save');
        	console.log($(this).data('ipWidget'));
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