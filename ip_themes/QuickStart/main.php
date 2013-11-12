<?php echo $this->doctypeDeclaration(); ?>
<html<?php echo $this->htmlAttributes(); ?>>
<head>
    <?php
    $site->addCss(\Ip\Config::themeUrl('ip_content.css'));
    $site->addCss(\Ip\Config::themeUrl('theme.css'));
    echo $site->generateHead();
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
        $site->addJavascript(\Ip\Config::coreModuleUrl('Assets/assets/js/jquery.js'));
        $site->addJavascript(\Ip\Config::themeUrl('theme.js'));
        echo $site->generateJavascript();
    ?>
</body>
</html>
