(function($) {
    $.fn.ipWidgetIpUserForm = function() {
        return this.each(function() {
            var $ipUserForm = $(this);
            console.log('init');
            $ipUserForm.find('form').validator();
            $ipUserForm.find('form').submit(function(e) {
                console.log('submit');
                var $form = $(this);
                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url: ip.baseUrl,
                        dataType: 'json',
                        type : 'POST',
                        data: $form.serialize(),
                        success: function (response){
                            if (!response) {
                                return;
                            }
                            if (response.status && response.status == 'success') {
                                if (response.redirectUrl) {
                                    document.location = response.redirectUrl;
                                } else {
                                    var curUrl = parent.window.location.href.split('#');
                                    document.location = curUrl[0];
                                }
                            } else {
                                if (response.errors) {
                                    $form.data("validator").invalidate(response.errors);
                                }
                            }
                        }
                      });
                }
                e.preventDefault();
            });

        });
    };
})(jQuery);


$(document).ready(function() {
    
    // IpUser widget
    $('.ipWidget-IpUserLogin').ipWidgetIpUserForm();
    $('.ipWidget-IpUserRegistration').ipWidgetIpUserForm();

});