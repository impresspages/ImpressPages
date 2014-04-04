<!DOCTYPE html>
<html>
<head>
    <title>ImpressPages</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Core/assets/admin/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/login.css'); ?>">
    <link rel="shortcut icon" href="<?php echo ipFileUrl('favicon.ico'); ?>">
</head>
<body>

<a href="http://www.impresspages.org/" target="_blank" class="logo"><img src="<?php echo ipFileUrl('Ip/Internal/Admin/assets/img/logo.png'); ?>"></a>
<div class="verticalAlign"></div>
<div class="login">
    <?php echo $content; ?>
</div>
<div class="loginFooter">Copyright 2009-<?php echo date("Y"); ?> by <a href="http://www.impresspages.org/" target="_blank">ImpressPages, UAB</a></div>

<?php echo ipJs(); ?>
</body>
</html>
