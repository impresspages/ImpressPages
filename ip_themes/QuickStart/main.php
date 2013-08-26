<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/site.css');
    $site->addCss(BASE_URL.THEME_DIR.THEME.'/ip_content.css');
    $site->addCss(BASE_URL.LIBRARY_DIR.'js/colorbox/themes/1/colorbox.css');
    echo $site->generateHead();
    ?>
</head>
<body>
    <div class="topmenu">
        <?php echo $this->generateMenu('top', 'menu1'); ?> <!--first argument is unique name of this menu within your theme. Choose anything you like. Next argument is zone name.-->
    </div>
    <div class="content">
        <?php echo $this->generateBlock('main'); ?>
        <div class="clear"><!-- --></div>
    </div>
    <?php
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/colorbox/jquery.colorbox.js');
        $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/site.js');
        echo $site->generateJavascript();
    ?>
</body>
</html>
