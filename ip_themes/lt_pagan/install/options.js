function ipDesignOption_backgroundColor(value) {
    $('body').css('backgroundColor', value);
}

function ipDesignOption_textColor(value) {
    $('body').css('color', value);
}

function ipDesignOption_style(value) {
    $('body .wrapper').removeClass('Dark Light Red').addClass(value);
}