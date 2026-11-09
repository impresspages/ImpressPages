$(document).ready(function () {
    "use strict";
    // Updating links rewrites all page content, so ask first.
    $('.ipsUpdateLinksForm').on('submit', function (e) {
        var form = this;
        e.preventDefault();
        ipAdminConfirm({
            title: $(form).data('confirm-title'),
            text: $(form).data('confirm'),
            confirmLabel: $(form).data('confirm-button'),
            danger: true
        }).then(function (confirmed) {
            if (confirmed) {
                form.submit(); // native submit, doesn't run this handler again
            }
        });
    });
});
