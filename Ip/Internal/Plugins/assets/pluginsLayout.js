var ipPluginsResize;

(function ($) {
    "use strict";

    ipPluginsResize = function () {
        var $window = $(window);
        var $container = $('.ipsModulePlugins');
        var $containerForScroll = $('.ipsModulePluginsContainer');
        var $plugins = $('.ipsPlugins');
        var $properties = $('.ipsProperties');

        var navbarHeight = parseInt($('.ipsAdminNavbarContainer').outerHeight());

        var contentHeight = parseInt($window.height());
        if (navbarHeight > 0) {
            contentHeight -= navbarHeight; // leaving place for navbar
        }

        var windowWidth = parseInt($window.width());
        var propertiesWidth = windowWidth;
        propertiesWidth -= parseInt($plugins.outerWidth());

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
        $containerForScroll.innerWidth(containerWidth);
        $properties.innerWidth(propertiesWidth);
    };

    $(document).ready(function () {
        ipPluginsResize();
    });

    $(window).bind('resize.ipPlugins,ipAdminPanelInit', ipPluginsResize);

})(jQuery);
