var ipDesignOptions = {
    textColor: function (value) {
        ipDesign.reloadLessFiles(['theme']);
    },
    linkColor: function (value) {
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
