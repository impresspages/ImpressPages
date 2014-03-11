<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php _e('ImpressPages CMS installation wizard', 'Install') ?></title>
    <link rel="shortcut icon" href="<?php echo ipFileUrl('install/favicon.ico') ?>">
    <?php ipAddCss('assets/theme.css'); ?>
    <?php echo ipHead(); ?>
</head>
<body>

<div class="ip">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <img src="<?php echo ipThemeUrl('assets/impresspages_logo.png') ?>" alt="ImpressPages CMS">
                    <h3><?php _e('ImpressPages CMS installation wizard', 'Install') ?> <small><?php esc(printf(__('Version %s', 'Install', false), \Ip\Application::getVersion())) ?></small></h3>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?php echo \Plugin\Install\Helper::generateMenu(); ?>
                    </div>
                    <div class="col-md-9">
                        <?php echo ipBlock('main')->render(); ?>
                    </div>
                </div>
                <div class="page-footer">
                    This software is brought to you by <a target="_blank" href="http://www.impresspages.org/cms/about/team/">ImpressPages team</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($requiredJs)) { ?>
    <?php foreach($requiredJs as $jsFile) { ?>
        <script type="text/javascript" src="<?php echo $jsFile ?>"></script>
    <?php } ?>
<?php } ?>
    <?php echo ipJs() ?>

</body>
