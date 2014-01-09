// defining global variables
var ipManagementMode;

(function($) {
    "use strict";
    $( document ).ready(function() {
        ipManagementMode.init();
    });

    ipManagementMode = new function() {

        this.init = function() {
            if ("undefined" !== typeof(ipContentShowEditButton)) {
                $('body').append('<div class="ipsContentEdit" style="position: fixed; right: 0px; top: 0px; background-color: #000; color: #fff; padding: 10px;">{{Edit}}</div>');
                $('.ipsContentEdit').on('click', function() {
                    ipManagementMode.setManagementMode(1);
                });
            }
            if ("undefined" !== typeof(ipWidgetSnippets)) {
                $.each(ipWidgetSnippets, function( index, value ) {
                    $('body').append(value);
                });
            }
            if (typeof ipWidgetLayoutModalTemplate  !== "undefined") {
                $('body').append(ipWidgetLayoutModalTemplate);
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
                        window.location = window.location.href.split('#')[0];
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
