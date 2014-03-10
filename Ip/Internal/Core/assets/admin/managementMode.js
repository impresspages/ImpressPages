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

            $('.ipsContentPublish').on('click', save);

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
                        window.location = window.location.href.split('#')[0] + '?cms_revision=' + ip.revisionId;
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

    var save = function(publish) {
        var $this = $(this);
        var postData = Object();
        postData.aa = 'Content.savePage';
        postData.securityToken = ip.securityToken;
        postData.revisionId = ip.revisionId;
        postData.publish = 1;

        $.ajax({
            type : 'POST',
            url : ip.baseUrl,
            data : postData,
            context : $this,
            success : savePageResponse,
            dataType : 'json'
        });
    };

    var savePageResponse = function(response) {
        var $this = $(this);
        var data = $this.data('ipContentManagement');
        if (response.status == 'success') {
            window.location.href = response.newRevisionUrl;
        } else {

        }
    };


})(ip.jQuery);
