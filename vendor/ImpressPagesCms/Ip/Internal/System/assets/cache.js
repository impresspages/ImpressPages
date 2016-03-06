$( document ).ready(function() {
    "use strict";
    $('.ipsClearCache').on('click', function(e) {
        e.preventDefault();
        $.ajax(ip.baseUrl, {
            'type': 'POST',
            'data': {'aa': 'System.clearCache', 'securityToken': ip.securityToken},
            'dataType': 'json',
            'success': function (data) {
                window.location.reload(true);
                //window.location = window.location.split('#')[0];
            },
            'error': function (response) {
                alert('Unexpected error.' + response.responseText);
            }
        });
    });
});
