$(document).ready(function() {
    "use strict";

    // Website configuration
    $('#configSiteName').focus();
    $('.ipsTOS').on('click', function() {
        $('#ipsTOS').modal();
    });
    $('.ipsMoreConfiguration').on('click', function () {
        var $block = $('#ipsMoreConfiguration');
        var $input = $('#ipsConfigExpanded');
        if ($block.hasClass('hidden')) {
            $block.removeClass('hidden');
            $input.val(1);
        } else {
            $block.addClass('hidden');
            $input.val(0);
        }
    });
    $('.ipsConfigurationForm').on('submit', function (e) {
        e.preventDefault();
        ModuleInstall.submitConfiguration();
    });

    // Database configuration
    $('#db_server').focus();
    $('.ipsDatabaseForm').on('submit', function (e) {
        e.preventDefault();
        ModuleInstall.submitDatabase();
    });


    // URL rewrites check

    // disable Next button until we check rewrites
    $('.ipsUrlRewritesCheck').addClass('disabled');

    $.ajax({
        url: 'Plugin/Install/test/check-rewrites.php',
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            // the result is stored in the session
            // so we can ignore the response
            $('.ipsUrlRewritesCheck').removeClass('disabled');
        },
        error: function (response) {
            // the result is stored in the session
            // so we can ignore the response
            $('.ipsUrlRewritesCheck').removeClass('disabled');
        }
    });

});
