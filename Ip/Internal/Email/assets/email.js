
$('.ipsGrid').on('init.ipGrid', function () {
    "use strict";

    var $modal = $('#ipEmailPreviewModal');
    var $iframe = $modal.find('iframe');
    $modal.find('.ipsOk').on('click', function(e) {
        $modal.modal('hide');
    });

    $('.ipsEmailPreview').on('click', function (e) {
        e.preventDefault();
        var $previewLink = $(this);
        var id = $previewLink.closest('.ipsRow').data('id');
        $iframe.attr('src', ip.baseUrl + '?aa=Email.preview&id=' + id);
        $modal.modal();
    });
});

