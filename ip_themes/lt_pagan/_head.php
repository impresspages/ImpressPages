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
    <?php

    $site->addCss(BASE_URL.THEME_DIR.THEME.'/960.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/site.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css');
    $site->addCss($site->compileThemeLess(THEME, 'theme.less'));
    $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/2/colorbox.css');
    echo $site->generateHead();
    ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<div class="container_12 wrapper <?php echo $this->getThemeOption('style') ?>">
    <header class="grid_12">
        <?php echo $this->generateManagedLogo(); ?>
        <?php echo $this->generateBlock('ipSitemap'); ?>
        <div class="languages">
            <?php echo $this->generateBlock('ipLanguages'); ?>
        </div>
        <?php echo $this->generateBlock('ipSearch'); ?>
        <?php echo $this->generateManagedImage('ipThemeBanner/lt_pagan', THEME_DIR.THEME.'/img/header.jpg', array('width' => '940'), 'banner'); ?>
        <div class="topmenu clearfix">
            <?php
            //first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name. They don't have to be equal.
            echo $this->generateMenu('top', 'menu1');
            ?>
        </div>
    </header>
