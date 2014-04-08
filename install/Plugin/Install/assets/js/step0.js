

$(document).ready(function() {
    "use strict";

    // disable Next button until we check rewrites
    $('.ipsStep0').addClass('disabled');

    var proceed = function (e) {
        e.preventDefault();
        var postData = {
            'pa': 'Install.proceed'
        };
        $.ajax({
            url: 'index.php',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                var proceedUrl = 'index.php?step=1';
                document.location = proceedUrl;
            },
            error: function (response) {
                $('#loading').hide();
                $('#content').show();

                alert('Error: ' + response.responseText);
            }
        })
    }

    $.ajax({
        url: 'Plugin/Install/test/check-rewrites.php',
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            // the result is stored in the session
            // so we can ignore the response
            $('.ipsStep0').removeClass('disabled');
            $('.ipsStep0').on('click', proceed);
        },
        error: function (response) {
            $('.ipsStep0').removeClass('disabled');
            $('.ipsStep0').on('click', proceed);
        }
    });

});

