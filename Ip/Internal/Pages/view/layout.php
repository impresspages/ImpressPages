<div class="ip">
    <div class="ipAdminPages" ng-app="Pages" ng-controller="ipPages" ng-cloak>
        <div class="_container _languages">
            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-plus"></i></a>
            <ul>
                <li ng-repeat="language in languages">
                    <a href="#" ng-click="setLanguageHash(language)">{{language.abbreviation}}</a>
                </li>
                <li>
                    <a href="<?php echo $languagesUrl; ?>"><i class="fa fa-cog"></i></a>
                </li>
            </ul>
        </div>
        <div class="_container _zones">
            <button ng-click="addZoneModal()" class="btn btn-sm btn-default" role="button" >
                <i class="fa fa-plus"></i>
                <?php _e('Add', 'ipAdmin'); ?>
            </button>
            <ul>
                <li ng-repeat="zone in zones" zones-post-repeat-directive data-zonename="{{zone.name}}">
                    <a href="#" ng-click="setZoneHash(zone)">{{zoneTitle(zone)}}</a>
                    <button class="btn btn-sm btn-default _control" ng-click="updateZoneModal(zone)"><i class="fa fa-cog"></i></button>
                </li>
            </ul>
        </div>
        <div class="_container _pages ipsPages">
            <div ng-repeat="language in languages" class="language" ng-show="language.id == activeLanguage.id">
                <div ng-repeat="zone in zones" class="tree" ng-show="zone.name == activeZone.name">
                    <div id="pages_{{language.id}}_{{zone.name}}">
                        <button class="btn btn-sm btn-default ipsAddPage" ng-click="addPageModal()" role="button">
                            <i class="fa fa-plus"></i>
                            <?php _e('Add', 'ipAdmin'); ?>
                        </button>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-default" title="<?php _e('Cut', 'ipAdmin'); ?>" ng-click="cutPage()" ng-show="selectedPageId" role="button">
                                <i class="fa fa-cut"></i>
                            </button>
                            <button class="btn btn-sm btn-default" title="<?php _e('Copy', 'ipAdmin'); ?>" ng-click="copyPage()" ng-show="selectedPageId" role="button">
                                <i class="fa fa-copy"></i>
                            </button>
                            <button class="btn btn-sm btn-default" title="<?php _e('Paste', 'ipAdmin'); ?>" ng-click="pastePage()" ng-show="copyPageId || cutPageId" role="button">
                                <i class="fa fa-paste"></i>
                            </button>
                        </div>
                        <div class="ipsTree"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="_container _properties ipsProperties" ng-show="selectedPageId"></div>
        <?php echo ipView('Ip/Internal/Pages/view/addPageModal.php', $this->getVariables())->render(); ?>
        <?php echo ipView('Ip/Internal/Pages/view/addZoneModal.php', $this->getVariables())->render(); ?>
        <?php echo ipView('Ip/Internal/Pages/view/updateZoneModal.php', $this->getVariables())->render(); ?>
    </div>
</div>
