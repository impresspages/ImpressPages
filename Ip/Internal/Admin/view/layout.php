<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php ipDoctypeDeclaration(); ?>
<html<?php ipHtmlAttributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/fonts/font-awesome/font-awesome.css')); ?>
    <?php ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.css')); ?>
    <?php ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.js')); ?>

    <?php ipPrintHead() ?>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="manage">
    <div class="ip">
        <?php echo ipBlock('main'); ?>
    </div>
<?php ipPrintJavascript() ?>
</body>
</html>
