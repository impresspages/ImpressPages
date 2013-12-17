function ipPages($scope) {
    //languages
    $scope.activeLanguage = languageList[0];
    $scope.activeZone = zoneList[0];

    $scope.languages = languageList;

    $scope.activateLanguage = function(language) {
        $scope.activeLanguage = language;
        initTree();
    }


    //zones
    $scope.zones = zoneList;

    $scope.activateZone = function(zone) {
        $scope.activeZone = zone;
        initTree();

    }
    var initTree = function () {
        var $zoneScope = $('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeZone.name);

        $zoneScope.find('.ipsTree').ipPageTree({languageId: $scope.activeLanguage.id, zoneName: $scope.activeZone.name});


        var $modal = $('.ipsAddModal');
        $zoneScope.find('.ipsAdd').off('click').on('click', function (){
            $modal.modal();
        });


        $modal.find('.ipsAdd').off('click').on('click', function() {
            var data = {
                aa: 'Pages.addPage',
                securityToken: ip.securityToken,
                title: $modal.find('input[name=title]').val(),
                visible: $modal.find('input[name=visible]').is(':checked') ? 1 : 0,
                zoneName: $scope.activeZone.name,
                languageId: $scope.activeLanguage.id
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: refresh,
                dataType: 'json'
            });

            $modal.modal('hide');
        });

    }

    var refresh = function () {
        var $zoneScope = $('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeZone.name);

        $zoneScope.find('.ipsTree').ipPageTree('refresh');
    }


}



