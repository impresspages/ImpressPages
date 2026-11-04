var ipPagesResize;

(function ($) {
    "use strict";

    function getQuery(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    ipPagesResize = function () {
        var $window = $(window);
        var $container = $('.ipsAdminPages');
        var $containerForScroll = $('.ipsAdminPagesContainer');
        var $menus = $('.ipsMenus');
        var $pages = $('.ipsPagesContainer');
        var $properties = $('.ipsProperties');

        var navbarHeight = parseInt($('.ipsAdminNavbarContainer').outerHeight());

        var contentHeight = parseInt($window.height());
        if (navbarHeight > 0) {
            contentHeight -= navbarHeight; // leaving place for navbar
        }

        // the icon rail sits to the left of the Pages columns
        var windowWidth = parseInt($window.width()) - (parseInt($('.ipsAdminRail').outerWidth()) || 0);
        var propertiesWidth = windowWidth;
        propertiesWidth -= parseInt($menus.outerWidth()) || 0;
        propertiesWidth -= parseInt($pages.outerWidth()) || 0;

        var minimumPropertiesWidth = 400;
        var maximumPropertiesWidth = 680;
        var containerWidth = windowWidth;

        // if properties doesn't fit, fix
        if (propertiesWidth < minimumPropertiesWidth) {
            containerWidth += minimumPropertiesWidth - propertiesWidth;
            propertiesWidth = minimumPropertiesWidth;
        }

        // don't allow properties to be to wide
        if (propertiesWidth > maximumPropertiesWidth) {
            propertiesWidth = maximumPropertiesWidth;
        }

        $container.innerHeight(contentHeight);
        // add scrollbar only if properties will be visible
        if (!getQuery('disableActions')) {
            $containerForScroll.innerWidth(containerWidth);
        }
//        $languages.innerHeight(contentHeight);
//        $menus.innerHeight(contentHeight);
//        $pages.innerHeight(contentHeight);
//        $properties.innerHeight(contentHeight);
        $properties.innerWidth(propertiesWidth);
    };

    $(document).ready(function () {
        ipPagesResize();
    });

    $(window).bind('resize.ipPages,ipAdminPanelInit', ipPagesResize);

})(jQuery);
