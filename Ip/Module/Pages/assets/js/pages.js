function ipPages($scope) {
    //languages
    $scope.languages = languageList;

    $scope.activateLanguage = function(language) {
        $.each($scope.languages, function (key, value) {
            value.active = false;
        })
        language.active = true;
    }


    //zones
    $scope.zones = zoneList;

    $scope.activateZone = function(zone) {
        $.each($scope.zones, function (key, value) {
            value.active = false;
        })
        zone.active = true;
    }
}