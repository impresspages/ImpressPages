$( document ).ready(function () {
    "use strict";
    $('.ipsPaymentMethod').on('click', function (e) {
        e.preventDefault();
        var postData = {
            paymentMethod: $(this).data('name'),
            securityToken: ip.securityToken
        };
        $.ajax({
            url: '',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.redirect) {
                    document.location = response.redirect;
                } else if (response && response.error && response.error.message) {
                    alert(response.error.message);
                } else {
                    alert(response.responseText);
                }
            },
            error: function (response) {
                alert('Unexpected error.' + response.responseText);
            }
        });
    });
});
