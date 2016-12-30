$(document).ready(function() {
    "use strict";

    // Website configuration
    $('#ipsConfigWebsiteName').focus();
    $('.ipsTOS').on('click', function() {
        $('#ipsTOS').modal();
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
        url: baseUrl + 'unknownurl/?step=check-rewrites',
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


    $.ajax({
        url: 'index.php',
        dataType: 'json',
        data: {pa: 'Install.testSessions'},
        type: 'GET',
        success: function (response) {
            if (response && response.html) {
                $('.ipsContent').html(response.html);
            }
        },
        error: function (response) {
            
        }
    });

});
