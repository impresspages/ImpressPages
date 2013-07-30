/**
 * @package ImpressPages
 */

var Market = new function() {

    var imagesDownloaded; //true if images have been downloaded
    var imagesData; //downloaded images data
    var themesDownloaded; //true if images have been downloaded
    var themesData; //downloaded themes data

    this.processOrder = function(order) {
        $('body').trigger('ipMarketOrderStart');

        if (typeof(order.images) != "undefined") {
            imagesDownloaded = false;
            downloadImages(order.images);
        } else {
            imagesDownloaded = true;
        }

        if (typeof(order.themes) != "undefined") {
            themesDownloaded = false;
            downloadThemes(order.themes);
        } else {
            themesDownloaded = true;
        }



    };

    var checkComplete = function() {
        if (imagesDownloaded && themesDownloaded) {
            $('body').trigger('ipMarketOrderComplete', [{images: imagesData, themes: themesData}]);
        }
    };

    var downloadImages = function(images) {
        var toDownload = new Array();

        for (var i = 0; i < images.length; i++) {
            toDownload.push({
                url: images[i].downloadUrl,
                title: images[i].title
            });
        }

        $.ajax(ip.baseUrl, {
            'type': 'POST',
            'data': {'g': 'administrator', 'm': 'repository', 'a': 'addFromUrl', 'files': toDownload},
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

        $('#ipModuleRepositoryTabBuy .ipmLoading').removeClass('ipgHide');
    };


    var downloadThemes= function(themes) {
        $('body').trigger('ipMarketOrderThemeDownload', {});
        themesDownloaded = true;
        themesData = {};
        checkComplete();
//        var toDownload = new Array();
//
//        for (var i = 0; i < images.length; i++) {
//            toDownload.push({
//                url: images[i].downloadUrl,
//                title: images[i].title
//            });
//        }
//
//        $.ajax(ip.baseUrl, {
//            'type': 'POST',
//            'data': {'g': 'administrator', 'm': 'repository', 'a': 'addFromUrl', 'files': toDownload},
//            'dataType': 'json',
//            'success': function (data) {
//                $('body').trigger('ipMarketOrderImageDownload', data);
//                tasks
//            },
//            'error': function () {
//                alert('Download failed.');
//                $('body').trigger('ipMarketOrderImageDownload', {}); }
//        });
//
//        $('#ipModuleRepositoryTabBuy .ipmLoading').removeClass('ipgHide');
    };

};