<div class="ip" ng-app ng-controller="ipPages" ng-cloak>
    <div class="languageList navBlock">
        <ul>
            <li ng-repeat="language in languages">
                <a href="#" ng-click="activateLanguage(language)">{{language.abbreviation}}</a>
            </li>
        </ul>
    </div>
    <div class="zoneList navBlock">
        <ul>
            <li ng-repeat="zone in zones">
                <a class="title" href="#" ng-click="activateZone(zone)">{{zone.title}}</a>
            </li>
        </ul>
    </div>
    <div class="ipsPages pages navBlock">
        <div ng-repeat="language in languages" class="language" ng-show="language.id == activeLanguage.id">
            <div ng-repeat="zone in zones" class="tree" ng-show="zone.name == activeZone.name">
                <div id="pages_{{language.id}}_{{zone.name}}">
                    <ul class="actions">
                        <button class="ipsAdd btn btn-default" role="button" >
                            <i class="fa fa-file-o"></i>
                            <?php _e('Add', 'ipAdmin') ?>
                        </button>
                        <button class="ipsCut btn btn-default" role="button" >
                            <i class="fa fa-cut"></i>
                            <?php _e('Cut', 'ipAdmin') ?>
                        </button>
                        <button class="ipsCopy btn btn-default" role="button" >
                            <i class="fa fa-copy"></i>
                            <?php _e('Copy', 'ipAdmin') ?>
                        </button>
                        <button class="ipsPaste btn btn-default hide" role="button" >
                            <i class="fa fa-paste"></i>
                            <?php _e('Paste', 'ipAdmin') ?>
                        </button>
                    </ul>
                    <div class="ipsTree"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="ipsProperties properties navBlock">

    </div>
    <?php echo \Ip\View::create('addPageModal.php', $this->getVariables())->render() ?>
</div>