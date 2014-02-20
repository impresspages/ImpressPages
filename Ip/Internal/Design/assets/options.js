var ipDesignOptionsOpen;
var ipDesignOptionsClose;
var ipDesignOptionsResize;

(function ($) {
    "use strict";
    ipDesignOptionsOpen = function (e) {
        e.preventDefault();

        var bodyClassToHideScroll = 'modal-open';

        $(document.body).addClass(bodyClassToHideScroll);
        $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
        $('.ipModuleDesign .ipsPreview').removeClass('hidden');
        $('.ipModuleDesign .ipsPreviewClose').off().on('click', ipDesignOptionsClose);
    };

    ipDesignOptionsClose = function (e) {
        e.preventDefault();

        var bodyClassToHideScroll = 'modal-open';

        $(document.body).removeClass(bodyClassToHideScroll);
        $('.ipModuleDesign .ipsPreview').addClass('hidden');
        $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', '');
    };

    ipDesignOptionsResize = function(e) {
        var $popup = $('.ipModuleDesign .ipsPreview');
        var height = parseInt($(window).height());
        height -= 40; // leaving place for navbar
        $popup.height(height + 'px');
    };

    ipDesignOptionsResize();
    $(window).bind('resize.ipDesignOptions', ipDesignOptionsResize);
})(ip.jQuery);
