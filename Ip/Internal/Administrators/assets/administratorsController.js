var ipAdministratorsController = null;

(function ($) {
    "use strict";

    var app = angular.module('Administrators', []);

    app.run(function ($rootScope) {
        $rootScope.$on('$locationChangeSuccess', function (e, newUrl, oldUrl) {
            $rootScope.$broadcast('PathChanged', newUrl);
        });
    });

    ipAdministratorsController = function ($scope, $location) {
        //init
        $scope.administrators = ipAdministrators;
        $scope.activeAdministrator = null;
        $scope.editMode = false;
        $scope.ipAdministratorsAdminId = ipAdministratorsAdminId;
        $scope.availablePermissions = ipAvailablePermissions;

        $scope.activeAdministratorEmail = 'null'; //to avoid chrome Autocomplete.

        $scope.$on('PathChanged', function (event, path) {
            var administratorId = getHashParams().administrator;
            for (var i = 0; i < ipAdministrators.length; i++) {
                if (administratorId == ipAdministrators[i]['id']) {
                    $scope.activateAdministrator(ipAdministrators[i]);
                }
            }
        });

        $scope.activateAdministrator = function (administrator) {
            $scope.activeAdministrator = administrator;
            $scope.activeAdministratorEmail = administrator.email; //to avoid chrome Autocomplete.
        };

        $scope.addModal = function () {
            var $modal = $('.ipsAddModal');
            $modal.find('input[name=email]').val('');
            $modal.find('input[name=username]').val('');
            $modal.find('input[name=password]').val('');
            $modal.modal();
            //$('.ipsAddModal').find("input[name=username]").hide();
            $('.ipsAddModal form').off('ipSubmitResponse').on('ipSubmitResponse', function (e, response) {
                if (response && response.status == 'ok') {
                    $scope.administrators.push({
                        username: $modal.find('input[name=username]').val(),
                        email: $modal.find('input[name=email]').val(),
                        permissions: response.permissions,
                        id: response.id
                    });
                    $scope.$apply();
                    $modal.modal('hide');
                }
            });
            setTimeout(function () {
                $('.ipsAddModal input[name=username]').focus();
            }, 500);
            $modal.find('.ipsAdd').off('click').on('click', function () {
                $('.ipsAddModal form').submit();
            });
        };

        $scope.setEditMode = function (mode) {
            $scope.editMode = mode;
        };

        $scope.updateModal = function () {
            var $modal = $('.ipsUpdateModal')
            $modal.modal();
            var $form = $('.ipsUpdateModal form');
            $form.find('input[name=id]').val($scope.activeAdministrator.id);
            $form.off('ipSubmitResponse').on('ipSubmitResponse', function (e, response) {
                if (response && response.status == 'ok') {
                    $scope.activeAdministrator.username = $form.find('input[name=username]').val();
                    $scope.activeAdministrator.email = $form.find('input[name=email]').val();
                    $scope.$apply();
                    $modal.modal('hide');
                }
            });
            $modal.find('.ipsSave').off('click').on('click', function () {
                $('.ipsUpdateModal form').submit();
            });
            setTimeout(function () {
                $('.ipsUpdateModal input[name=username]').focus();
            }, 500);
        };

        $scope.setPermission = function (permission, value, callback) {
            if ($scope.activeAdministrator.id == ipAdministratorId && permission == 'Super admin' && !value && !$scope.activeAdministrator['permissions']['Administrators']) {
                alert(ipAdministratorsSuperAdminWarning);
                return;
            }

            var data = {
                aa: 'Administrators.setAdminPermission',
                securityToken: ip.securityToken,
                permission: permission,
                value: value ? 1 : 0,
                adminId: $scope.activeAdministrator.id
            };
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    $scope.activeAdministrator.permissions[permission] = value;
                    $scope.$apply();
                    if (callback) {
                        callback();
                    }
                },
                error: function (response) {
                    alert('Error: ' + response.responseText);
                },
                dataType: 'json'
            });

        };

        $scope.deleteModal = function () {
            $('.ipsDeleteModal').modal();
            $('.ipsDeleteModal .ipsDelete').off('click').on('click', function (e) {
                e.preventDefault();
                deleteAdministrator($scope.activeAdministrator.id, function () {
                    $('.ipsDeleteModal').modal('hide');

                    var index = $scope.administrators.indexOf($scope.activeAdministrator);
                    $scope.activeAdministrator = null;
                    if (index > -1) {
                        $scope.administrators.splice(index, 1);
                    }
                    $scope.$apply();
                });
            });
        };


        var deleteAdministrator = function (id, successCallback) {
            var data = {
                aa: 'Administrators.delete',
                id: id,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: successCallback,
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };


        var updateHash = function (administrator) {
            var path = 'hash&administrator=' + administrator.id;
            $location.path(path);
        };

        var getHashParams = function () {

            var hashParams = {};
            var e,
                a = /\+/g,  // Regex for replacing addition symbol with a space
                r = /([^&;=]+)=?([^&;]*)/g,
                d = function (s) {
                    return decodeURIComponent(s.replace(a, " "));
                },
                q = window.location.hash.substring(1);

            while (e = r.exec(q))
                hashParams[d(e[1])] = d(e[2]);

            return hashParams;
        }


    }


})(jQuery);


