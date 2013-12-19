$(document).ready(function() {
    "use strict";

    $('.ipmControlInput').first().focus();

    $('.ipsModuleForm').validator(validatorConfig);
    $('.ipsModuleForm').submit(function (e) {
        var form = $(this);

        // client-side validation OK.
        if (!e.isDefaultPrevented()) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.status && response.status == 'success') {
                        window.location = response.redirectUrl;
                    } else {
                        //PHP controller says there are some errors
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