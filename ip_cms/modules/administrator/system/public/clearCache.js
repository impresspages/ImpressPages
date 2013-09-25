$(document).ready(function () {
    "use strict";
    $('.ipsClearCache').on('click', ipSystemClearCache);
});

function ipSystemClearCache(e) {
    "use strict";
    e.preventDefault();
    var $this = $(this);
    var postData = {};
    postData.g = 'administrator';
    postData.m = 'system';
    postData.aa = 'clearCache';
    postData.securityToken = ip.securityToken;

    postData.jsonrpc = '2.0';

    $.ajax({
        url: ip.baseUrl,
        data: postData,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            if (response && response.result && response.result.redirectUrl) {
                window.location = response.result.redirectUrl;
            } else {
                alert('Unknown error. Please see logs.');
            }
        },
        error: function () {
            alert('Unknown error. Please see logs.');
        }
    });

}