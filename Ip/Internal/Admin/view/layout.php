<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
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
        <div class="ipAdminSubmenuContent clearfix">
    <?php } ?>

    <?php echo ipBlock('main'); ?>

    <?php if (!empty($submenu)) { ?>
        </div>
    <?php } ?>

    <?php echo ipJs(); ?>
</body>
</html>
