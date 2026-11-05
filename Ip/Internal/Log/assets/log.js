
$('.ipsGrid').on('init.ipGrid', function () {
    "use strict";
    $('.ipsClearAll').on('click', function (e) {
        ipAdminConfirm({
            text: clearConfirmTranslation,
            confirmLabel: clearConfirmButtonTranslation,
            danger: true
        }).then(function (confirmed) {
            if (confirmed) {
                clearLog();
            }
        });
    });

    var clearLog = function () {
        var postData = {
            'aa': 'Log.clear',
            'securityToken': ip.securityToken
        };
        $.ajax({
            url: 'index.php',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                window.location = ip.baseUrl + '?aa=Log';
                window.location.reload(true);
            },
            error: function (response) {
                ipAdminNotify.ajaxError(response);
            }
        });
    };

});

