function ZoneList($scope) {
    $scope.zones = zoneList;

    $scope.activate = function(zone) {
        $.each($scope.zones, function (key, value) {
            value.active = false;
        })
        zone.active = 1;
    }
}