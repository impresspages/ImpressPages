(function ($) {
    "use strict";
    if ($('.ipsStartMigration').length) {
        $('.ipsStartMigration').on('click', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            runMigrations(url);
        });
    }


})(jQuery);

function runMigrations(url) {
    $.ajax({
        url: url,
        data: {},
        dataType: 'json',
        type: 'GET',
        success: migrationResponse,
        error: function (response) {
            alert(response.responseText);
        }
    });
}

function migrationResponse(response) {
    window.location.href = window.location.href;
}