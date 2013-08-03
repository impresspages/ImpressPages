
$(document).ready(function() {
    $('body').append(ipModuleDesignConfiguration);
    $('a').off('click').on('click', ipDesign.openLink);
});


var ipDesign = new function() {

    this.openLink = function (e) {
        e.preventDefault();
        var href = $(e.currentTarget).attr('href');
        href = href + '?ipDesignPreview=1';
        window.location = href;
    }

    var internalFunction = function() {

    };

    this.publicFunction = function() {

    };
};