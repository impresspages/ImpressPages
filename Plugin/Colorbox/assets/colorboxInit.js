$(document).ready(function () {
    "use strict";
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
