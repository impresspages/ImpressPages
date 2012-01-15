

$(document).ready(function($) {
    var widgetOptions = new Object;
    $('.ipModuleNewsletter').ipModuleNewsletter();
    $('.ipModuleNewsletter').ipModuleNewsletter();
});



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
            data = $this.data('ipModuleNewsletter');
            // If the plugin hasn't been initialized yet
            if (!data) {

                /*
                 * Do more setup stuff here
                 */

                $(this).data('ipModuleNewsletter', {'init' : true});

                
                //SUBSCRIBTION
                $this.bind('subscribe.ipModuleNewsletter', function(event) {
                    $(this).ipModuleNewsletter('subscribe');
                });
                
                $this.find('.ipmForm').bind('submit', function(event) {
                    event.preventDefault();
                    $(this).trigger('subscribe.ipModuleNewsletter');
                });
                
                $this.find('.ipmSubscribe').bind('click', function(event) {
                    event.preventDefault();
                    $(this).trigger('subscribe.ipModuleNewsletter');
                });
                
                
                //UNSUBSCRIBTION
                $this.find('.ipmUnsubscribe').bind('click', function(event) {
                    event.preventDefault();
                    $(this).trigger('unsubscribe.ipModuleNewsletter');
                });
                
                $this.bind('unsubscribe.ipModuleNewsletter', function(event) {
                    $(this).ipModuleNewsletter('unsubscribe');
                });                
                

            }
        });
    },
    subscribe : function(newsletterUrl) {
        var $this = this;
        
        var data = Object();
        data.g = 'community';
        data.m = 'newsletter';
        data.a = 'subscribe';
        data.email = $this.find('.ipmInput').val();
        
        
        $.ajax({
            type : 'POST',
            url : ip.baseUrl,
            data : data,
            context : $this,
            success : methods._subscribeAnswer,
            dataType : 'json'
        });
        return false;
    },
    
    _subscribeAnswer : function (answer) {
        $this = this;
        if (answer.status == 'success') {
            window.location = answer.redirectUrl;
        } else {
            if (answer.errorMessage) {
                $this.find('.ipmError').text(answer.errorMessage);
            }
        }
    },
    
    unsubscribe : function(newsletterUrl, email) {

        var $this = this;
        
        var data = Object();
        data.g = 'community';
        data.m = 'newsletter';
        data.a = 'unsubscribe';
        data.email = $this.find('.ipmInput').val();
        
        $.ajax({
        type : 'POST',
        url : ip.baseUrl,
        data : data,
        context : $this,
        success : methods._unsubscribeAnswer,
        dataType : 'json'
        });
        return false;
    },

    _unsubscribeAnswer : function(answer) {
        $this = this;
        if (answer.status == 'success') {
            window.location = answer.redirectUrl;
        } else {
            if (answer.errorMessage) {
                $this.find('.ipmError').text(answer.errorMessage);
            }
        }
    }

    };

    $.fn.ipModuleNewsletter = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipModuleNewsletter');
        }

    };

})(jQuery);
