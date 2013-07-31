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
        console.log('order');
        console.log(order);

        if (typeof(order.images) != "undefined" && order.images.length) {
            console.log('downloadImages');
            imagesDownloaded = false;
            downloadImages(order.images);
        } else {
            imagesDownloaded = true;
        }

        if (typeof(order.themes) != "undefined" && order.themes.length) {
            console.log('downloadThemes');
            themesDownloaded = false;
            downloadThemes(order.themes);
        } else {
            themesDownloaded = true;
        }



    };

    var checkComplete = function() {
        console.log('checkComplete ' + imagesDownloaded + ' ' + themesDownloaded);
        if (imagesDownloaded && themesDownloaded) {
            console.log('orderCompleteEvent2');
            console.log('body');
            console.log($('body'));
            $('body').trigger('ipMarketOrderComplete', [{images: imagesData, themes: themesData}]);
            console.log('body');
            console.log($('body'));

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

        if (toDownload.length == 0) {
            $('body').trigger('ipMarketOrderImageDownload', {});
            imagesDownloaded = true;
            imagesData = {};
            checkComplete();
            return;
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