<div class="ip" ng-app="Pages" ng-controller="ipPages" ng-cloak>
    <div class="languageList navBlock">
        <ul>
            <li ng-repeat="language in languages">
                <a ng-click="setLanguageHash(language)">{{language.abbreviation}}</a>
            </li>
        </ul>
        <a href="<?php echo $languagesUrl ?>">*</a>
    </div>
    <div class="zoneList navBlock">
        <ul>
            <li ng-repeat="zone in zones" zones-post-repeat-directive>
                <a class="title" ng-click="setZoneHash(zone)">{{zoneTitle(zone)}}</a>  <a ng-click="updateZoneModal(zone)">*</a>
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
        <div ng-repeat="language in languages" class="language" ng-show="language.id == activeLanguage.id">
            <div ng-repeat="zone in zones" class="tree" ng-show="zone.name == activeZone.name">
                <div id="pages_{{language.id}}_{{zone.name}}">
                    <ul class="actions">
                        <button ng-click="addPageModal()" class="btn btn-default" role="button" >
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
    <?php echo $this->subview('addPageModal.php')->render() ?>
    <?php echo $this->subview('addZoneModal.php')->render() ?>
    <?php echo $this->subview('updateZoneModal.php')->render() ?>

</div>