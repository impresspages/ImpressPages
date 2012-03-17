(function($) {
    $.fn.ipWidgetIpUserForm = function() {
        return this.each(function() {
            var $ipUserForm = $(this);
            $ipUserForm.find('form').validator();
            $ipUserForm.find('form').submit(function(e) {
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
                                }
                            } else {
                                if (response.errors) {
                                    $form.data("validator").invalidate(response.errors);
                                }
                            }
                        },
                        error: function () {
                            console.log('error');
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
    $('.ipWidget-IpUserProfile').ipWidgetIpUserForm();
    $('.ipWidget-IpUserPasswordReset').ipWidgetIpUserForm();

});