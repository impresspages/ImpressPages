$(document).ready(function () {
    "use strict";
    $('.ipsInstallPlugin').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        var postData = {};
        postData.aa = 'Design.installPlugin';
        postData.securityToken = ip.securityToken;
        postData.params = {
            'pluginName': $this.data('pluginname')
        };

        postData.jsonrpc = '2.0';

        $.ajax({
            url: ip.baseUrl,
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                if (response && response.result) {
                    window.location = window.location.href.split('#')[0];
                }
                if (response && response.error && response.error.message) {
                    alert(response.error.message);
                }
            },
            error: function () {
                alert('Unknown error. Please see the logs.');
            }
        });

    });

});
