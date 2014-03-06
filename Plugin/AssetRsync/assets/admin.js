$(document).ready(function () {
    AssetRsync.init();
});

var AssetRsync = new function () {
    "use strict";

    this.init = function () {
        $('.ipsSyncAssets').on('click', this.syncAssets);


        $('.ipsAssetRsyncOptions').on('ipSubmitResponse', function (e, response) {
            if (response.result) {
                alert('OK');
            }
        });
    };


    this.syncAssets = function () {
        var postData = {};
        postData.aa = 'AssetRsync.syncAssets';
        postData.securityToken = ip.securityToken;
        postData.jsonrpc = '2.0';

        $.ajax({
            url: ip.baseUrl,
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.result) {
                    // window.location = window.location.href.split('#')[0];
                    alert('success');
                } else {
                    if (response && response.error && response.error.message) {
                        alert(response.error.message);
                    } else {
                        alert('Unknown error. Logs will not help you.');
                    }
                }
            },
            error: function () {
                alert('Unknown error. Logs will not help you.');
            }
        });
    };
};
