<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <title><?php echo __('ImpressPages CMS installation wizard', 'ipInstall') ?></title>
    <link rel="stylesheet" href="<?php echo \Ip\Config::coreModuleUrl('Install/assets/style.css') ?>">
    <link rel="shortcut icon" href="<?php echo \Ip\Config::baseUrl('favicon.ico') ?>">
</head>
<body>

<div class="container">
    <img id="logo" src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/img/cms_logo.png') ?>" alt="ImpressPages CMS">
    <div class="clear"></div>
    <div id="wrapper">
        <p id="installationNotice"><?php echo __('ImpressPages CMS installation wizard', 'ipInstall') ?> <span><?php printf(__('Version %s', 'ipInstall'), IP_VERSION) ?></span></p>
        <div class="clear"></div>
        <img class="border" src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/img/cms_main_top.gif') ?>" alt="Design">
        <div id="main">
            <div id="menu">
                <?php echo \Ip\Module\Install\Helper::gen_menu() ?>
            </div>
            <div id="content">
                <?php echo $content ?>
            </div>
            <div id="loading">
                <img src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/img/loading.gif') ?>" >
            </div>
            <div class="clear"></div>
        </div>
        <img class="border" src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/img/cms_main_bottom.gif') ?>" alt="Design">
        <div class="clear"></div>
    </div>
    <div class="footer">Copyright 2009-<?php echo date("Y") ?> by <a href="http://www.impresspages.org">ImpressPages UAB</a></div>
</div>

<script type="text/javascript" src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/js/jquery.js') ?>"></script>
<script type="text/javascript" src="<?php echo \Ip\Config::coreModuleUrl('Install/assets/js/init.js') ?>"></script>
<?php if (!empty($requiredJs)) { ?>
    <?php foreach($requiredJs as $jsFile) { ?>
        <script type="text/javascript" src="<?php echo $jsFile ?>"></script>
    <?php } ?>
<?php } ?>

</body>