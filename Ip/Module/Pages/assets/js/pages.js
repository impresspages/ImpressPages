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
test($scope);
        if (!zone.initialized) {
//            if (!zone[$scope.activeLanguage.id].initializded) {
//                zone[$scope.activeLanguage.id].initializded = true;
//            }
        }
    }

    $scope.test = function() {

    }

    //var initPages =
}



var storedScope;

function test($scope)
{
    storedScope = $scope;
}

$('body').on('click', '.ipmItemCurrent', function(e) {
    e.preventDefault();
    storedScope.activeZone = zoneList[0];
//    storedScope.activateZone(zoneList[0]);
    storedScope.$apply();
});