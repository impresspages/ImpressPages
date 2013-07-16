/**
 * @package ImpressPages
 *
 *
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
        
        
        refreshPageData : function (pageId, zoneName) {
            var $this = this;
            
            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'getPageOptionsHtml';
            data.pageId = pageId;
            data.zoneName = zoneName;

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];
            $.ajax({
                type : 'POST',
                url : postUrl,
                data : data,
                context : $this,
                success : methods._refreshPageDataResponse,
                dataType : 'json'
            });            
        },
        
        _refreshPageDataResponse : function (response) {
            var $this = this;
            if (response.status == 'success') {
                $this.html(response.optionsHtml);
                $this.tabs();
            }

            $('.ipaOptionsConfirm').bind('click', methods._confirm);
            $('.ipaOptionsCancel').bind('click', methods._cancel);


        },
        
        _confirm : function (event) {
            var $this = $(this);
            $this.trigger('pageOptionsConfirm.ipPageOptions');
        },
        
        _cancel : function (event) {
            var $this = $(this);
            $this.trigger('pageOptionsCancel.ipPageOptions');
        },
        
        
        getPageOptions : function () {

            var data = Object();

            data.buttonTitle = $('#formGeneral input[name="buttonTitle"]').val();
            data.visible = $('#formGeneral input[name="visible"]').attr('checked') ? 1 : 0;
            data.createdOn = $('#formGeneral input[name="createdOn"]').val();
            data.lastModified = $('#formGeneral input[name="lastModified"]').val();

            data.pageTitle = $('#formSEO input[name="pageTitle"]').val();
            data.keywords = $('#formSEO textarea[name="keywords"]').val();
            data.description = $('#formSEO textarea[name="description"]').val();
            data.url = $('#formSEO input[name="url"]').val();
            data.type = $('#formAdvanced input:checked[name="type"]').val();
            data.redirectURL = $('#formAdvanced input[name="redirectURL"]').val();
            data.rss = $('#formAdvanced input[name="rss"]').attr('checked') ? 1 : 0;
            data.layout = $('#formLayout select[name="layout"]').val();

            return data;
        }
        
    };
    
    

    $.fn.ipPageOptions = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipPageOptions');
        }
    };
    
    

})(jQuery);