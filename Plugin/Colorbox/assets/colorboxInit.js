$(document).ready(function () {
    "use strict";

    if (ip.isManagementState) {
        return;
    }
    $('a[rel*=lightbox]').colorbox({
        rel: 'ipwImage',
        maxWidth: '90%',
        maxHeight: '90%',
        title: function(){return $(this).attr('title') + ($(this).data('description') ? '. ' + $(this).data('description') : '');}
    });
    $('a[rel=standaloneLightbox]').colorbox({
        maxWidth: '90%',
        maxHeight: '90%'
    });
});
