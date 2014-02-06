
var ipDesignOpenOptions = function (e) {
    "use strict";
    e.preventDefault();


    $('body').addClass('ipgStopScrolling');
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipsPreview').show();
    $('.ipModuleDesign .ipsPreviewClose').off().on('click', ipDesignCloseOptions);
};

var ipDesignCloseOptions = function (e) {
    "use strict";
    e.preventDefault();

    $('body').removeClass('ipgStopScrolling');
    $('.ipModuleDesign .ipsPreview').hide();
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', '');
};


