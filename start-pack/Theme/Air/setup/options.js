var ipDesignOptions = {
    textColor: function (value) {
        $('.ipModuleForm .ipmLabel, .ipWidget-IpText td, .ipWidget-Text').css('color', value);
        ipDesign.reloadLessFiles(['theme']);
    },
    linkColor: function (value) {
        $('footer a, .ipWidget-Text a, .ipWidget-File a').css('color', value);
        ipDesign.reloadLessFiles(['theme']);
    },
    bodyBackgroundColor: function (value) {
        'use strict';
        $('body').css('background-color', value);
    },
    backgroundColor: function (value) {
        'use strict';
        $('.wrapper').css('background-color', value);
    }
};
