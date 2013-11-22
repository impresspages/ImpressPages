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
    ipAddCss(ipConfig()->coreModuleUrl('Config/assets/admin.css'));
    ipPrintHead();
    ?>
</head>
<body>
    <?php echo empty($content) ? '' : $content ?>
<?php ipPrintJavascript() ?>
</body>
</html>
