<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Core/assets/admin/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/login.css'); ?>">
    <link rel="shortcut icon" href="<?php echo ipFileUrl('favicon.ico'); ?>">
</head>
<body>


<a href="http://www.impresspages.org/" class="logo" target="_blank"><img src="<?php echo ipFileUrl('Ip/Internal/Admin/assets/img/logo.png'); ?>"></a>
<div class="ip languageSelect">
    <?php echo $languageSelectForm->render(); ?>
</div>
<div class="verticalAlign"></div>
<div class="login">
    <?php echo $content; ?>
</div>
<div class="loginFooter">Copyright 2009-<?php echo date("Y"); ?> by <a href="http://www.impresspages.org/">ImpressPages, UAB</a></div>

<?php echo ipJs(); ?>
</body>
</html>
