<?php ipDoctypeDeclaration(); ?>
<html<?php ipHtmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/css/ipContent/ipContent.css'));
    ipPrintHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo ipBlock('main'); ?>
</div>
<?php
ipPrintJavascript();
?>
</body>
</html>
