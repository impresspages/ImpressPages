
$('.ipsGrid').on('init.ipGrid', function () {
    "use strict";
    $('.ipsClearAll').on('click', function (e) {
        if (!confirm(clearConfirmTranslation)) {
            return;
        }
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
                alert(response.responseText);
            }
        });
    });

});

