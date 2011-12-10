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
                var data = $this.data('ipPageOptions');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipPageOptions', {
                    }); 
                }
            });
        },
        
        
        getData : function (pageId, zoneName) {
            var $this = this;
            
            data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'getPageOptionsHtml';
            data.pageId = pageId;
            data.zoneName = zoneName;
    
            $.ajax({
                type : 'POST',
                url : document.location,
                data : data,
                context : $this,
                success : methods._getDataResponse,
                dataType : 'json'
            });            
        },
        
        _getDataResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                $this.html(response.optionsHtml);
                $this.tabs('destroy');
                $this.tabs();
            }
            console.log('response');
        }
        
    };
    
    

    $.fn.ipPageOptions = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }
    };
    
    

})(jQuery);