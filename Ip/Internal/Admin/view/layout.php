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

    <?php ipAddCss('Ip/Internal/Ip/assets/fonts/font-awesome/font-awesome.css'); ?>
    <?php ipAddCss('Ip/Internal/Ip/assets/bootstrap/bootstrap.css'); ?>
    <?php ipAddJs('Ip/Internal/Ip/assets/bootstrap/bootstrap.js'); ?>

    <?php echo ipHead() ?>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="manage">
    <div class="ip">
        <?php echo ipBlock('main'); ?>
    </div>
<?php echo ipJs() ?>
</body>
</html>
