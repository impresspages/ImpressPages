

var ipDesignOpenOptions = function (e) {
    "use strict";
    e.preventDefault();

    if (top.document.getElementById('adminFrameset')) {
        adminFramesetRows = top.document.getElementById('adminFrameset').rows;
        top.document.getElementById('adminFrameset').rows = "0px,*";
    }


    $('body').addClass('ipgStopScrolling');
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', ip.baseUrl + '?ipDesignPreview=1');
    $('.ipModuleDesign .ipsPreview').show();
    $('.ipModuleDesign .ipsPreviewClose').off().on('click', ipDesignCloseOptions);
};

var ipDesignCloseOptions = function (e) {
    "use strict";
    e.preventDefault();

    if (top.document.getElementById('adminFrameset')) {
        top.document.getElementById('adminFrameset').rows = adminFramesetRows;
    }

    $('body').removeClass('ipgStopScrolling');
    $('.ipModuleDesign .ipsPreview').hide();
    $('.ipModuleDesign .ipsPreview .ipsFrame').attr('src', '');
};


