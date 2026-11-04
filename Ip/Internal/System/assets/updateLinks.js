$(document).ready(function () {
    "use strict";
    // Updating links rewrites all page content, so ask first.
    $('.ipsUpdateLinksForm').on('submit', function (e) {
        if (!confirm($(this).data('confirm'))) {
            e.preventDefault();
        }
    });
});
