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
                        name : $this.attr('id').substr(15)
                        
                    });
                }                
            });
        },
        destroy : function() {
            // TODO
        },
        
        _createWidget : function (widgetName) {
            var $this = $(this);
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'createWidget';
            data.widgetName = widgetName;
        
            $.ajax({
                type : 'POST',
                url : ipBaseUrl,
                data : data,
                context : $this,
                success : function(response){  $(this).find('.ipWidgetButton').replaceWith(response.widgetHtml); $(this).ipBlock('destroy'); },
                dataType : 'json'
            });        
        }        
        
//        _createWidget : function(name) {
//            return this.each(function() {
//                alert(name);
//            }
//        }
    

        
    };
    
    

    $.fn.ipBlock = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };
    
   

})(jQuery);