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

    <?php ipAddCss('Ip/Internal/Ip/assets/admin/admin.css'); ?>
    <?php ipAddJs('Ip/Internal/Ip/assets/admin/bootstrap.js'); ?>

    <?php echo ipHead(); ?>
</head>
<body class="ip ipAdminBody">
    <?php echo ipBlock('main'); ?>
    <?php echo ipJs(); ?>
</body>
</html>
