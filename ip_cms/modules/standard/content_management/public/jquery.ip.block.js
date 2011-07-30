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
                        handle: '.ipWidgetControls .ipWidgetMove',

                        //this event is fired twice by both blocks, when element is moved from one block to another.
                        update: function(event, ui) { 
                    		if(!$(ui.item).data('ipWidget')) {
                    			//some other object is dragged in. Do nothing.
                    			return;
                    		}
                    		
                    		//item is dragged out of the block. This action will be handled by the reciever using "receive"
                    		if ($(ui.item).parent().data('ipBlock').name != $this.data('ipBlock').name) { 
                    			return;
                    		}
                    	
                    		var widgetId = $(ui.item).data('ipWidget').id;
                    		var position = $(ui.item).index();
                    		
                            var data = Object();
                            data.g = 'standard';
                            data.m = 'content_management';
                            data.a = 'moveWidget';
                            data.widgetId = widgetId;
                            data.position = position;
                            data.blockName = $this.data('ipBlock').name;
                            data.revisionId = $this.data('ipBlock').revisionId;
                        
                            $.ajax({
                                type : 'POST',
                                url : ipBaseUrl,
                                data : data,
                                context : $this,
                                success : methods._moveWidgetResponse,
                                dataType : 'json'
                            });	                     		
                    		
                    	},
                        
                        receive: function(event, ui) {
                    		console.log('received');

                            
                            $element = $(ui.item);

                            //if received element is WidgetButton (insert new widget)
                            if ($element && $element.is('.ipWidgetButton')) {
                            	
                            	
                            	
                                $duplicatedDragItem =  $('.ipBlock .ipWidgetButtonSelector');
                                $position = $duplicatedDragItem.index();
                                var newWidgetName = $element.data('ipWidgetButton').name;
                                
                                $duplicatedDragItem.remove();
                                
                                $block = $(event.target);
                                
                                $block.ipBlock('_createWidget', newWidgetName, $position);
                            }                            
                            
                        }
                    });        
                    $this.data('ipBlock', {
                        name : $this.attr('id').substr(8),
                        revisionId : options.revisionId,
                        widgetControlsHtml : options.widgetControlsHtml
                        
                    }); 
                    
                    
                    var widgetOptions = new Object;
                    $this.find('.ipWidget').ipWidget(widgetOptions);
                    $this.find('.ipWidget').prepend($this.data('ipBlock').widgetControlsHtml);
                    
                    
                    
                    $this.delegate('.ipWidget .ipWidgetManage', 'click', function(event){$(this).trigger('manageClick.ipBlock');});
                    $this.delegate('.ipWidget .ipWidgetSave', 'click', function(event){$(this).trigger('saveClick.ipBlock');});
                    $this.delegate('.ipWidget .ipWidgetDelete', 'click', function(event){$(this).trigger('deleteClick.ipBlock');});
                    
                    $this.delegate('.ipWidget', 'manageClick.ipBlock', function(event){$(this).trigger('manageWidget.ipBlock', $(this).data('ipWidget').id);});
                    $this.delegate('.ipWidget', 'saveClick.ipBlock', function(event){$(this).trigger('saveWidget.ipBlock', $(this).data('ipWidget').id);});
                    $this.delegate('.ipWidget', 'preparedWidgetData.ipWidget', function(event, widgetData){$(this).trigger('preparedWidgetData.ipBlock', [$(this).data('ipWidget').id, widgetData]);});
                    $this.delegate('.ipWidget', 'deleteClick.ipBlock', function(event){$(this).trigger('deleteWidget.ipBlock', $(this).data('ipWidget').id);});                    
                    
                    $this.bind('manageWidget.ipBlock', function(event, widgetId){$(this).ipBlock('manageWidget', widgetId);});
                    $this.bind('saveWidget.ipBlock', function(event, widgetId){$(this).ipBlock('saveWidget', widgetId);});
                    $this.bind('preparedWidgetData.ipBlock', function(event, widgetId, widgetData){$(this).ipBlock('_saveWidgetData', widgetId, widgetData);});
                    $this.bind('deleteWidget.ipBlock', function(event, widgetId){$(this).ipBlock('deleteWidget', widgetId);});
                    
                }                
            });
        },
        
        _moveWidgetResponse : function (response) {
        	//todo show error on error response
        },
        
        save : function () {
            return this.each(function() {
            	$this = $(this);        	
            	console.log('Block save');
            	$this.trigger('saveFinish.ipBlock');
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
            	$this.ipBlock('_replaceWidgetContent', response.widgetId, response.previewHtml);
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
	                success : methods._saveWidgetDataResponse,
	                dataType : 'json'
	            });				
				
				
				

			});	        	
        },
        
        _saveWidgetDataResponse : function (response) {
            return this.each(function() {

            	$this = $(this);
            	$this.ipBlock('_replaceWidgetContent', response.widgetId, response.previewHtml);
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
            	$this.ipBlock('_replaceWidgetContent', response.widgetId, response.managementHtml);
            });
        },
                
        

        destroy : function() {
            // TODO
        },
        
        _showError : function (errorMessage) {
            alert(errorMessage);    
            
        },
        
        deleteWidget : function(widgetId){
            return this.each(function() {
   	
	        	$this = $(this);
	        	
	            data = Object();
	            data.g = 'standard';
	            data.m = 'content_management';
	            data.a = 'deleteWidget';
	            data.widgetId = widgetId;
	            data.revisionId = $this.data('ipBlock').revisionId;	            
	        
	            $.ajax({
	                type : 'POST',
	                url : ipBaseUrl,
	                data : data,
	                context : $this,
	                success : methods._deleteResponse,
	                dataType : 'json'
	            });
            });
        },
        
        _deleteResponse : function(response){
        	$this.find('#ipWidget_' + response.widgetId).remove();
        	
        },
                
        
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

        _createWidgetResponse : function(response) {
            
	            if (response.status == 'error') {
	                $.fn.ipBlock('_showError', response.errorMessage);
	            }
	            
	            if (response.status == 'success') {           
	            	if (response.position == 0) {
		                $(this).prepend(response.widgetHtml);
	            	} else {
		                $secondChild = $(this).find('.ipWidget:nth-child(' + response.position + ')');
		                $(response.widgetHtml).insertAfter($secondChild);
	            	}
	            	this.ipBlock('_initWidgetManagement', response.widgetId);
	
	            }

        },
        
        _replaceWidgetContent : function(widgetId, newContent){
            return this.each(function() {
            	$this = $(this);
            	$widget = $this.find('#ipWidget_' + widgetId);
            	$widget.replaceWith(newContent);
            	$this.ipBlock('_initWidgetManagement', widgetId);
            });        	
        	
        },
        
        
        _initWidgetManagement : function(widgetId){
        	$this = (this);
        	$widget = $this.find('#ipWidget_' + widgetId);
        	$widget.ipWidget(new Object).prepend($this.data('ipBlock').widgetControlsHtml);
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