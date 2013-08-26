function ipDesignOption_backgroundColor(value) {
    $('body').css('backgroundColor', value);
}

function ipDesignOption_color1(value) {
    $('.wrapper').css('borderColor', value);
    $('.topmenu').css('backgroundColor', value);
    $('.wrapper .footer').css('borderColor', value);
    $('.topmenu li.current').css('color', value);
}

function ipDesignOption_wrapperBackgroundColor(value) {
    $('.wrapper').css('backgroundColor', value);
}

function ipDesignOption_textColor(value) {
    $('body').css('color', value);
}

function ipDesignOption_style(value) {
    $('body .wrapper').removeClass('Dark Light Red').addClass(value);
}