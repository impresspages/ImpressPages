<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    ipAddCss(\Ip\Config::themeUrl('ip_content.css'));
    ipAddCss(\Ip\Config::themeUrl('theme.css'));
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
        <?php echo $this->generateBlock('main')->render(); ?>
    </div>
    <?php
        ipAddJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        ipAddJavascript(\Ip\Config::themeUrl('theme.js'));
        echo ipJavascript();
    ?>
</body>
</html>
