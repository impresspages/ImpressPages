
$( document ).ready(function () {
    ipModulePlugins.init();
});

var ipModulePlugins = new function () {
    "use strict";

    var activate = function () {
        var pluginName = $(this).closest('.panel').data('pluginname');

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
        var pluginName = $(this).closest('.panel').data('pluginname');

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
        var pluginName = $(this).closest('.panel').data('pluginname');

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

    this.init = function () {
        $('.ipModulePlugins .ipsActivate').on('click', activate);
        $('.ipModulePlugins .ipsDeactivate').on('click', deactivate);
        $('.ipModulePlugins .ipsRemove').on('click', remove);
    };
};


