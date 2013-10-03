var ipDesignOptions = {
    textColor: function (value) {
        $('.ipModuleForm .ipmLabel, .ipWidget-IpTable, .ipWidget-IpTable td, .ipWidget-IpText, .ipWidget-IpTextImage, .ipWidget-IpFaq').css('color', value);
        ipDesign.reloadLessFiles(['theme']);
    },
    linkColor: function (value) {
        $('footer a, .ipWidget-IpTable a, .ipWidget-IpText a, .ipWidget-IpTextImage a, .ipWidget-IpFaq a, .ipWidget-IpFile a').css('color', value);
        ipDesign.reloadLessFiles(['theme']);
    },
    bodyBackgroundColor: function (value) {
        'use strict';
        $('body').css('background-color', value);
    },
    backgroundColor: function (value) {
        'use strict';
        $('.theme').css('background-color', value);
    }
};
