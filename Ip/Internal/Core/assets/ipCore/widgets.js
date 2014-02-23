
/*************
 * Form widget
 **************/

(function($) {
    $.fn.ipWidgetForm = function() {
        return this.each(function() {
            var $ipForm = $(this);

            $ipForm.find('form').validator(validatorConfig);
            $ipForm.find('form').submit(function(e) {
                var form = $(this);

                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url : ip.baseUrl,
                        dataType: 'json',
                        type : 'POST',
                        data: form.serialize(),
                        success: function (response){
                            if (response.status && response.status == 'success') {
                                if (typeof ipWidgetFormSuccess == 'function'){ //custom handler exists
                                    ipWidgetFormSuccess($ipForm);
                                } else { //default handler
                                    $ipForm.find('.ipwSuccess').show();
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


