var ipDesignOptions = {
    textColor: function (value) {
        $('.ipModuleForm .ipmLabel, .ipWidget-IpTable, .ipWidget-IpTable td, .ipWidget-Text, .ipWidget-TextImage, .ipWidget-Faq').css('color', value);
        ipDesign.reloadLessFiles(['theme']);
    },
    linkColor: function (value) {
        $('footer a, .ipWidget-IpTable a, .ipWidget-Text a, .ipWidget-TextImage a, .ipWidget-Faq a, .ipWidget-File a').css('color', value);
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
