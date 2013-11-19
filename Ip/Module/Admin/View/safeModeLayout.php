<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipGetConfig()->coreModuleUrl('Assets/assets/css/ipContent/ip_content.css'));
    ipPrintHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo ipBlock('main'); ?>
</div>
<?php
ipAddJavascript(ipGetConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
ipPrintJavascript();
?>
</body>
</html>
