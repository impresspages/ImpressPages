<h1><?php _e('Finished!', 'Install'); ?></h1>

<?php if ($showInfo) { ?>
    <p class="alert alert-info"><?php _e('If you wish to repeat the installation, please clear the configuration file "config.php".', 'Install'); ?></p>
<?php } ?>

<p>
    <?php _e('ImpressPages has been successfully installed.', 'Install'); ?>
</p>
<p>
    <a href="../"><?php _e('Front page', 'Install'); ?></a>
</p>
<p>
    <a href="../admin.php"><?php _e('Administration page', 'Install'); ?></a>
</p>
