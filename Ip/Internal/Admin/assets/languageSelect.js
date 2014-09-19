//select language in login screen
$('.ipsLanguageSelect').find('select').on('change', function () {
    "use strict";
    var $select = $(this);
    var data = {
        sa: 'Admin.changeLanguage',
        securityToken: ip.securityToken,
        languageCode: $select.val()
    };
    $.ajax({
        type: 'POST',
        url: ip.baseUrl,
        data: data,
        success: function (response) {
            window.location.reload(true);
        },
        error: function (response) {
            alert('Error: ' + response.responseText);
        },
        dataType: 'json'
    });
});

