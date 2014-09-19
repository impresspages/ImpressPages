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
        $scope.selectedPlugin = {name: null, title: null};
        $scope.pluginList = pluginList;

        $scope.$on('PathChanged', function (event, path) {
            var pluginName = getHashParams().plugin;

            // selecting first active plugin
            if (!pluginName) {
                $.each(pluginList, function (key, value) {
                    if (value.active && !pluginName) {
                        pluginName = value.name;
                    }
                });

                // if we don't have active, selected first from the list
                if (!pluginName && pluginList[0]) {
                    pluginName = pluginList[0].name;
                }
            }

            if (pluginName && pluginName != $scope.selectedPlugin.name) {
                $.each(pluginList, function (key, value) {
                    if (value.name == pluginName) {
                        $scope.showPlugin(value);
                    }
                });
            }
        });

        $scope.showPlugin = function (plugin) {
            $scope.selectedPlugin = plugin;
            var $properties = $('.ipsProperties');
            $properties.ipPluginProperties({
                pluginName: plugin.name
            });

            $properties.off('deactivate.ipPlugins').on('deactivate.ipPlugins', function () {
                deactivate($scope.selectedPlugin.name);
            });
            $properties.off('activate.ipPlugins').on('activate.ipPlugins', function () {
                activate($scope.selectedPlugin.name);
            });
            $properties.off('delete.ipPlugins').on('delete.ipPlugins', function () {
                deletePlugin($scope.selectedPlugin.name);
            });
        };

        $scope.setPluginHash = function (plugin) {
            updateHash(plugin.name);
        };


        var activate = function (pluginName) {
            var postData = {};
            postData.aa = 'Plugins.activate';
            postData.securityToken = ip.securityToken;
            postData.jsonrpc = '2.0';
            postData.params = {pluginName: pluginName};

            $.ajax({
                url: ip.baseUrl,
                data: postData,
                dataType: 'json',
                type: 'POST',
                success: function (response) {
                    if (response && response.result) {
                        location.reload();
                    } else {
                        if (response && response.error && response.error.message) {
                            alert(response.error.message);
                        } else {
                            alert('Error: ' + response.responseText);
                        }
                    }
                },
                error: function (response) {
                    alert('Error: ' + response.responseText);
                }
            });

        };

        var deactivate = function (pluginName) {
            var postData = {};
            postData.aa = 'Plugins.deactivate';
            postData.securityToken = ip.securityToken;
            postData.jsonrpc = '2.0';
            postData.params = {pluginName: pluginName};

            $.ajax({
                url: ip.baseUrl,
                data: postData,
                dataType: 'json',
                type: 'POST',
                success: function (response) {
                    if (response && response.result) {
                        location.reload();
                    } else {
                        if (response && response.error && response.error.message) {
                            alert(response.error.message);
                        } else {
                            alert('Error: ' + response.responseText);
                        }
                    }
                },
                error: function (response) {
                    alert('Error: ' + response.responseText);
                }
            });
        };

        // 'delete' is predefined class
        var deletePlugin = function (pluginName) {
            var postData = {};
            postData.aa = 'Plugins.remove';
            postData.securityToken = ip.securityToken;
            postData.jsonrpc = '2.0';
            postData.params = {pluginName: pluginName};

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
                            alert('Error: ' + response.responseText);
                        }
                    }
                },
                error: function (response) {
                    alert('Error: ' + response.responseText);
                }
            });
        };

        var updateHash = function (pluginName) {
            var curVariables = getHashParams();
            curVariables['/hash'] = '';

            curVariables.plugin = pluginName ? pluginName : null;

            var path = '';
            $.each(curVariables, function (key, value) {
                if (value != null) {
                    if (path != '') {
                        path = path + '&';
                    }
                    path = path + key + '=' + value;
                }
            });
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
    };


//    this.init = function () {
//        $('.ipsModulePlugins .ipsActivate').on('click', activate);
//        $('.ipsModulePlugins .ipsDeactivate').on('click', deactivate);
//        $('.ipsModulePlugins .ipsRemove').on('click', remove);
//    };

})(jQuery);
