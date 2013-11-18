<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(\Ip\Config::coreModuleUrl('Assets/assets/css/ipContent/ip_content.css'));
    ipPrintHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo ipBlock('main'); ?>
</div>
<?php
ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
echo ipJavascript();
?>
</body>
</html>
