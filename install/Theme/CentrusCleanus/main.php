<?php //echo $this->doctypeDeclaration(); ?>
<html<?php //echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(ipConfig()->themeUrl('ipContent.css'));
    ipAddCss(ipConfig()->themeUrl('theme.css'));

    ?>
</head>
<body>
    <div class="topmenu">
        <?php
        ?>
    </div>
    <div class="content">
        <?php echo ipBlock('main') ?>
    </div>
    <?php
//        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
//        ipAddJavascript(ipConfig()->themeUrl('theme.js'));
//        ipPrintJavascript();
    ?>
</body>
</html>
