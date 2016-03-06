(function ($) {
    "use strict";
    if ($('.ipsStartMigration').length) {
        $('.ipsStartMigration').on('click', function (e) {
            e.preventDefault();
            var $link = $(this);

            $.ajax({
                url: $link.attr('href'),
                data: {},
                dataType: 'json',
                type: 'GET',
                success: migrationResponse,
                error: function (response) {
                    alert(response.responseText);
                }
            });
        });
    }

    function migrationResponse(response) {
        window.location.href = window.location.href;
    }
})(jQuery);
