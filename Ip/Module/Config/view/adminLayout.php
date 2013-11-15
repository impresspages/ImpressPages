<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>

<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(\Ip\Config::coreModuleUrl('Config/public/admin.css'));
    echo ipHead();
    ?>
</head>
<body>
    <?php echo empty($content) ? '' : $content ?>
<?php
echo ipJavascript();
?>
</body>
</html>
