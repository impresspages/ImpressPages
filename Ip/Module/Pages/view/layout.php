<div class="ip" ng-app>
    <div ng-controller="LanguageList" class="languageList">
        <ul>
            <li ng-repeat="language in languages">
                <a href="#">{{language.abbreviation}}</a>
            </li>
        </ul>
    </div>
</div>