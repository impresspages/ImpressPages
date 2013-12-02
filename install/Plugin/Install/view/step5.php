<h1><?php _e('ImpressPages CMS successfully installed.', 'ipInstall') ?></h1>
<?php _e('FINISH_MESSAGE', 'ipInstall') ?>

<?php
/*
 * autocron is required in case first visit is to administration panel.
 * In this case cron tries to delete old revisions in parallel to administration panel load.
 * Some issues are avoided while executing cron before going to administration panel.
 */
?>

<img width="1px" height="1px" style="border: none;" src="<?php echo ipActionUrl(array('pa' => 'Cron', 'pass' => ipGetOption('Config.cronPassword'))) ?>" alt=""/>