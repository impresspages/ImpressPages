<div class="ipModulePlugins">
    <?php foreach ($plugins as $plugin){ ?>
        <div class="panel panel-default" data-pluginname="<?php echo esc($plugin['name']) ?>">
            <div class="panel-heading"><?php echo esc($plugin['title']) ?></div>
            <div class="panel-body">
                <p>
                <?php echo esc($plugin['description']) ?>
                </p>
                <?php if ($plugin['active']) { ?>
                    <button type="button" class="ipsDeactivate btn btn-default navbar-btn"><?php _e('Deactivate', 'Ip-admin') ?></button>
                <?php } else { ?>
                    <button type="button" class="ipsActivate btn btn-default navbar-btn"><?php _e('Activate', 'Ip-admin') ?></button>
                    <button type="button" class="ipsRemove btn btn-default navbar-btn"><?php _e('Remove', 'Ip-admin') ?></button>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
