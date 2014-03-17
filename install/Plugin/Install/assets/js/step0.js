

$(document).ready(function() {
    "use strict";

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

    $('.ipsStep0').on('click', proceed);


});

