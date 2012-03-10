(function($) {
    $.fn.ipWidgetIpUser = function() {
        return this.each(function() {
            $ipForm = $(this);
            
            $ipForm.find('form').validator();
            $ipForm.find('form').submit(function(e) {
                var form = $(this);

                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url: ip.baseUrl,
                        dataType: 'json',
                        type : 'POST',
                        data: form.serialize(),
                        success: function (response){
                            if (response.status && response.status == 'success') {
                                if (typeof ipWidgetipForm_success == 'function'){ //custom handler exists
                                    ipWidgetipForm_success($ipForm);
                                } else { //default handler
                                    $ipForm.find('.ipwThankYou').show();
                                    $ipForm.find('.ipwThankYou').height($ipForm.find('.ipwForm').height());
                                    $ipForm.find('.ipwForm').hide();
                                }
                            } else {
                                if (response.errors) {
                                    form.data("validator").invalidate(response.errors);
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
    $('.ipWidget-IpUser').ipWidgetIpUser();

});