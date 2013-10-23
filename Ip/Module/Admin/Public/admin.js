var ipAdmin = new function () {
    "use strict";

    this.init = function () {
        $('body').prepend($(ipAdminToolbar));
        var $container = $('.ipsAdminToolbarContainer'); // the most top element physically creates a space
        var $toolbar = $('.ipsAdminToolbar'); // Administration Panel that stays always visible
        var $adminMenu = $('.ipsAdminMenuBlock').height($(window).height());
        $container.height($toolbar.outerHeight()); // setting the height to container

        $('.ipsAdminMenu').on('hover', function (e) {
            e.preventDefault();
            $adminMenu.show('slide', {direction: 'left'}, 1000);
        });

        $adminMenu.on('mouseleave', function (e) {
            e.preventDefault();
            $adminMenu.hide('slide', {direction: 'left'}, 1000);
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

};

$(document).ready(function() {
    "use strict";
    ipAdmin.init();
});
