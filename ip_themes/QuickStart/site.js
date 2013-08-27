$(document).ready(function() {
    // Enabling lightbox (colorbox) support for images
    jQuery('.ipWidget-IpImageGallery li a, .ipWidget-IpImage a').colorbox({
        rel:'ipwImage',
        maxWidth:'90%',
        maxHeight:'90%'
    });
});
