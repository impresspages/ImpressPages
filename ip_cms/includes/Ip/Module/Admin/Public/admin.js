var ipAdmin = new function () {
    "use strict";
    var $adminMenu = $(ipAdminMenuHtml);

    this.init = function () {
        $('body').append($adminMenu);
        $('body').append($(ipAdminToolbar));
        $('body').css('paddingTop', '30px');


        $('.ipsAdminMenu').on('hover', function (e) {
            e.preventDefault();
            showAdminMenu();
        });

        $adminMenu.on('mouseleave', function (e) {
            e.preventDefault();
            hideAdminMenu();
        });

        //prevent session expire
        if (typeof(ipAdminSessionRefresh) !== 'undefined') {
            setInterval(refreshSession, ipAdminSessionRefresh * 1000);
        }

    };

    var refreshSession = function () {
        $.ajax({
            url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
            dataType: 'json',
            type : 'GET',
            data: {sa: 'Admin.sessionRefresh'},
            success: function (response) {
                //do nothing
            }
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
    };

};

$(document).ready(function() {
    "use strict";
    ipAdmin.init();
});
