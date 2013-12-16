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
            <div id="pages_{{language.id}}_{{zone.name}}" ng-repeat="zone in zones" class="tree" ng-show="zone.name == activeZone.name">
                {{language.title}} /  {{zone.title}}
            </div>
        </div>
    </div>
    <div class="properties navBlock">
          PROPERTIES
    </div>

</div>