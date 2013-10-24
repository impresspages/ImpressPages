var ipAdmin = new function () {
    "use strict";
    var $adminMenu;
    var $container;

    this.init = function () {
        $('body').prepend($(ipAdminToolbar));
        $container = $('.ipsAdminToolbarContainer'); // the most top element physically creates a space

        $adminMenu = $('.ipsAdminMenuBlock');

        $('.ipsAdminMenu').on('mouseenter', function (e) { showAdminMenu(); });

        $adminMenu.on('mouseleave', function (e) { hideAdminMenu(); });

        //prevent session expire
        if (typeof(ipAdminSessionRefresh) !== 'undefined') {
            setInterval(refreshSession, ipAdminSessionRefresh * 1000);
        }

        $(window).on('resize', fixLayout);
        fixLayout();
    };


    var showAdminMenu = function () {
        var newWidth = 200;
        $adminMenu.width(0);
        $adminMenu.show();
        $adminMenu.animate({
            width: newWidth + 'px'
        }, 200);
    };

    var hideAdminMenu = function () {
        $('body').removeClass('ipAdminNoScroll');
        var newWidth = 0;
        $adminMenu.width('auto');
        $adminMenu.animate({
            width: newWidth + 'px'
        }, 200, function(){$(this).hide();});
    };

    var fixLayout = function () {
        var $toolbar = $('.ipsAdminToolbar'); // Administration Panel that stays always visible
        $adminMenu.height($(window).height());
        $container.height($toolbar.outerHeight()); // setting the height to container
    }


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
