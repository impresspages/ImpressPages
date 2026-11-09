<div class="ipAdminPages ipsAdminPages" ng-app="Pages" ng-controller="ipPages">
    <div class="_outer ipsAdminPagesContainer">
        <div class="_container _menus ipsMenus" ng-cloak>
            <h2 class="_columnTitle"><?php _e('Pages', 'Ip-admin'); ?></h2>
            <div class="_languageSwitch ipsLanguages" role="group" aria-label="<?php _e('Language', 'Ip-admin'); ?>" ng-show="languageList.length > 1">
                <button type="button" ng-repeat="language in languageList" ng-class="{active: language == activeLanguage}" aria-pressed="{{language == activeLanguage}}" ng-click="setLanguageHash(language)">{{language.abbreviation}}</button>
            </div>
            <ul class="ipsMenuList">
                <li ng-repeat="menu in menuList" menulist-post-repeat-directive data-menuname="{{menu.alias}}" data-menuid="{{menu.id}}" ng-class="{active: menu == activeMenu}">
                    <a href="" ng-show="activeLanguage.code == menu.languageCode" ng-click="setMenuHash(menu)">{{menuTitle(menu)}}</a>
                    <button ng-show="{{allowActions}}" class="btn btn-default _control" ng-click="updateMenuModal(menu)" title="<?php _e('Menu settings', 'Ip-admin'); ?>" aria-label="<?php _e('Menu settings', 'Ip-admin'); ?>"><i class="fa fa-cog" aria-hidden="true"></i></button>
                </li>
            </ul>
            <div class="_actions _foot" ng-show="{{allowActions}}">
                <button ng-click="addMenuModal()" class="btn btn-default btn-block" role="button">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    <?php _e('Add menu', 'Ip-admin'); ?>
                </button>
            </div>
        </div>
        <div class="_container _pagesListing ipsPagesContainer" ng-cloak>
            <div ng-repeat="menu in menuList" class="tree" ng-show="menu.id == activeMenu.id">
                <div id="pages_{{menu.languageCode}}_{{menu.alias}}">
                    <h2 class="_columnTitle">{{menuTitle(menu)}}</h2>
                    <div class="_actions" ng-show="{{allowActions}}">
                        <button class="btn btn-new ipsAddPage" ng-click="addPageModal()" role="button">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <?php _e('Add page', 'Ip-admin'); ?>
                        </button>
                        <div class="btn-group">
                            <button class="btn btn-default" title="<?php _e('Cut', 'Ip-admin'); ?>" ng-click="cutPage()" ng-class="{disabled: !selectedPageId}" role="button">
                                <i class="fa fa-cut"></i>
                            </button>
                            <button class="btn btn-default" title="<?php _e('Copy', 'Ip-admin'); ?>" ng-click="copyPage()" ng-class="{disabled: !selectedPageId}" role="button">
                                <i class="fa fa-copy"></i>
                            </button>
                            <button class="btn btn-default" title="<?php _e('Paste', 'Ip-admin'); ?>" ng-click="pastePage()" ng-class="{disabled: !copyPageId && !cutPageId}" role="button">
                                <i class="fa fa-paste"></i>
                            </button>
                        </div>
                    </div>
                    <div class="_tree ipsPages"></div>
                </div>
            </div>
        </div>
        <div class="_container _properties ipsProperties" ng-show="selectedPageId && allowActions"></div>
    </div>
    <?php echo ipView('Ip/Internal/Pages/view/addPageModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/addMenuModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/updateMenuModal.php', $this->getVariables())->render(); ?>
</div>
