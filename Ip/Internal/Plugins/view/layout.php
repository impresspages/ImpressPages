<div class="ipModulePlugins ipsModulePlugins" ng-app="Plugins" ng-controller="ipPlugins">
    <div class="_outer ipsModulePluginsContainer">
        <div class="_container _plugins ipsPlugins" ng-cloak>
            <div class="_actions">
                <a href="<?php echo ipActionUrl(array('aa' => 'Plugins.market')); ?>" class="btn btn-new"><i class="fa fa-plus"></i> <?php _e('Add', 'Ip-admin'); ?></a>
            </div>
            <ul>
                <li ng-repeat="plugin in pluginList">
                    <a href="" ng-click="setPluginHash(plugin)" class="_plugin ipsPlugin" ng-class="{active: plugin == selectedPlugin, disabled: !plugin.active}">
                        <span class="_heading">
                            <i class="fa fa-cog"></i>
                            <span class="_name">{{plugin.title}}</span>
                            <span class="label label-{{plugin.labelType}}">{{plugin.label}}</span>
                        </span>
                        <p class="_description">{{plugin.description}}</p>
                    </a>
                </li>
            </ul>
        </div>
        <div class="_container _properties ipsProperties"></div>
    </div>
</div>
