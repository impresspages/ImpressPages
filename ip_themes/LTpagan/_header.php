<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css'); ?>
    <?php $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css'); ?>
    <?php $site->addCss(BASE_URL.THEME_DIR.THEME.'/theme.css'); ?>
    <?php echo $site->generateHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<div class="container_12 wrapper">
    <header class="grid_12">
        <?php echo $this->generateManagedLogo(); ?>
        <?php echo $this->generateBlock('ipSitemap'); ?>
        <div class="languages">
            <?php echo $this->generateBlock('ipLanguages'); ?>
        </div>
        <?php echo $this->generateBlock('ipSearch'); ?>
        <?php echo $this->generateManagedImage('homeImage1', THEME_DIR.THEME.'/img/header.jpg', array('width' => '940'), 'banner'); ?>
        <div class="topmenu clearfix">
            <?php
                //first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name. They don't have to be equal.
                echo $this->generateMenu('top', 'menu1');
            ?>
        </div>
    </header>
