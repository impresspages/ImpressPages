<div class="ip">
    <div class="ipAdminPages" ng-app="Pages" ng-controller="ipPages" ng-cloak>
        <div class="_container _languages">
<!--            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-plus"></i></a>-->
            <ul>
                <li ng-repeat="language in languageList">
                    <a href="#" ng-click="setLanguageHash(language)">{{language.abbreviation}}</a>
                </li>
                <li>
                    <a href="<?php echo $languagesUrl; ?>"><i class="fa fa-cog"></i></a>
                </li>
            </ul>
        </div>
        <div class="_container _zones">
            <button ng-click="addMenuModal()" class="btn btn-sm btn-default" role="button" >
                <i class="fa fa-plus"></i>
                <?php _e('Add', 'ipAdmin'); ?>
            </button>
            <ul>
                <li ng-repeat="menu in menuList" menulist-post-repeat-directive data-menuname="{{menu.alias}}">
                    <a href="" ng-click="setMenuHash(menu)">{{menuTitle(menu)}}</a>
                    <button class="btn btn-sm btn-default _control" ng-click="updateMenuModal(menu)"><i class="fa fa-cog"></i></button>
                </li>
            </ul>
        </div>
        <div class="_container _pages ipsPages">
            <div ng-repeat="language in languageList" class="language" ng-show="language.code == activeLanguage.code">
                <div ng-repeat="menu in menuList" class="tree" ng-show="menu.id == activeMenu.id">
                    <div id="pages_{{language.id}}_{{menu.alias}}">
                        <button class="btn btn-sm btn-default ipsAddPage" ng-click="addPageModal()" role="button">
                            <i class="fa fa-plus"></i>
                            <?php _e('Add', 'ipAdmin'); ?>
                        </button>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-default" title="<?php _e('Cut', 'ipAdmin'); ?>" ng-click="cutPage()" ng-class="{disabled: !selectedPageId}" role="button">
                                <i class="fa fa-cut"></i>
                            </button>
                            <button class="btn btn-sm btn-default" title="<?php _e('Copy', 'ipAdmin'); ?>" ng-click="copyPage()" ng-class="{disabled: !selectedPageId}" role="button">
                                <i class="fa fa-copy"></i>
                            </button>
                            <button class="btn btn-sm btn-default" title="<?php _e('Paste', 'ipAdmin'); ?>" ng-click="pastePage()" ng-class="{disabled: !copyPageId && !cutPageId}" role="button">
                                <i class="fa fa-paste"></i>
                            </button>
                        </div>
                    <div class="ipsTree"></div>
                </div>
            </div>
        </div>
    <div class="_container _properties ipsProperties" ng-show="selectedPageId"></div>
    <?php echo ipView('Ip/Internal/Pages/view/addPageModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/addMenuModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/updateMenuModal.php', $this->getVariables())->render(); ?>
</div>
