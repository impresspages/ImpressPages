<?php echo ipDoctypeDeclaration(); ?>
<html<?php echo ipHtmlAttributes(); ?>>
<head>
    <?php
    ipAddCss('Ip/Internal/Core/assets/ipContent/ipContent.css');
    echo ipHead();
    ?>
</head>
<body>
<div class="content">
    <?php echo ipBlock('main'); ?>
</div>
<?php echo ipJs(); ?>
</body>
</html>
