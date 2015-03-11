var ipDesignOptionsOpen = function (e) {
    "use strict";
    e.preventDefault();

    var bodyClassToHideScroll = 'modal-open';

    $(document.body).addClass(bodyClassToHideScroll);
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipsPreview').removeClass('hidden');
    $('.ipModuleDesign .ipsPreviewClose').off().on('click', ipDesignOptionsClose);
};

var ipDesignOptionsClose = function (e) {
    "use strict";
    e.preventDefault();

    var bodyClassToHideScroll = 'modal-open';

    if(!$('.modal[aria-hidden=false]').length) {
        $(document.body).removeClass(bodyClassToHideScroll);
    }
    $('.ipModuleDesign .ipsPreview').addClass('hidden');
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', '');
};

var ipDesignOptionsResize = function (e) {
    "use strict";
    var $popup = $('.ipModuleDesign .ipsPreview');
    var height = parseInt($(window).height());
    height -= 40; // leaving place for navbar
    $popup.height(height + 'px');
};

ipDesignOptionsResize();
$(window).bind('resize.ipDesignOptions', ipDesignOptionsResize);
