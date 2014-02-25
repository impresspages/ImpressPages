$(document).ready(function () {
    "use strict";

    console.log(ip);
    if (ip.isManagementState) {
        return;
    }
    $('a[rel=lightbox]').colorbox({
        rel: 'ipwImage',
        maxWidth: '90%',
        maxHeight: '90%'
    });
    $('a[rel=standaloneLightbox]').colorbox({
        maxWidth: '90%',
        maxHeight: '90%'
    });
});
