//create crossdomain socket connection

$(document).ready(function () {
    $('.ipModuleDesign .ipsOpenMarket').on('click', ipDesignThemeMarket.openMarketWindow);
    $('.ipModuleDesign .ipsThemeMarketPopupClose').on('click', ipDesignThemeMarket.closeMarketWindow);

    $('.ipModuleDesign .ipsOpenOptions').on('click', ipDesignOptionsOpen);

    // Design > Theme options in the section column links here with #options
    var openOptionsFromHash = function () {
        if (window.location.hash === '#options') {
            $('.ipModuleDesign .ipsOpenOptions').first().trigger('click');
        }
    };
    openOptionsFromHash();
    $(window).on('hashchange', openOptionsFromHash);

    $('.ipsInstallTheme').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type: 'POST',
            data: {
                'aa': 'Design.installTheme',
                'themeName': $(this).data('theme'),
                'securityToken': ip.securityToken
            },
            success: function (response) {
                if (response.status && response.status == 'success') {
                    window.location = ip.baseUrl + '?aa=Design.index';
                } else if (response.error) {
                    ipAdminNotify.error(response.error);
                }
            },
            error: function () {
                ipAdminNotify.error();
            }
        });


    });

});

