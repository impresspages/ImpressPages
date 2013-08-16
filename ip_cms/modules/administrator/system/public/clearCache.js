"use strict";


$(document).ready(function() {
    $('.ipsClearCache').on('click', ipSystemClearCache);
});

function ipSystemClearCache(e)
{
    e.preventDefault();
    var $this = $(this);
    var postData = Object();
    postData.g = 'administrator';
    postData.m = 'system';
    postData.ba = 'clearCache';
    postData.securityToken = ip.securityToken;

    postData.jsonrpc = '2.0'

    $.ajax({
        url: ip.baseUrl,
        data: postData,
        dataType: 'json',
        type : 'POST',
        success: function (response){
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