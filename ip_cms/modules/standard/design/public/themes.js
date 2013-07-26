//create crossdomain socket connection
var remote = new easyXDM.Rpc({
        remote: $('#ipModuleDesignContainer').data('marketurl'),
        container: "ipModuleDesignContainer",
        onMessage: function(message, origin){
            //DO NOTHING
        },
        onReady: function() {
            //DO NOTHING
        }
    },
    {
        remote: {
        },
        local: {
            downloadImages: function(images){
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
                        $.proxy(methods._confirm, buyTab, data)();
                    },
                    'error': function () { alert('Download failed.'); }
                });

                $('#ipModuleRepositoryTabBuy .ipmLoading').removeClass('ipgHide');
            }
        }
    }

);