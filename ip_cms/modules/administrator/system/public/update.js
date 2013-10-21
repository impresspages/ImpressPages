$(document).ready(function () {
    "use strict";

    var postData = {};
    postData.g = 'administrator';
    postData.m = 'system';
    postData.aa = 'getSystemInfo';
    postData.securityToken = ip.securityToken;

    postData.jsonrpc = '2.0';

    $.ajax({
        url: ip.baseUrl,
        data: postData,
        dataType: 'json',
        type: 'POST',
        success: mod_administrator_system_publish_updates,
        error: function () {
            alert('Unknown error. Please see logs.');
        }
    });

    $('.actStartUpdate').live('click', startUpdate);
});


function mod_administrator_system_publish_updates(response) {
    "use strict";

    var container = document.getElementById('systemInfo');
    var messages = '';
    if (response != '') {
        messages = response;
        if (messages.length > 0) {
            container.style.display = '';
            var i = 0;
            for (i = 0; i < messages.length; i++) {
                container.innerHTML = container.innerHTML + '<div class="' + messages[i]['type'] + '">' + messages[i]['message'] + '</div>';

                if (messages[i]['code'] == 'update') {
                    container.innerHTML = container.innerHTML + ' <a target="_blank" class="button" href="' + messages[i]['downloadUrl'] + '">Download</a> <a class="button actStartUpdate" href="' + messages[i]['downloadUrl'] + '">Start update</a><br/><br/>';
                }
                container.innerHTML = container.innerHTML + '<div class="clear"></div>';
            }
        }
    }

}


function startUpdate(e) {
    "use strict";

    e.preventDefault();

    var postData = {};
    postData.g = 'administrator';
    postData.m = 'system';
    postData.aa = 'startUpdate';
    postData.securityToken = ip.securityToken;

    $.ajax({
        url: ip.baseUrl,
        data: postData,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            if (!response) {
                return;
            }
            if (response.status && response.status == 'success') {
                if (response.redirectUrl) {
                    parent.document.location = response.redirectUrl;
                }
            } else {
                if (response.error) {
                    alert(response.error);
                }
            }
        },
        error: function () {
            alert('Unknown error. Please see logs.');
        }
    });

}