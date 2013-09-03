var ipDesignOptions = {
    backgroundColor: function (value) {
        'use strict';
        $('body').css('backgroundColor', value);
    },
    mainColor: function (value) {
        'use strict';
        $('.wrapper').css('borderColor', value);
        $('.topmenu').css('backgroundColor', value);
        $('.wrapper .footer').css('borderColor', value);
        $('.topmenu li.current').css('color', value);

        ipDesign.reloadLessFiles(['ip_content', 'theme']);
    }
};