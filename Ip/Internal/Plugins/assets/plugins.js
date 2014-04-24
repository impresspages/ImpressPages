var ipPlugins = null;

(function ($) {
    "use strict";

    var app = angular.module('Plugins', []);

    app.run(function ($rootScope) {
        $rootScope.$on('$locationChangeSuccess', function (e, newUrl, oldUrl) {
            $rootScope.$broadcast('PathChanged', newUrl);
        });
    });

    ipPlugins = function ($scope, $location) {
        //init
        $scope.selectedPluginName = null;

        $scope.$on('PathChanged', function (event, path) {
            var pluginName = getHashParams().plugin;

            if (!$scope.selectedPluginName) {
                pluginName = $('.ipsPlugin')[0];
            }

            if (pluginName && pluginName != $scope.selectedPluginName) {
                $scope.showPlugin(pluginName);
            }
        });

        $scope.showPlugin = function (pluginName) {
            $scope.selectedPluginName = pluginName;
            var $properties = $('.ipsProperties');
            $properties.ipPluginProperties({
                pluginName: pluginName
            });
        }

        var updateHash = function (pluginName) {
            var curVariables = getHashParams();
            curVariables['/hash'] = '';

            curVariables.plugin = pluginName ? pluginName : null;

            var path = '';
            $.each(curVariables, function(key, value){
                if (value != null) {
                    if (path != '') {
                        path = path + '&';
                    }
                    path = path + key + '=' + value;
                }
            });
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

    var activate = function () {
        var pluginName = $(this).closest('.ipsPlugin').data('pluginname');

        var postData = {};
        postData.aa = 'Plugins.activate';
        postData.securityToken = ip.securityToken;
        postData.jsonrpc = '2.0';
        postData.params = {pluginName : pluginName};

        $.ajax({
            url: ip.baseUrl,
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.result) {
                    window.location = window.location.href.split('#')[0];
                } else {
                    if (response && response.error && response.error.message) {
                        alert(response.error.message);
                    } else {
                        alert('Unknown error. Please see logs.');
                    }
                }
            },
            error: function () {
                alert('Unknown error. Please see logs.');
            }
        });

    };

    var deactivate = function () {
        var pluginName = $(this).closest('.ipsPlugin').data('pluginname');

        var postData = {};
        postData.aa = 'Plugins.deactivate';
        postData.securityToken = ip.securityToken;
        postData.jsonrpc = '2.0';
        postData.params = {pluginName : pluginName};

        $.ajax({
            url: ip.baseUrl,
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.result) {
                    window.location = window.location.href.split('#')[0];
                } else {
                    if (response && response.error && response.error.message) {
                        alert(response.error.message);
                    } else {
                        alert('Unknown error. Please see logs.');
                    }
                }
            },
            error: function () {
                alert('Unknown error. Please see logs.');
            }
        });
    }

    var remove = function () {
        var pluginName = $(this).closest('.ipsPlugin').data('pluginname');

        var postData = {};
        postData.aa = 'Plugins.remove';
        postData.securityToken = ip.securityToken;
        postData.jsonrpc = '2.0';
        postData.params = {pluginName : pluginName};

        $.ajax({
            url: ip.baseUrl,
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.result) {
                    window.location = window.location.href.split('#')[0];
                } else {
                    if (response && response.error && response.error.message) {
                        alert(response.error.message);
                    } else {
                        alert('Unknown error. Please see logs.');
                    }
                }
            },
            error: function () {
                alert('Unknown error. Please see logs.');
            }
        });
    }

//    this.init = function () {
//        $('.ipsModulePlugins .ipsActivate').on('click', activate);
//        $('.ipsModulePlugins .ipsDeactivate').on('click', deactivate);
//        $('.ipsModulePlugins .ipsRemove').on('click', remove);
//    };

})(jQuery);
