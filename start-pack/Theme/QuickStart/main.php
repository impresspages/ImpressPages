<?php // @Layout name: Main ?>
<?php echo ipDoctypeDeclaration(); ?>
<html<?php echo ipHtmlAttributes(); ?>>
<head>
    <?php
        ipAddCss('Ip/Internal/Core/assets/ipContent/ipContent.css'); // include default CSS for widgets
        ipAddCss('assets/theme.css');
        echo ipHead();
    ?>
</head>
<body>
    <div class="topmenu">
        <?php echo ipSlot('menu', 'menu1'); ?>
    </div>
    <div class="content">
        <?php echo ipBlock('main')->render(); ?>
    </div>
    <?php
        ipAddJs('assets/theme.js');
        echo ipJs();
    ?>
</body>
</html>
