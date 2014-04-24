<div class="ipModulePlugins ipsModulePlugins" ng-app="Plugins" ng-controller="ipPlugins">
    <div class="_outer ipsModulePluginsContainer">
        <div class="_container _plugins ipsPlugins">
            <div class="_actions">
                <a href="#" class="btn btn-new"><i class="fa fa-plus"></i> <?php _e('Add', 'Ip-admin'); ?></a>
            </div>
            <ul>
            <?php foreach ($plugins as $plugin) { ?>
                <li>
                    <a href="#hash=&plugin=<?php echo esc($plugin['name']); ?>" class="_plugin ipsPlugin<?php if (!$plugin['active']) { echo ' disabled'; }?>" ng-class="{active: '<?php echo esc($plugin['name']); ?>' == selectedPluginName}">
                        <span class="_heading">
                            <i class="fa fa-cog"></i>
                            <span class="_name"><?php echo esc($plugin['title']); ?></span>
                            <span class="label label-<?php echo esc($plugin['labelType']); ?>"><?php echo esc($plugin['label']); ?></span>
                        </span>
                        <p class="_description"><?php echo esc($plugin['description']); ?></p>
                    </a>
                </li>
            <?php } ?>
            </ul>
        </div>
        <div class="_container _properties ipsProperties"></div>
    </div>
</div>
