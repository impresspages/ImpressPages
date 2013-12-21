<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php ipDoctypeDeclaration(); ?>
<html<?php ipHtmlAttributes(); ?>>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php ipAddCss(ipThemeUrl('assets/theme.css')); ?>
    <?php echo ipHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="theme clearfix">
    <header class="clearfix col_12">
        <?php ipSlot('Ip.logo') ?>
        <div class="right">
            <span class="currentPage"><?php echo esc(ipContent()->getCurrentPage()->getNavigationTitle()) ?></span>
            <a href="#" class="topmenuToggle">&nbsp;</a>
            <div class="topmenu">
                <?php ipSlot('Ip.menu', 'menu1'); ?>
                <div class="languages">
                    <?php ipSlot('Ip.languages'); ?>
                </div>
            </div>

            <a href="#" class="searchToggle">&nbsp;</a>
            <?php ipSlot('Search'); //TODOX review ?>
        </div>
    </header>
