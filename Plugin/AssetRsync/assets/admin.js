$(document).ready(function () {
    AssetRsync.init();
});

var AssetRsync = new function () {
    "use strict";

    this.init = function () {
        $('.ipsSyncAssets').on('click', this.syncAssets);


        $('.ipsAssetRsyncOptions').validator(validatorConfig);
        $('.ipsAssetRsyncOptions').submit(function (e) {
            var form = $(this);

            // client-side validation OK.
            if (!e.isDefaultPrevented()) {
                $.ajax({
                    url: ip.baseUrl,
                    dataType: 'json',
                    type : 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        if (response.result) {
                            alert('OK');
                        } else {
                            //PHP controller says there are some errors
                            if (response.error) {
                                form.data("validator").invalidate(response.error.data);
                            }
                        }
                    }
                });
            }
            e.preventDefault();
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