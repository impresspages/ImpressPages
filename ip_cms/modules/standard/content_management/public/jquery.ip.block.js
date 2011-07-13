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
                    widgetOptions.widgetControllsHtml = '';
                    $this.find('.ipWidget').ipWidget(widgetOptions);
                    $this.find('.ipWidget').prepend($this.data('ipBlock').widgetControllsHtml);
                    
                    
                    
                    $this.find('.ipWidget').delegate('.ipWidgetManage', 'click', function(event){console.log('MANAGE STEP0'); $(this).trigger('manageclick.ipBlock');});
                    //$this.find('.ipWidget').delegate('.ipWidgetSave', 'click', function(event){$(this).trigger('save.ipWidget');});
                    //$this.find('.ipWidget').delegate('.ipWidgetDelete', 'click', function(event){$(this).trigger('delete.ipWidget');});
                    
                    $this.find('.ipWidget').live('manageclick.ipBlock', function(event){$(this).trigger('manage.ipBlock', $(this).data('ipWidget').id);});
                    //$this.find('.ipWidget').bind('save.ipWidget', function(event){$(this).ipWidget('save');});
                    //$this.find('.ipWidget').bind('preparedData.ipWidget', function(event, data){$(this).ipWidget('preparedData', data);});
                    //$this.find('.ipWidget').bind('delete.ipWidget', function(event){$(this).ipWidget('delete');});                    
                    
                    
                    $this.bind('manage.ipBlock', function(event, widgetId){console.log('MANAGE STEP2'); $(this).ipBlock('manage', widgetId);});
                    
                    //$this.bind('stateChangedToManagement.ipWidget', methods._reinitWidgets);                    
                }                
            });
        },
        
        
        manage : function (widgetId) { 

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
	                success : methods._manageResponse,
	                dataType : 'json'
	            });
            });        	
        },
        
        
        
        _manageResponse : function(response) {

            return this.each(function() {
            	$this = $(this);
            	$widget = $this.find('#ipWidget_' + response.widgetId);
            	console.log($widget);
            	$widget.replaceWith(response.managementHtml);
	        	//TODO change widget status to 'management'
	        	$widget.trigger('stateChangedToManagement.ipWidget');
            });
        },
                
        
        _reinitWidgets : function(event){
        	console.log('REINIT');
        	var $this = $(this);
        	
            var widgetOptions = new Object;
            widgetOptions.widgetControllsHtml = $this.data('ipBlock').widgetControllsHtml;
            
            $this.find('.ipWidget').ipWidget(widgetOptions);
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