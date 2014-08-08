var ipTrash = null;

(function ($) {
    'use strict';

    var app = angular.module('System', []);

    app.run(function ($rootScope) {
        $rootScope.$on('$locationChangeSuccess', function (e, newUrl, oldUrl) {
            $rootScope.$broadcast('PathChanged', newUrl);
        });
    });

    ipTrash = function ($scope, $location) {

        $scope.recoveryPageModal = function () {
            var $modal = $('.ipsRecoveryPageModal');
            $modal.modal();

            $modal.find('.ipsRecovery').off('click').on('click', function () {
                $modal.find('form').submit();
            });
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var pages = '';
                $modal.find('input[name="page[]"]:checked').each(function () {
                    pages += '|' + $(this).val();
                });
                recoveryPage(pages);
                $modal.modal('hide');
            });
        };

        $scope.emptyPageModal = function () {
            var $modal = $('.ipsEmptyPageModal');
            $modal.modal();

            $modal.find('.ipsEmpty').off('click').on('click', function () {
                $modal.find('form').submit();
            });
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var pages = '';
                $modal.find('input[name="page[]"]:checked').each(function () {
                    pages += '|' + $(this).val();
                });
                emptyPage(pages);
                $modal.modal('hide');
            });
        }

    };

    var recoveryPage = function (pages) {
        var data = {
            aa: 'System.recoveryTrash',
            pages: pages,
            securityToken: ip.securityToken
        };
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                if (response.error && response.errorMessage) {
                    alert(response.errorMessage);
                } else {
                    location.reload(true);
                }
            },
            error: function (response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            },
            dataType: 'json'
        });
    };

    var emptyPage = function (pages) {
        var data = {
            aa: 'System.emptyTrash',
            pages: pages,
            securityToken: ip.securityToken
        };
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: this,
            success: function (response) {
                if (response.error && response.errorMessage) {
                    alert(response.errorMessage);
                } else {
                    location.reload(true);
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

})(jQuery);
