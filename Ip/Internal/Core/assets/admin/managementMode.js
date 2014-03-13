// defining global variables
var ipManagementMode;

(function($) {
    "use strict";
    $( document ).ready(function() {
        ipManagementMode.init();
    });

    ipManagementMode = new function() {

        this.init = function() {
            $(document).on('ipAdminPanelInit', function () {
                $('.ipsContentEdit').on('click', function() {
                    ipManagementMode.setManagementMode(1);
                });
            });

            if ("undefined" !== typeof(ipWidgetSnippets)) {
                $.each(ipWidgetSnippets, function( index, value ) {
                    $('body').append(value);
                });
            }
            if (typeof ipWidgetLayoutModalTemplate  !== "undefined") {
                $('body').append(ipWidgetLayoutModalTemplate);
            }
            if (typeof ipBrowseLinkModalTemplate  !== "undefined") {
                $('body').append(ipBrowseLinkModalTemplate);
            }
        };

        this.setManagementMode = function(mode) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'POST',
                data: {aa: 'Content.setManagementMode', value: mode, securityToken: ip.securityToken},
                success: function (response) {
                    if (response) {
                        window.location = window.location.href.split('?')[0] + '?_revision=' + ip.revisionId;
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
    };


})(ip.jQuery);
