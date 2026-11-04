<?php echo ipDoctypeDeclaration(); ?>
<html<?php echo ipHtmlAttributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        // Page title names the admin screen, e.g. "Pages · Administration"
        $ipAdminScreen = explode('.', (string)ipRequest()->getRequest('aa', ''))[0];
        if ($ipAdminScreen == 'Email') {
            $ipAdminScreen = 'Email log';
        }
    ?>
    <title><?php echo esc(($ipAdminScreen ? __($ipAdminScreen, 'Ip-admin', false) . ' · ' : '') . __('Administration', 'Ip-admin', false)); ?></title>

    <?php ipAddCss('Ip/Internal/Core/assets/admin/admin.css'); ?>
    <?php ipAddCss('Ip/Internal/Core/assets/admin/adminExtra.css'); ?>
    <?php ipAddCss('Ip/Internal/Core/assets/admin/adminIcons.css'); ?>

    <?php echo ipHead(); ?>
</head>
<body class="ip ipAdminBody">
    <a class="ipAdminSkipLink" href="#ipAdminContent"><?php _e('Skip to content', 'Ip-admin'); ?></a>
    <?php if (!empty($submenu)) { ?>
        <div class="ipAdminSubmenu" role="navigation" aria-label="<?php _e('Section', 'Ip-admin'); ?>">
            <?php echo ipSlot('menu', $submenu); ?>
        </div>
    <?php } ?>

    <?php if (empty($removeAdminContentWrapper)) { ?>
        <div class="ipsAdminAutoHeight ipAdminContentWrapper clearfix<?php if (!empty($submenu)) { ?> _hasSubmenu<?php } ?>" id="ipAdminContent" role="main" tabindex="-1">
    <?php } ?>

        <?php echo ipBlock('main'); ?>

    <?php if (empty($removeAdminContentWrapper)) { ?>
        </div>
    <?php } ?>

    <?php echo ipJs(); ?>
</body>
</html>
