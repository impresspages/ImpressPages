var ipAdministratorsController = null;

(function ($) {
    "use strict";


    var app = angular.module('Administrators', []);

    app.run(function ($rootScope) {
        $rootScope.$on('$locationChangeSuccess', function (e, newUrl, oldUrl) {
            $rootScope.$broadcast('PathChanged', newUrl);
        });
    });

//    ipAdministratorsController = function ($scope, $location) {
//        //init
//        $scope.administrators = ipAdministrators;
//        $scope.activeAdministrator = null;
//
//        console.log(ipAdministrators);
//    };

    ipAdministratorsController = function ($scope, $location) {



        //init
        $scope.administrators = ipAdministrators;
        $scope.activeAdministrator = null;
        $scope.editMode = false;
        $scope.ipAdministratorsAdminId = ipAdministratorsAdminId;


        $scope.$on('PathChanged', function (event, path) {
            var administratorId = getHashParams().administrator;
            if (administratorId) {
                $scope.activateAdministrator(administratorId);
            }
        });


        $scope.setAdministratorHash = function (administrator) {
            updateHash(null, administrator.id, false);
        }

        $scope.activateAdministrator = function (administrator) {
            $scope.activeAdministrator = administrator;
        }

        $scope.addModal = function () {
            $('.ipsAddModal').modal();
            //$('.ipsAddModal').find("input[name=username]").hide();
            $('.ipsAddModal form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var username = $form.find('input[name=username]').val();
                var email = $form.find('input[name=email]').val();
                var password = $form.find('input[name=password]').val();
                addAdministrator(username, email, password);
            });
            setTimeout(function() {$('.ipsAddModal input[name=username]').focus();}, 500);
            $('.ipsAddModal').find('.ipsAdd').on('click', function () {
                $('.ipsAddModal form').submit();
            });
        }

        $scope.setEditMode = function (mode) {
            $scope.editMode = mode;
        }


        $scope.updateModal = function () {
            $('.ipsUpdateModal').modal();
            $('.ipsUpdateModal form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var username = $form.find('input[name=username]').val();
                var email = $form.find('input[name=email]').val();
                var password = $form.find('input[name=password]').val();
                updateAdministrator($scope.activeAdministrator.id, username, email, password);
            });
            setTimeout(function() {$('.ipsUpdateModal input[name=username]').focus();}, 500);
        }

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
        }


        var addAdministrator = function (username, email, password) {
            var data = {
                aa: 'Administrators.add',
                securityToken: ip.securityToken,
                username: username,
                email: email,
                password: password
            }
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    $scope.administrators.push({
                        username: username,
                        email: email,
                        password: password
                    });
                    $scope.$apply();
                    $('.ipsAddModal').modal('hide');
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });

        }


        var updateAdministrator = function (id, username, email, password) {
            var data = {
                aa: 'Administrators.update',
                securityToken: ip.securityToken,
                id: id,
                username: username,
                email: email,
                password: password
            };
            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    if (response && response.status == 'ok') {
                        $scope.activeAdministrator.username = username;
                        $scope.activeAdministrator.email= email;
                        $scope.$apply();
                        $('.ipsUpdateModal').modal('hide');
                    }
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        }

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
        }


        var updateHash = function (administrator) {
            var path = 'hash&administrator=' + administrator.id;
            $location.path(path);
        }

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


})(ip.jQuery);


