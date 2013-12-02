<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipFileUrl('Ip/Module/Assets/assets/css/ipContent/ipContent.css'));
    ipPrintHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo ipBlock('main'); ?>
</div>
<?php
ipAddJavascript(ipFileUrl('Ip/Module/Assets/assets/js/jquery.js'));
ipPrintJavascript();
?>
</body>
</html>
