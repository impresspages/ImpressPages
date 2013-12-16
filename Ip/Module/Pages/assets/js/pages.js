function ipPages($scope) {
    //languages
    $scope.activeLanguage = languageList[0];
    $scope.activeZone = zoneList[0];

    $scope.languages = languageList;

    $scope.activateLanguage = function(language) {
        $scope.activeLanguage = language;
    }


    //zones
    $scope.zones = zoneList;

    $scope.activateZone = function(zone) {
        $scope.activeZone = zone;
        $('#pages_' + $scope.activeLanguage.id + '_' + zone.name).ipPageTree({languageId: $scope.activeLanguage.id, zoneName: zone.name});

    }


}



