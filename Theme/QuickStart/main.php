<?php echo ipDoctypeDeclaration(); ?>
<html<?php echo ipHtmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipThemeUrl('ipContent.css'));
    ipAddCss(ipThemeUrl('theme.css'));
    echo ipHead();
    ?>
</head>
<body>
    <div class="topmenu">
        <?php
        echo $this->generateMenu('top', 'menu1'); // first argument is unique name of this menu within your theme. Choose anything you like. Second argument is a zone name
        ?>
    </div>
    <div class="content">
        <?php echo ipBlock('main')->render(); ?>
    </div>
    <?php
        ipAddJs(ipThemeUrl('theme.js'));
        echo ipJs();
    ?>
</body>
</html>
