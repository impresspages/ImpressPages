


function ipDesignOption_backgroundColor(value) {
    "use strict";
    $('body').css('backgroundColor', value);
}

function ipDesignOption_mainColor(value) {
    "use strict";
    $('.wrapper').css('borderColor', value);
    $('.topmenu').css('backgroundColor', value);
    $('.wrapper .footer').css('borderColor', value);
    $('.topmenu li.current').css('color', value);

    ipDesign.reloadLessFile(['ip_content', 'theme']);


}

