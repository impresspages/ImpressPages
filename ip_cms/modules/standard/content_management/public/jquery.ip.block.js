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
                                $block.ipBlock('_createWidget', newWidgetName);
                            } else {

                            }
                        }
                    });        
                    
                    $this.data('ipBlock', {
                        name : $this.attr('id').substr(8),
                        zoneName : options.zoneName,
                        pageId : options.pageId
                        
                    }); 
                    
                }                
            });
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
        
            $.ajax({
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : $.fn.ipBlock,
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
                alert(response.widgetHtml);
                $(this).find('.ipWidgetButton').replaceWith(response.widgetHtml);

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
        } else if (typeof method === 'object' && method.action) {
            alert(method.action);
            methods[method.action].apply(this, Array.prototype.slice.call(arguments, 0));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };
    
   

})(jQuery);