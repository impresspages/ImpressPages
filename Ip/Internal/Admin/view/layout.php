<?php echo ipDoctypeDeclaration(); ?>
<html<?php echo ipHtmlAttributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php ipAddCss('Ip/Internal/Core/assets/admin/admin.css'); ?>

    <?php echo ipHead(); ?>
</head>
<body class="ip ipAdminBody">
    <?php if (!empty($submenu)) { ?>
        <div class="ipAdminSubmenu">
            <?php echo ipSlot('menu', $submenu); ?>
        </div>
    <?php } ?>

    <?php if (empty($removeAdminContentWrapper)) { ?>
        <div class="ipsAdminAutoHeight ipAdminContentWrapper clearfix<?php if (!empty($submenu)) { ?> _hasSubmenu<?php } ?>">
    <?php } ?>

        <?php echo ipBlock('main'); ?>

    <?php if (empty($removeAdminContentWrapper)) { ?>
        </div>
    <?php } ?>

    <?php echo ipJs(); ?>
</body>
</html>
