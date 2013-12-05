<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipFileUrl('Ip/Module/Ip/assets/css/ipContent/ipContent.css'));
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
