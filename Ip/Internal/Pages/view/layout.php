<div class="ip" ng-app="Pages" ng-controller="ipPages" ng-cloak>
    <div class="languageList navBlock">
        <ul>
            <li ng-repeat="language in languages">
                <a ng-click="setLanguageHash(language)">{{language.abbreviation}}</a>
            </li>
        </ul>
        <a href="<?php echo $languagesUrl ?>">*</a>
    </div>
    <div class="menuList navBlock">
        <ul>
            <li ng-repeat="menu in menuList" menus-post-repeat-directive data-menuname="{{menu.menuName}}">
                <a class="title" ng-click="setMenuHash(menu)">{{menuTitle(menu)}}</a>  <a ng-click="updateZoneModal(menu)">*</a>
            </li>
        </ul>
        <ul class="actions">
            <button ng-click="addZoneModal()" class="btn btn-default" role="button" >
                <i class="fa fa-file-o"></i>
                <?php _e('Add', 'ipAdmin') ?>
            </button>
        </ul>
    </div>
    <div class="ipsPages pages navBlock">
        <div ng-repeat="language in languageList" class="language" ng-show="language.id == activeLanguage.id">
            <div ng-repeat="menu in menuList" class="tree" ng-show="menu.alias == activeMenu.alias">
                <div id="pages_{{language.id}}_{{menu.alias}}">
                    <ul class="actions">
                        <button ng-click="addPageModal()" class="ipsAddPage btn btn-default" role="button" >
                            <i class="fa fa-file-o"></i>
                            <?php _e('Add', 'ipAdmin') ?>
                        </button>
                        <button ng-click="cutPage()" ng-show="selectedPageId" class="btn btn-default" role="button" >
                            <i class="fa fa-cut"></i>
                            <?php _e('Cut', 'ipAdmin') ?>
                        </button>
                        <button ng-click="copyPage()" ng-show="selectedPageId" class="btn btn-default" role="button" >
                            <i class="fa fa-copy"></i>
                            <?php _e('Copy', 'ipAdmin') ?>
                        </button>
                        <button ng-click="pastePage()" class="btn btn-default" ng-show="copyPageId || cutPageId" role="button" >
                            <i class="fa fa-paste"></i>
                            <?php _e('Paste', 'ipAdmin') ?>
                        </button>
                    </ul>
                    <div class="ipsTree"></div>
                </div>
            </div>
        </div>
    </div>
    <div ng-show="selectedPageId" class="ipsProperties properties navBlock">

    </div>
    <?php echo ipView('Ip/Internal/Pages/view/addPageModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/addZoneModal.php', $this->getVariables())->render(); ?>
    <?php echo ipView('Ip/Internal/Pages/view/updateZoneModal.php', $this->getVariables())->render(); ?>

</div>
