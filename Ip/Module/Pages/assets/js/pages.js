function ipPages($scope) {
    //languages
    $scope.activeLanguage = languageList[0];
    $scope.activeZone = zoneList[0];
    $scope.copyPageId = false;
    $scope.cutPageId = false;
    $scope.selectedPageId = false;
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

    $scope.addPageModal = function() {
        var $modal = $('.ipsAddModal');
        $modal.modal();


        $modal.find('.ipsAdd').off('click').on('click', function(){$modal.find('form').submit()});
        $modal.find('form').off('submit').on('submit', function(e) {
            e.preventDefault();
            var title = $modal.find('input[name=title]').val();
            var visible = $modal.find('input[name=visible]').is(':checked') ? 1 : 0;
            addPage(title, visible);
            $modal.modal('hide');
        });
    }

    $scope.cutPage = function() {
        $scope.copyPageId = false;
        $scope.cutPageId = $scope.selectedPageId
    }

    $scope.copyPage = function () {
        $scope.cutPageId = false;
        $scope.copyPageId = $scope.selectedPageId;
    }

    $scope.pastePage = function () {
        var tree = getJsTree();
        var position = tree._get_children(-1).length; //last position
        var node = tree.get_selected();
        if (node.length) {
            var position = node.index() + 1;
        }
        if ($scope.cutPageId) {
            movePage($scope.cutPageId, $scope.activeLanguage.id, $scope.activeZone.name, $scope.selectedPageId, position);
        } else {
            copyPage($scope.selectedPageId, $scope.activeLanguage.id, $scope.activeZone.name, $scope.selectedPageId, position);
        }
        refresh();
    }

    var initTree = function () {
        $scope.selectedPageId = false;
        getTreeDiv().ipPageTree({languageId: $scope.activeLanguage.id, zoneName: $scope.activeZone.name});
        getTreeDiv().off('select_node.jstree').on('select_node.jstree', function(e) {
            var tree = getJsTree();
            var node = tree.get_selected();
            $scope.selectedPageId = node.attr('pageId');
            console.log($scope.selectedPageId);
            $scope.$apply();
        });

    }

    var getTreeDiv = function () {
        return $('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeZone.name).find('.ipsTree');
    }

    var getJsTree = function () {
        return jQuery.jstree._reference('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeZone.name + ' .ipsTree');
    }

    var refresh = function () {
        $('.tree .ipsTree').ipPageTree('refresh');
    }

    var addPage = function (title, visible) {
        var data = {
            aa: 'Pages.addPage',
            securityToken: ip.securityToken,
            title: title,
            visible: visible,
            zoneName: $scope.activeZone.name,
            languageId: $scope.activeLanguage.id
        };

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                refresh();
            },
            error: function(response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            },
            dataType: 'json'
        });

    }

    var deletePage = function (pageId) {
        var data = {
            aa: 'Pages.deletePage',
            securityToken: ip.securityToken
        };

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                refresh();
            },
            error: function(response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            },
            dataType: 'json'
        });
    }


    var copyPage = function(pageId, destinationLanguageId, destinationZoneName, destinationParentId, position) {
        var data = {
            aa: 'Pages.copyPage',
            destinationParentId: destinationParentId,
            languageId: destinationLanguageId,
            zoneName: destinationZoneName,
            securityToken: ip.securityToken
        };

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                refresh();
            },
            error: function(response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            },
            dataType: 'json'
        });
    }

    var movePage = function(pageId, destinationLanguageId, destinationZoneName, destinationParentId, destinationPosition) {
        var data = {
            aa: 'Pages.movePage',
            pageId: pageId,
            destinationPosition: destinationPosition,
            destinationParentId: destinationParentId,
            languageId: destinationLanguageId,
            zoneName: destinationZoneName,
            securityToken: ip.securityToken
        };

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                console.log('refresh');
                refresh();
            },
            error: function(response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            },
            dataType: 'json'
        });
    }



}



$( document ).ready(function() {
    $('.zoneList li:first a').click();
});