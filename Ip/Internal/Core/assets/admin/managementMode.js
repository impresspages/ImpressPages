// defining global variables


$(document).ready(function () {
    "use strict";
    ipManagementMode.init();
});

var ipManagementMode = new function () {
    "use strict";
    this.init = function () {
        $(document).on('ipAdminPanelInit', function () {
            $('.ipsContentEdit').on('click', function () {
                ipManagementMode.setManagementMode(1);
            });
        });

        if ("undefined" !== typeof(ipWidgetSnippets)) {
            $.each(ipWidgetSnippets, function (index, value) {
                $('body').append(value);
            });
        }
        if (typeof ipWidgetLayoutModalTemplate !== "undefined") {
            $('body').append(ipWidgetLayoutModalTemplate);
        }
        if (typeof ipBrowseLinkModalTemplate !== "undefined") {
            $('body').append(ipBrowseLinkModalTemplate);
        }
    };

    this.setManagementMode = function (mode) {
        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type: 'POST',
            data: {aa: 'Content.setManagementMode', value: mode, securityToken: ip.securityToken},
            success: function (response) {
                if (response) {
                    var newLocation = window.location.href.split('#')[0].split('?')[0];
                    if (mode == 0 || getParameterByName('_revision')){
                        newLocation = newLocation + '?_revision=' + ip.revisionId;
                    }
                    window.location = newLocation;
                } else {
                    //login has expired
                    window.location = ip.baseUrl + 'admin';
                }
            },
            error: function (response) {
                alert('error: ' + response);
            }
        });
    };

    var getParameterByName = function (name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }
};

