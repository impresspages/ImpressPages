<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(\Ip\Config::libraryUrl('css/ipContent/ip_content.css'));
    echo ipHead();
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
