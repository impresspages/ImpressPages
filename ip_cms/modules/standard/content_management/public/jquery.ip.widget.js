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
                	
                	data.state = 'preview'; //possible values: preview, management

                    $this.data('ipWidget', data); 
                    
                    $this.prepend(options.widgetControllsHtml);
                    
                    $this.delegate('.ipWidgetManage', 'click', function(event){$(this).trigger('manage.ipWidget');});
                    $this.delegate('.ipWidgetSave', 'click', function(event){$(this).trigger('save.ipWidget');});
                    
                    $this.bind('manage.ipWidget', function(event){$(this).ipWidget('manage')});
                    $this.bind('save.ipWidget', function(event){$(this).ipWidget('save')});
                    $this.bind('preparedData.ipWidget', function(event, data){$(this).ipWidget('preparedData', data)});
                    
                    $this.bind('saveSuccess.ipWidget', methods.saveSuccess);
                    
                }                
            });
        },
        

        manage : function(event){
            return this.each(function() {
   	
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
	                success : methods._manageResponse,
	                dataType : 'json'
	            });
            });
        },
        
        _manageResponse : function(response) {

            return this.each(function() {
            	$this = $(this);
            	$block = $this.parent();
	        	$this.replaceWith(response.managementHtml);
	        	//TODO change widget status to 'management'
	        	console.log('manage');
	        	$block.trigger('stateChangedToManagement.ipWidget');
            });
        },
        
        
        save : function(event){

            return this.each(function() {        	
	        	console.log('save start');
	        	var widgetObject = new ipWidget_text($(this));
	        	widgetObject.prepareData();
            });
        },
        
        preparedData : function(data) {
        	
            return this.each(function() {          	
	        	$this = $(this);
	        	
	        	$this.ipWidget('saveData', data);
	        	

	        });	        	
        },
        
        saveData : function (data) {
       	
			return this.each(function() {           	
				console.log(data);
				$(this).ipWidget('preview');
			});	        	
        },
        
        preview : function () {

            return this.each(function() {         	
	        	$this = $(this);

	            data = Object();
	            data.g = 'standard';
	            data.m = 'content_management';
	            data.a = 'previewWidget';
	            data.widgetId = $this.data('ipWidget').id;
	        
	            $.ajax({
	                type : 'POST',
	                url : ipBaseUrl,
	                data : data,
	                context : $this,
	                success : methods._previewResponse,
	                dataType : 'json'
	            });	        	
	        	
	        	alert($this.data('ipWidget').id);
            });
        },
        
        _previewResponse : function() {

            return this.each(function() {
            	$block = $this.parent();
	        	$this.replaceWith(response.previewHtml);
	        	console.log('preview');
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