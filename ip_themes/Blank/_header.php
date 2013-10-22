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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/'.$this->getThemeOption('lightboxStyle').'/colorbox.css'); ?>
    <?php $site->addCss(BASE_URL.THEME_DIR.THEME.'/theme.css'); ?>
    <?php echo $site->generateHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body<?php if ($site->managementState()) { echo ' class="manage"'; } ?> >

<div class="theme clearfix">
    <header class="clearfix col_12">
        <?php echo $this->generateManagedLogo(); ?>

        <div class="right">
            <span class="currentPage"><?php echo $site->getCurrentElement()->getButtonTitle(); ?></span>
            <a href="#" class="topmenuToggle">&nbsp;</a>
            <div class="topmenu">
                <?php
                    //first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name. They don't have to be equal.
                    echo $this->generateMenu('top', 'menu1');
                ?>
                <div class="languages">
                    <?php echo $this->generateSlot('ipLanguages'); ?>
                </div>
            </div>

            <a href="#" class="searchToggle">&nbsp;</a>
            <?php echo $this->generateSlot('ipSearch'); ?>
        </div>
    </header>
