var ipAdmin = new function () {
    "use strict";
    var $adminMenu = $(ipAdminMenuHtml);

    this.init = function () {
        $('body').append($adminMenu);
        $('body').append($(ipAdminToolbar));

        $('.ipsAdminMenu').on('hover', function (e) {
            e.preventDefault();
            showAdminMenu();
        });

        $adminMenu.on('mouseleave', function (e) {
            e.preventDefault();
            hideAdminMenu();
        });
    };

    var showAdminMenu = function () {
        var newWidth = 200;
        $adminMenu.animate({
            width: newWidth + 'px'
        }, 200);


    };

    var hideAdminMenu = function () {
        var newWidth = 0;
        $adminMenu.animate({
            width: newWidth + 'px'
        }, 200);
    }
};

$(document).ready(function() {
    "use strict";
    ipAdmin.init();
});
