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
                // todox: remove HTML from JavaScript
                $('body').append('<div class="ip"><div class="ipModuleContentEditButton"><button type="button" class="btn btn-primary ipsContentEdit">{{Edit}}</button></div></div>');
                $('.ipsContentEdit').on('click', function() {
                    ipManagementMode.setManagementMode(1);
                });
            }
            if ("undefined" !== typeof(ipContentShowPublishButton)) {
                // todox: remove HTML from JavaScript
                $('body').append('<div class="ip"><div class="ipModuleContentPublishButton"><button type="button" class="btn btn-primary ipsContentPublish">{{Publish}}</button></div></div>');
            }
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
