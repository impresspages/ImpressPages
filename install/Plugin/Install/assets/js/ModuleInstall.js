var ModuleInstall = new function () {
    "use strict";

    var replaceTables = 0;
    var context = this;

    this.submitDatabase = function () {
        $('.ipsErrorContainer').empty();

        var db = {
            'hostname': $('#db_server').val(),
            'username': $('#db_user').val(),
            'password': $('#db_pass').val(),
            'database': $('#db_db').val(),
            'tablePrefix': $('#db_prefix').val()
        };

        if (replaceTables) {
            db.replaceTables = 1;
        }

        var postData = {
            'pa': 'Install.createDatabase',
            'db': db,
            'jsonrpc': '2.0'
        };

        $.ajax({
            url: 'index.php',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                var $errorContainer = $('.ipsErrorContainer');
                if (response && response.result) {
                    $errorContainer.html('');
                    document.location = response.result.redirect;
                } else if (response && response.error && response.error.code && response.error.code == 'table_exists' && response.error.message) {
                    if (confirm(response.error.message)) {
                        replaceTables = 1;
                        context.submitDatabase();
                    }
                } else if (response && response.error && response.error.message) {
                    $errorContainer.html('<p class="alert alert-danger">' + response.error.message + '</p>');
                } else {
                    alert(response.responseText);
                }
            },
            error: function (response) {
                alert('Error: ' + response.responseText);
            }
        });
    };

    this.submitConfiguration = function() {
        $('.ipsErrorContainer').empty();

        var postData = {
            'pa': 'Install.testConfiguration',
            'configWebsiteName': $('#ipsConfigWebsiteName').val(),
            'configWebsiteEmail': $('#ipsConfigWebsiteEmail').val(),
            'configTimezone': $('#ipsConfigTimezone').val(),
            'configSupport': $('#ipsConfigSupport').prop('checked') ? 1 : 0,
            'jsonrpc': '2.0'
        };

        $.ajax({
            url: 'index.php',
            data: postData,
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                var $errorContainer = $('.ipsErrorContainer');
                if (response && response.result) {
                    $errorContainer.html('');
                    document.location = response.result.redirect;
                } else if (response && response.error && response.error.message) {
                    var errors = response.error.errors;
                    var errorsText = '';
                    for (var i = 0; i < errors.length; ++i) {
                        errorsText += '<br>- ' + errors[i];
                    }
                    $errorContainer.html('<p class="alert alert-danger">' + response.error.message + errorsText + '</p>');
                } else {
                    alert(response.responseText);
                }
            },
            error: function (response) {
                alert('Unexpected error.' + response.responseText);
            }
        });
    };
};
