/**
 * @package ImpressPages
 */


var Market;

(function ($) {
    "use strict";
    Market = new function () {

        var imagesDownloaded; //true if images have been downloaded
        var imagesData; //downloaded images data
        var themesDownloaded; //true if images have been downloaded
        var themesData; //downloaded themes data
        var pluginsDownloaded; // true if images have been downloaded
        var pluginsData; // downloaded themes data

        this.processOrder = function (order) {
            $('body').trigger('ipMarketOrderStart');


            if (typeof(order.images) != "undefined" && order.images.length) {
                imagesDownloaded = false;
                downloadImages(order.images);
            } else {
                imagesDownloaded = true;
            }

            if (typeof(order.themes) != "undefined" && order.themes.length) {
                themesDownloaded = false;
                downloadThemes(order.themes);
            } else {
                themesDownloaded = true;
            }

            if (typeof(order.plugins) != "undefined" && order.plugins.length) {
                pluginsDownloaded = false;
                downloadPlugins(order.plugins);
            } else {
                pluginsDownloaded = true;
            }

        };

        var checkComplete = function () {
            if (imagesDownloaded && themesDownloaded && pluginsDownloaded) {
                $('body').trigger('ipMarketOrderComplete', [
                    {images: imagesData, themes: themesData, plugins: pluginsData}
                ]);
            }
        };

        var downloadImages = function (images) {

            if (images.length == 0) {
                $('body').trigger('ipMarketOrderImageDownload', {});
                imagesDownloaded = true;
                imagesData = {};
                checkComplete();
                return;
            }

            var toDownload = [];

            for (var i = 0; i < images.length; i++) {
                toDownload.push({
                    url: images[i].downloadUrl,
                    title: images[i].title
                });
            }

            $.ajax(ip.baseUrl, {
                'type': 'POST',
                'data': {'aa': 'Repository.addFromUrl', 'files': toDownload, 'securityToken': ip.securityToken},
                'dataType': 'json',
                'success': function (data) {
                    $('body').trigger('ipMarketOrderImageDownload', data);
                    imagesDownloaded = true;
                    imagesData = data;
                    checkComplete();
                },
                'error': function () {
                    alert('Download failed.');
                    $('body').trigger('ipMarketOrderImageDownload', {});
                    imagesDownloaded = true;
                    imagesData = {};
                    checkComplete();
                }
            });

            $('#ipsModuleRepositoryTabBuy').find('.ipsLoading').removeClass('hidden');
        };


        var downloadThemes = function (themes) {

            if (themes.length == 0) {
                $('body').trigger('ipMarketOrderThemeDownload', {});
                themesDownloaded = true;
                themesData = {};
                checkComplete();
                return;
            }

            var toDownload = [];

            for (var i = 0; i < themes.length; i++) {
                toDownload.push({
                    url: themes[i].downloadUrl,
                    name: themes[i].name,
                    signature: themes[i].signature
                });
            }

            $.ajax(ip.baseUrl, {
                'type': 'POST',
                'data': {'aa': 'Design.downloadThemes', 'themes': toDownload, 'securityToken': ip.securityToken, 'jsonrpc': '2.0'},
                'dataType': 'json',
                'success': function (response) {
                    if (!response || response.error || !response.result || !response.result.themes) {
                        alert('Unknown error. Please see logs.');
                        return;
                    }

                    themesDownloaded = true;
                    themesData = response.result.themes;
                    checkComplete();
                },
                'error': function () {
                    alert('Unknown error. Please see logs.');
                }
            });
        };

        var downloadPlugins = function (plugins) {

            if (plugins.length == 0) {
                $('body').trigger('ipMarketOrderPluginDownload', {});
                pluginsDownloaded = true;
                pluginsData = {};
                checkComplete();
                return;
            }

            var toDownload = [];

            for (var i = 0; i < plugins.length; i++) {
                toDownload.push({
                    url: plugins[i].downloadUrl,
                    name: plugins[i].name,
                    signature: plugins[i].signature
                });
            }

            $.ajax(ip.baseUrl, {
                'type': 'POST',
                'data': {'aa': 'Plugins.downloadPlugins', 'plugins': toDownload, 'securityToken': ip.securityToken, 'jsonrpc': '2.0'},
                'dataType': 'json',
                'success': function (response) {
                    if (!response || response.error || !response.result || !response.result.plugins) {

                        if (response.error.message) {
                            alert(response.error.message);
                        } else {
                            alert('Unknown error. Please see logs.');
                        }
                        return;
                    }

                    pluginsDownloaded = true;
                    pluginsData = response.result.plugins;
                    checkComplete();
                },
                'error': function () {
                    alert('Unknown error. Please see logs.');
                }
            });
        };

    };

})(jQuery);
