
/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */


$(document).ready(function($) {
    $('.ipWidget-IpNewsletter').ipModuleNewsletter();
});



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
    
                    $this.find('form').validator(validatorConfig);
                    $this.find('form').submit(function(e) {
    
                        // client-side validation OK.
                        if (!e.isDefaultPrevented()) {
                            if ($(this).data('tmp').buttonClicked == 'subscribe') {
                                $(this).ipModuleNewsletter('subscribe');
                            } else  {
                                $(this).ipModuleNewsletter('unsubscribe');
                            }
                        }
                        e.preventDefault();
                    });
    
                }
            });
        },
        subscribe : function() {
            var $this = this;
            var data = Object();
            data.g = 'community';
            data.m = 'newsletter';
            data.a = 'subscribe';
            data.email = $this.find('input[name=email]').val();
            
            $.ajax({
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                context : $this,
                success : _subscribeAnswer,
                dataType : 'json'
            });
            return false;
        },
        

        
        unsubscribe : function(newsletterUrl, email) {
    
            var $this = this;
            
            var data = Object();
            data.g = 'community';
            data.m = 'newsletter';
            data.a = 'unsubscribe';
            data.email = $this.find('input[name=email]').val();
            
            $.ajax({
            type : 'POST',
            url : ip.baseUrl,
            data : data,
            context : $this,
            success : _unsubscribeAnswer,
            dataType : 'json'
            });
            return false;
        }
    

    };

    var _unsubscribeAnswer = function(answer) {
        var $this = this;
        if (answer.status == 'success') {
            window.location = answer.redirectUrl;
        } else {
            if (answer.errorMessage) {
                $this.data("validator").invalidate({email:answer.errorMessage});
            }
        }
    };
    
    var _subscribeAnswer = function (answer) {
        var $this = this;
        if (answer && answer.status == 'success') {
            window.location = answer.redirectUrl;
        } else {
            if (answer && answer.errorMessage) {
                $this.data("validator").invalidate({email:answer.errorMessage});
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
