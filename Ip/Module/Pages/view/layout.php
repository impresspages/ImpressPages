<div class="ip" ng-app>
    <div ng-controller="LanguageList" class="languageList navBlock">
        <ul>
            <li ng-repeat="language in languages">
                <a href="#">{{language.abbreviation}}</a>
            </li>
        </ul>
    </div>
    <div ng-controller="ZoneList">
        <div class="zoneList navBlock">
            <ul>
                <li ng-repeat="zone in zones">
                    <a class="title" href="#" ng-click="activate(zone)">{{zone.title}}</a>
                </li>
            </ul>
        </div>
        <div class="ipsTree pageTree navBlock">
            <div ng-repeat="zone in zones" class="tree" ng-show="zone.active">
                tree {{zone.title}}
            </div>
        </div>
    </div>
</div>