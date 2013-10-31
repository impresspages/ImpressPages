<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    $site->addCss(\Ip\Config::libraryUrl('css/ipContent/ip_content.css'));
    echo $site->generateHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo $this->generateBlock('main'); ?>
</div>
<?php
$site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
echo $site->generateJavascript();
?>
</body>
</html>
