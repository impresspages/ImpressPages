var ipDesignOptionsOpen = function (e) {
    "use strict";
    e.preventDefault();

    var bodyClassToHideScroll = 'modal-open';

    $(document.body).addClass(bodyClassToHideScroll);
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipsPreview').removeClass('hidden');
    $('.ipModuleDesign .ipsPreviewClose').off().on('click', ipDesignOptionsClose);
    $(document).off('keydown.ipDesignOptions').on('keydown.ipDesignOptions', function (e) {
        if (e.keyCode === 27 && !$('.ipModuleDesign .ipsPreview').hasClass('hidden')) {
            ipDesignOptionsClose(e);
        }
    });
};

var ipDesignOptionsClose = function (e, alreadyConfirmed) {
    "use strict";
    e.preventDefault();

    if (!alreadyConfirmed) {
        var frameWindow = $('.ipModuleDesign .ipsPreview .ipsFrame').get(0).contentWindow;
        try {
            if (frameWindow.ipDesign && frameWindow.ipDesign.isDirty()) {
                if (!ipAdminConfirmDiscard()) {
                    return;
                }
                frameWindow.ipDesign.discardChanges();
            }
        } catch (error) {
            // preview not loaded yet
        }
    }

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
