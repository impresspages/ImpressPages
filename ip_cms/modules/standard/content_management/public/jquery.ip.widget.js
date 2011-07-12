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
                	
                	var data = Object();

                	$this.find('.ipWidgetData input').each(
            			function() {
            				data[$(this).attr('name')] = $(this).val();
            			}
                	);

                    $this.data('ipWidget', data); 
                    
                    $this.prepend(options.widgetControllsHtml);
                    
                    $this.delegate('.ipWidgetEdit', 'click', methods.editPressed);
                    $this.delegate('.ipWidgetSave', 'click', methods.savePressed);
                    
                    $this.bind('edit.ipWidget', methods.edit);
                    $this.bind('save.ipWidget', methods.save);
                    
                    $this.bind('saveSuccess.ipWidget', methods.saveSuccess);
                    
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
        	$block = $this.parent();
        	$this.replaceWith(response.managementHtml);
        	console.log('manage');
        	$block.trigger('manage.ipWidget');
        },
        
        testSave : function(){
        	console.log('testSave');	
        },
        
        save : function(event){
        	console.log('save start');
        	var widgetObject = new ipWidget_text($(this));
        	//widgetObject.init(this);
        	widgetObject.save();
        	
        	//ipWidget_text_save(this, false);
//        	$this = $(this);
//        	alert('Save test');
//            data = Object();
//            data.g = 'standard';
//            data.m = 'content_management';
//            data.a = 'manageWidget';
//            data.widgetId = $this.data('ipWidget').id;
//        
//            $.ajax({
//                type : 'POST',
//                url : ipBaseUrl,
//                data : data,
//                context : $this,
//                success : methods.saveSuccess,
//                dataType : 'json'
//            });                	
//        	
//        	console.log($(this).data('ipWidget'));
        },
        
        saveSuccess : function(response) {
        	console.log('saved');
        	$(this).ipWidget('preview');
        },
        
        preview : function () {
        	$this = $(this);
        	
        	alert($this.data('ipWidget').id);
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