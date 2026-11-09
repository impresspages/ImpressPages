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
    <title><?php echo esc(!empty($adminScreenTitle) ? $adminScreenTitle : (($ipAdminScreen ? __($ipAdminScreen, 'Ip-admin', false) . ' · ' : '') . __('Administration', 'Ip-admin', false))); ?></title>

    <?php ipAddCss('Ip/Internal/Core/assets/admin/admin.css'); ?>
    <?php ipAddCss('Ip/Internal/Core/assets/admin/adminExtra.css'); ?>
    <?php ipAddCss('Ip/Internal/Core/assets/admin/adminIcons.css'); ?>

    <?php echo ipHead(); ?>
</head>
<body class="ip ipAdminBody<?php echo !empty($adminRail) ? ' ipHasRail' : ''; ?><?php echo !empty($adminSection) ? ' ipHasSection' : ''; ?>">
    <a class="ipAdminSkipLink" href="#ipAdminContent"><?php _e('Skip to content', 'Ip-admin'); ?></a>
    <?php if (!empty($adminRail)) { echo $adminRail; } ?>
    <?php if (!empty($adminSection)) { echo $adminSection; } ?>

    <?php if (empty($removeAdminContentWrapper)) { ?>
        <div class="ipsAdminAutoHeight ipAdminContentWrapper clearfix" id="ipAdminContent" role="main" tabindex="-1">
    <?php } else { ?>
        <div class="ipAdminMainBare" id="ipAdminContent" role="main">
    <?php } ?>

        <?php echo ipBlock('main'); ?>

        </div>

    <?php echo ipJs(); ?>
</body>
</html>
