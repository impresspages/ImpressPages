// defining global variables
var ipAdmin;

(function($){
    "use strict";

    $(document).ready(function() {
        ipAdmin.init();
    });

    ipAdmin = new function () {
        var $adminMenu;
        var $container;
        var $currentItem;

        this.init = function () {
            $('body').prepend($(ipAdminToolbar));
            $container = $('.ipsAdminToolbarContainer'); // the most top element physically creates a space
            $currentItem = $('.ipsItemCurrent');

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
            $currentItem.hide();
            $adminMenu.show();
        };

        var hideAdminMenu = function () {
            $currentItem.show();
            $adminMenu.hide();
            $adminMenu.focus(); //makes click outside adminMenu work as roll out.
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

})(ip.jQuery);
