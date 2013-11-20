



$( document ).ready(function() {
    "use strict";
    ipContent.init();
});

var ipContent = new function() {
    "use strict";

    this.init = function() {

        if ("undefined" !== 'ipContentShowEditButton') {
            $('body').append('<div class="ipsContentEdit" style="position: fixed; right: 0px; top: 0px; background-color: #000; color: #fff; padding: 10px;">{{Edit}}</div>');
            $('.ipsContentEdit').on('click', function() {
                ipContent.setManagementMode(1);
            });
        }
    };

    this.setManagementMode = function(mode) {
        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type : 'POST',
            data: {aa: 'Content.setManagementMode', value: mode, securityToken: ip.securityToken},
            success: function (response) {
                window.location = window.location.href.split('#')[0];
            },
            error: function (response) {
                alert('error: ' + response);
            }
        });
    };


};

var preview = function (e) {

}
