$(document).ready(function () {
    "use strict";

    if (ipSystemSendUsageStatistics) {
        ipSendUsageStatistics();
    }
});

var ipSendUsageStatistics = function (data) {
    "use strict";

    // Works only if admin is logged in (AJAX is sent to Admin Controller)
    var postData = {
        'aa': 'System.sendUsageStatisticsAjax',
        'data': data,
        'securityToken': ip.securityToken,
        'jsonrpc': '2.0'
    };

    $.ajax({
        url: ip.baseUrl,
        data: postData,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            // do nothing
        },
        error: function (response) {
            // do nothing
        }
    });
};
