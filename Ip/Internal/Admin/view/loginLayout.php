<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/img/apple-touch-icon.png'); ?>" />
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Core/assets/admin/admin.css'); ?>" />
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/login.css'); ?>" />
    <link rel="shortcut icon" href="<?php echo ipFileUrl('favicon.ico'); ?>" />
</head>
<body>

<a href="http://www.impresspages.org/" class="logo" target="_blank"><img src="<?php echo ipFileUrl('Ip/Internal/Admin/assets/img/logo.png'); ?>" width="332" height="59" alt="ImpressPages" /></a>
<div class="verticalAlign"></div>
<div class="login">
    <?php echo $content; ?>
</div>
<div class="loginFooter">Copyright 2009-<?php echo date("Y"); ?> by <a href="http://www.impresspages.org/" target="_blank">ImpressPages, UAB</a></div>

<?php echo ipJs(); ?>
</body>
</html>
