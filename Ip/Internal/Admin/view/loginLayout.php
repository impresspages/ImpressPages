<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/backend/login/login.css') ?>">
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.css') ?>">
    <link rel="shortcut icon" href="<?php echo ipFileUrl('favicon.ico') ?>">
</head>
<body>


<a href="http://www.impresspages.org/" class="logo" target="_blank"><img src="<?php echo ipFileUrl('Ip/Internal/Admin/assets/backend/login/logo.png') ?>"></a>
<div class="verticalAlign"></div>
<div class="login">
    <?php echo $content ?>
</div>
<div class="loginFooter">Copyright 2009-<?php echo date("Y") ?> by <a href="http://www.impresspages.org/">ImpressPages UAB</a></div>

<?php echo ipJs() ?>
</body>
</html>
