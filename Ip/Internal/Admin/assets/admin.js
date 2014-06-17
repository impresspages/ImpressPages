$(document).ready(function () {
    "use strict";
    ipAdmin.init();
});

var ipAdmin = new function () {
    "use strict";
    var $adminMenu;
    var $adminMenuContainer;
    var $container;
    var $currentItem;
    var $navbar;

    this.init = function () {
        $(document.body).prepend($(ipAdminNavbar));
        $container = $('.ipsAdminNavbarContainer'); // the most top element physically creates a space
        $navbar = $('.ipsAdminNavbar'); // Administration Panel that stays always visible
        $currentItem = $('.ipsItemCurrent');

        $adminMenu = $('.ipsAdminMenuBlock');
        $adminMenuContainer = $('.ipsAdminMenuBlockContainer');

        $('.ipsAdminMenu').on('mouseenter', function (e) {
            showAdminMenu();
        });

        $adminMenu.on('mouseleave', function (e) {
            hideAdminMenu();
        });

        //prevent session expire
        if (typeof(ipAdminSessionRefresh) !== 'undefined') {
            setInterval(refreshSession, ipAdminSessionRefresh * 1000);
        }

        fixLayout();
        onResize();

        if (!ip.isManagementState) {
            $('.ipsContentPublish').on('click', function (e) {
                save(true)
            });
            $('.ipsContentSave').addClass('hidden');
        }

        $(window).bind('resize.ipAdmin', onResize);

        $(document).trigger('ipAdminPanelInit');
    };

    var showAdminMenu = function () {
        $currentItem.addClass('hidden');
        $adminMenu.removeClass('hidden');
    };

    var hideAdminMenu = function () {
        $currentItem.removeClass('hidden');
        $adminMenu.addClass('hidden');
        $adminMenu.focus(); //makes click outside adminMenu work as roll out.
    };

    var fixLayout = function () {
        $container.height($navbar.outerHeight()); // setting the height to container
    };

    var refreshSession = function () {
        $.ajax({
            url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
            dataType: 'json',
            type: 'GET',
            data: {sa: 'Admin.sessionRefresh'},
            success: function (response) {
                //do nothing
            }
        });
    };

    //TODO this function is duplicated in jquery.ip.contentManagement
    var save = function (publish) {
        var $this = $(this);

        var postData = Object();
        postData.aa = 'Content.savePage';
        postData.securityToken = ip.securityToken;
        postData.revisionId = ip.revisionId;
        if (publish) {
            postData.publish = 1;
        } else {
            postData.publish = 0;
        }

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: postData,
            context: $this,
            success: _savePageResponse,
            dataType: 'json'
        });
    };

    //TODO this function is duplicated in jquery.ip.contentManagement
    var _savePageResponse = function (response) {
        if (response.status == 'success') {
            window.location.href = response.newRevisionUrl;
        }
    };

    var onResize = function () {
        // Admin menu height
        var containerHeight = $(window).height() - $navbar.outerHeight();
        $adminMenuContainer.height(containerHeight);

        // elements with 'ipsAdminAutoHeight' CSS class
        var $container = $(window);
        var $elements = $('.ipsAdminAutoHeight');
        var containerHeight = parseInt($container.outerHeight());
        var navbarHeight = parseInt($('.ipsAdminNavbarContainer').outerHeight());
        if (navbarHeight > 0) {
            containerHeight -= navbarHeight; // leaving place for navbar
        }
        $elements.css('min-height', containerHeight);
    }

};
