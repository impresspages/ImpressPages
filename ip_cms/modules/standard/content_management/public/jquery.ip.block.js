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
                        receive: function(event, ui) {
                            $element = null;
                            
                            elementId = $(ui.item).attr('id');
                            
                            if (elementId) {
                                $element = $('#' + elementId);
                            }
                            $block = $(event.target);
                            //if received element is WidgetButton (insert new widget)
                            if ($element && $element.is('.ipWidgetButton')) {
                                var newWidgetName = $element.data('ipWidgetButton').name;
                                alert(newWidgetName);
                                $block.ipBlock('_createWidget', newWidgetName);
                            } else {

                            }
                        }
                    });        
                    $this.data('ipBlock', {
                        name : $this.attr('id').substr(8),
                        revisionId : options.revisionId,
                        widgetControllsHtml : options.widgetControllsHtml
                        
                    }); 
                    
                    
                    var widgetOptions = new Object;
                    widgetOptions.widgetControllsHtml = $this.data('ipBlock').widgetControllsHtml;
                    $this.find('.ipWidget').prepend().ipWidget(widgetOptions);
                    
                                        
                    $this.bind('stateChangedToManagement.ipWidget', methods._reinitWidgets);                    
                }                
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
        
        _createWidget : function (widgetName) {
            var $this = $(this);

            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'createWidget';
            data.widgetName = widgetName;
            data.position = 1;
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
        },       
        // function(response){alert('test');  $(this).find('.ipWidgetButton').replaceWith(response.widgetHtml); $(this).ipBlock('destroy'); }
        _createWidgetResponse : function(response) {
            
            if (response.status == 'error') {
                $.fn.ipBlock('_showError', response.errorMessage);
                //alert(response.errorMessage);
            }
            
            if (response.status == 'success') {
                alert(response.widgetId);
                $(this).find('.ipWidgetButton').replaceWith(response.widgetHtml);

                $('.ipWidget_' + response.widgetId).trigger('ipInitManagement', [response.widgetId]);
                //$('.ipWidget_' + response.widgetId).trigger('ipSave', ['message1', 'message2']);

            }
            //console.log(response);
            
            //alert('tst ' + name);
//            return $(this).each(function() {
//                alert(name);
//            });
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