var ipDesignOptions = {
    ipTextColor: function (value) {
        ipDesign.reloadLessFiles(['ip_content', 'theme']);
    },
    ipLinkColor: function (value) {
        ipDesign.reloadLessFiles(['ip_content', 'theme']);
    },
    bodyBackgroundColor: function (value) {
        'use strict';
        $('body').css('background-color', value);
    },
    ipBackgroundColor: function (value) {
        'use strict';
        $('.theme').css('background-color', value);
    }
};
