<div class="ip" ng-app ng-controller="ipPages">
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
        <div ng-repeat="language in languages" class="language" ng-show="language.active">
            <div ng-repeat="zone in zones" class="tree" ng-show="zone.active">
                {{language.title}} /  {{zone.title}}
            </div>
        </div>
    </div>
</div>