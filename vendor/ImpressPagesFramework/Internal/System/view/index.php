<div class="ipModuleSystem" ng-app="System" ng-controller="ipTrash">
    <div class="page-header">
        <h1>ImpressPages <small><?php echo esc($version); ?></small></h1>
    </div>

    <?php if (!empty($notes)) { ?>
        <?php foreach ($notes as $note) { ?>
        <p class="alert alert-success">
            <?php echo $note; ?>
        </p>
        <?php } ?>
    <?php } ?>

    <?php if ($changedUrl) { ?>
        <h2><?php _e('Website\'s URL seems to be changed', 'Ip-admin'); ?></h2>
        <p><?php echo sprintf(__(
            'We have detected that website\'s URL has changed. Would you like to update links on your website from %s to %s ?',
            'Ip-admin'
        ), '<b>' . $oldUrl . '</b>', '<b>' . $newUrl . '</b>') ?></p>
        <a href="<?php echo ipActionUrl(array('aa' => 'System.updateLinks')) ?>" class="ipsUpdateLinks btn btn-primary"><?php _e('Update links', 'Ip-admin'); ?></a>
    <?php } ?>

    <?php if ($migrationsAvailable) { ?>
        <h2><?php _e('Database migrations', 'Ip-admin'); ?></h2>
        <p><?php _e('Your database is outdated.', 'Ip-admin') ?></p>
        <a class="btn btn-primary ipsStartMigration" href="<?php echo $migrationsUrl ?>"><?php _e('Update', 'Ip-admin') ?></a>
    <?php } ?>

    <?php if ($trash['size'] > 0) { ?>
        <h2><?php _e('Trash', 'Ip-admin'); ?></h2>
        <p><?php printf(__('Trash contains %s deleted pages.', 'Ip-admin'), $trash['size']) ?></p>
        <button ng-click="recoveryPageModal()" class="btn btn-default" role="button">
            <i class="fa fa-plus"></i>
            <?php _e('Recovery', 'Ip-admin'); ?>
        </button>
        <button ng-click="emptyPageModal()" class="btn btn-danger ipsDelete" role="button">
            <i class="fa fa-fw fa-trash-o"></i>
            <?php _e('Empty', 'Ip-admin'); ?>
        </button>
    <?php } ?>


    <h2><?php _e('Cache', 'Ip-admin'); ?></h2>
    <p><?php _e('If you change CSS or JS file on your website, users may still have the old version cached on their browsers. "Clear cache" will increase the number added at the end of all CSS / JS links invalidating the cache for all your visitors.', 'Ip-admin'); ?></p>
    <a class="btn btn-default ipsClearCache" href="#">Clear cache</a>


    <div class="hidden ipsSystemStatus">
        <h2><?php _e('System status', 'Ip-admin'); ?></h2>
    </div>

    <div id="ipWidgetUpdatePopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Update in progress', 'Ip-admin'); ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php _e('Downloading new files and migrating the database. Please don\'t refresh the page. Be patient. It may take up to 2 min. on a good line.', 'Ip-admin') ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php echo ipView('Ip/Internal/System/view/recoveryModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/System/view/emptyModal.php', $this->getVariables())->render(); ?>
</div>
