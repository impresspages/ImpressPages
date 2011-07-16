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
                    $this.sortable({
                        connectWith: '.ipBlock',
                        revert: true,
                        dropOnEmpty: true,
                        forcePlaceholderSize: true,

                        receive: function(event, ui) {
                            $element = null;
                            
                            $element = $(ui.item);

                            $duplicatedDragItem =  $('.ipBlock .ipWidgetButtonSelector');
                            
                            if ($duplicatedDragItem) {
                                $position = $duplicatedDragItem.index();
                                $duplicatedDragItem.remove();
                                
                                $block = $(event.target);
                                //if received element is WidgetButton (insert new widget)
                                if ($element && $element.is('.ipWidgetButton')) {
                                    var newWidgetName = $element.data('ipWidgetButton').name;
                                    $block.ipBlock('_createWidget', newWidgetName, $position);
                                } else {
                                	//do nothing
                                }
                            	
                            } else {
                            	$.fn.ipBlock('_showError', 'Can\'t select dragged item');
                            }
                        }
                    });        
                    $this.data('ipBlock', {
                        name : $this.attr('id').substr(8),
                        revisionId : options.revisionId,
                        widgetControllsHtml : options.widgetControllsHtml
                        
                    }); 
                    
                    
                    var widgetOptions = new Object;
                    $this.find('.ipWidget').ipWidget(widgetOptions);
                    $this.find('.ipWidget').prepend($this.data('ipBlock').widgetControllsHtml);
                    
                    
                    
                    $this.delegate('.ipWidget .ipWidgetManage', 'click', function(event){$(this).trigger('manageClick.ipBlock');});
                    $this.delegate('.ipWidget .ipWidgetSave', 'click', function(event){console.log('save0'); $(this).trigger('saveClick.ipBlock');});
                    //$this.find('.ipWidget').delegate('.ipWidgetDelete', 'click', function(event){$(this).trigger('delete.ipWidget');});
                    
                    $this.delegate('.ipWidget', 'manageClick.ipBlock', function(event){$(this).trigger('manageWidget.ipBlock', $(this).data('ipWidget').id);});
                    $this.delegate('.ipWidget', 'saveClick.ipBlock', function(event){console.log('save1'); $(this).trigger('saveWidget.ipBlock', $(this).data('ipWidget').id);});
                    //$this.find('.ipWidget').bind('delete.ipWidget', function(event){$(this).ipWidget('delete');});                    
                    $this.delegate('.ipWidget', 'preparedWidgetData.ipWidget', function(event, widgetData){console.log('prepared wiget data'); $(this).trigger('preparedWidgetData.ipBlock', [$(this).data('ipWidget').id, widgetData]);});
                    	
                    	
             

                    
                    $this.bind('manageWidget.ipBlock', function(event, widgetId){$(this).ipBlock('manageWidget', widgetId);});
                    $this.bind('saveWidget.ipBlock', function(event, widgetId){console.log('save'); $(this).ipBlock('saveWidget', widgetId);});
                    $this.bind('preparedWidgetData.ipBlock', function(event, widgetId, widgetData){console.log('prepared widget data'); console.log(widgetId); $(this).ipBlock('_saveWidgetData', widgetId, widgetData);});
                    
                }                
            });
        },
        
        
        saveWidget : function(widgetId){

            return this.each(function() {
            	$this = $(this);
	        	console.log('save start');
	        	$widget = $this.find('#ipWidget_' + widgetId);
	        	widgetName = $widget.data('ipWidget').name;
	        	console.log(widgetName);
	        	if (eval("typeof ipWidget_" + widgetName + " == 'function'")) {
	        		eval('var widgetPluginObject = new ipWidget_' + widgetName + '($widget);');
		        	widgetPluginObject.prepareData();
	        	} else {
	        		console.log($this);
	        		$this.ipBlock('previewWidget', widgetId);
	        	}
	        	
	        	
            });
        },
        
        previewWidget : function (widgetId) {
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'previewWidget';
            data.widgetId = widgetId;
        
            $.ajax({
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : methods._previewWidgetResponse,
                dataType : 'json'
            });	        	
        	
        },
        
        _previewWidgetResponse : function (response) {
            return this.each(function() {

            	$this = $(this);
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	console.log($widget);
            	$widget.replaceWith(response.previewHtml);
	        	//TODO change widget status to 'management'
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	$widget.ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
            		
                //$this.find('.ipWidget:not(:data("ipWidget"))').ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
                //$this.ipBlock('_reinitWidgets');
            });        	
        },
                
        _saveWidgetData : function (widgetId, widgetData) {
       	
			return this.each(function() {     
				console.log(widgetData);
	            data = Object();
	            data.g = 'standard';
	            data.m = 'content_management';
	            data.a = 'updateWidget';
	            data.widgetId = widgetId;
	            data.widgetData = widgetData;
	            console.log(widgetData);
	        
	            $.ajax({
	                type : 'POST',
	                url : ipBaseUrl,
	                data : data,
	                context : $this,
	                success : methods._saveDataResponse,
	                dataType : 'json'
	            });				
				
				
				

			});	        	
        },
        
        _saveDataResponse : function (response) {
            return this.each(function() {

            	$this = $(this);
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	$widget.replaceWith(response.previewHtml);
	        	//TODO change widget status to 'management'
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	$widget.ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
            		
                //$this.find('.ipWidget:not(:data("ipWidget"))').ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
                //$this.ipBlock('_reinitWidgets');
            });        	
        },
        
        manageWidget : function (widgetId) { 

            return this.each(function() {
               	
	        	$this = $(this);
	        	
	            data = Object();
	            data.g = 'standard';
	            data.m = 'content_management';
	            data.a = 'manageWidget';
	            data.widgetId = widgetId;
	        
	            $.ajax({
	                type : 'POST',
	                url : ipBaseUrl,
	                data : data,
	                context : $this,
	                success : methods._manageWidgetResponse,
	                dataType : 'json'
	            });
            });        	
        },
        
        
        
        _manageWidgetResponse : function(response) {

            return this.each(function() {

            	$this = $(this);
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	console.log($widget);
            	$widget.replaceWith(response.managementHtml);
	        	//TODO change widget status to 'management'
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	$widget.ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
            		
                //$this.find('.ipWidget:not(:data("ipWidget"))').ipWidget(new Object).prepend($this.data('ipBlock').widgetControllsHtml);
                //$this.ipBlock('_reinitWidgets');
            });
        },
                
        

        destroy : function() {
            // TODO
        },
        
        _showError : function (errorMessage) {
            alert(errorMessage);    
            
        },
        
