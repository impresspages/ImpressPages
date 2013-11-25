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
    <?php ipAddThemeAsset('colorbox/themes/' . $this->getThemeOption('lightboxStyle').'/colorbox.css'); ?>
    <?php ipAddThemeAsset('theme.css'); ?>
    <?php ipPrintHead(); ?>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
<div class="theme clearfix">
    <header class="clearfix col_12">
        <?php echo ipSlot('Ip.logo') ?>
        <div class="right">
            <span class="currentPage"><?php echo esc(ipContent()->getCurrentPage()->getButtonTitle()) ?></span>
            <a href="#" class="topmenuToggle">&nbsp;</a>
            <div class="topmenu">
                <?php
                    echo ipSlot('Ip.menu', 'menu1');
                ?>
                <div class="languages">
                    <?php echo $this->generateSlot('Ip.languages'); ?>
                </div>
            </div>

            <a href="#" class="searchToggle">&nbsp;</a>
            <?php echo ipSlot('Search'); //TODOX review ?>
        </div>
    </header>
