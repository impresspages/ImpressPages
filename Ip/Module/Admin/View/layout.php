<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $site->addCss(BASE_URL . 'ip_cms/modules/administrator/theme/public/theme.css'); ?>
    <?php echo $site->generateHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<?php echo $this->generateBlock('main'); ?>

<?php echo $site->generateJavascript(); ?>
</body>
</html>