//        'deleteWidget' : function(event){
//            return this.each(function() {
//   	
//	        	$this = $(this);
//	        	
//	            data = Object();
//	            data.g = 'standard';
//	            data.m = 'content_management';
//	            data.a = 'deleteWidget';
//	            data.widgetId = $this.data('ipWidget').id;
//	            data.revisionId = $this.data('ipBlock').revisionId;	            
//	        
//	            $.ajax({
//	                type : 'POST',
//	                url : ipBaseUrl,
//	                data : data,
//	                context : $this,
//	                success : methods._deleteResponse,
//	                dataType : 'json'
//	            });
//            });
//        },
//        
//        _deleteResponse : function(response){
//        	console.log('REMOVEED');
//        	
//        },
                
        
        _createWidget : function (widgetName, position) {

            return this.each(function() {
                        	
	            var $this = $(this);
	
	            data = Object();
	            data.g = 'standard';
	            data.m = 'content_management';
	            data.a = 'createWidget';
	            data.widgetName = widgetName;
	            data.position = position;
	            data.blockName = $this.data('ipBlock').name;
	            data.zoneName = $this.data('ipBlock').zoneName;
	            data.pageId = $this.data('ipBlock').pageId;
	            data.revisionId = $this.data('ipBlock').revisionId;
	        
	            $.ajax({
	                type : 'POST',
	                url : ipBaseUrl,
	                data : data,
	                context : $this,
	                success : methods._createWidgetResponse,
	                dataType : 'json'
	            });        
	            
            });

        },       
        // function(response){alert('test');  $(this).find('.ipWidgetButton').replaceWith(response.widgetHtml); $(this).ipBlock('destroy'); }
        _createWidgetResponse : function(response) {
            
            if (response.status == 'error') {
                $.fn.ipBlock('_showError', response.errorMessage);
                //alert(response.errorMessage);
            }
            
            if (response.status == 'success') {           
            	//console.log ('.ipWidget:nth-child( + ' + response.position + ' + )');
            	if (response.position == 0) {
	                $(this).prepend(response.widgetHtml);
            	} else {
	                $secondChild = $(this).find('.ipWidget:nth-child(' + response.position + ')');
	                $(response.widgetHtml).insertAfter($secondChild);
            	}
                //$(this).find('.ipWidgetButton').replaceWith(response.widgetHtml);

                //$('.ipWidget_' + response.widgetId).trigger('ipInitManagement', [response.widgetId]);
                //$('.ipWidget_' + response.widgetId).trigger('ipSave', ['message1', 'message2']);

            }

        }
    

        
    };
    
    

    $.fn.ipBlock = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidgetButton');
        }


    };
    
   

})(jQuery);