<!DOCTYPE html>
<html lang="<?php echo escAttr(ipContent()->getCurrentLanguage() ? ipContent()->getCurrentLanguage()->getCode() : 'en'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc(__('Log in', 'Ip-admin', false)); ?> · ImpressPages</title>
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Core/assets/admin/admin.css'); ?>">
    <link rel="stylesheet" href="<?php echo ipFileUrl('Ip/Internal/Admin/assets/login.css'); ?>">
    <link rel="shortcut icon" href="<?php echo ipFileUrl('favicon.ico'); ?>">
</head>
<body>


<a href="http://www.impresspages.org/" class="logo" target="_blank" rel="noopener"><img src="<?php echo ipFileUrl('Ip/Internal/Admin/assets/img/logo.png'); ?>" alt="ImpressPages"></a>
<div class="ip languageSelect">
    <?php echo $languageSelectForm->render(); ?>
</div>
<div class="verticalAlign"></div>
<main class="login">
    <?php echo $content; ?>
</main>
<div class="loginFooter">Copyright 2009-<?php echo date("Y"); ?> by <a href="http://www.impresspages.org/">ImpressPages, UAB</a></div>

<?php echo ipJs(); ?>
</body>
</html>
