<div class="ipModuleSystem">
    <div class="page-header">
        <h1>ImpressPages <small><?php echo esc($version); ?></small></h1>
    </div>
    <?php if (!empty($notes)) { ?>
        <?php foreach ($notes as $note) { ?>
            <p class="bg-success">
                <?php echo $note; ?>
            </p>
        <?php } ?>
    <?php } ?>

    <?php if ($changedUrl) { ?>
    <h2><?php _e('Website\'s URL seems to be changed', 'ipAdmin'); ?></h2>
    <p><?php echo sprintf(__(
            'We have detected that website\'s URL has changed. Would you like to update links on your website from %s to %s ?',
            'ipAdmin'
        ), '<b>' . $oldUrl . '</b>', '<b>' . $newUrl . '</b>') ?></p>
    <a href="<?php echo ipActionUrl(array('aa' => 'System.updateLinks')) ?>" class="ipsUpdateLinks btn btn-primary"><?php _e('Update links', 'ipAdmin'); ?></a>
    <?php } ?>
    <?php if ($migrationsAvailable) { ?>
        <h2><?php _e('Database migrations', 'ipAdmin'); ?></h2>
        <p><?php _e('Your database is outdated.', 'ipAdmin') ?></p>
        <a class="btn btn-primary ipsStartMigration" href="<?php echo $migrationsUrl ?>"><?php _e('Update', 'ipAdmin') ?></a>
    <?php } ?>
    <div class="hidden ipsSystemStatus">
        <h2><?php _e('System status', 'ipAdmin'); ?></h2>
    </div>

    <div id="ipWidgetUpdatePopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Update progress', 'ipAdmin'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php _e('Downloading new files and migrating the database. Please don\'t refresh the page. Be patient', 'ipAdmin') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
