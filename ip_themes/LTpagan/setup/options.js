function ipDesignOption_backgroundColor(value) {
    $('body').css('backgroundColor', value);
}

function ipDesignOption_mainColor(value) {
    $('.wrapper').css('borderColor', value);
    $('.topmenu').css('backgroundColor', value);
    $('.wrapper .footer').css('borderColor', value);
    $('.topmenu li.current').css('color', value);
}


