
(function($){
    "use strict";

    var ipSystem = new function() {

        this.init = function() {
            var postData = {};
            postData.aa = 'System.getIpNotifications';
            postData.securityToken = ip.securityToken;
            postData.jsonrpc = '2.0';

            $.ajax({
                url: ip.baseUrl,
                data: postData,
                dataType: 'json',
                type: 'POST',
                success: notificationsResponse,
                error: function () {
                    alert('Unknown error. Please see logs.');
                }
            });

            $('body').on('click', '.actStartUpdate', startUpdate);
        };

        var notificationsResponse = function (response) {
            var $container = $('#systemInfo');
            var messages = '';
            if (response != '') {
                messages = response;
                if (messages.length > 0) {
                    $container.css('display', '');
                    var i = 0;
                    for (i = 0; i < messages.length; i++) {
                        $container.html($container.html() + '<div class="' + messages[i]['type'] + '">' + messages[i]['message'] + '</div>');

                        if (messages[i]['code'] == 'update') {
                            $container.html($container.html() + ' <a target="_blank" class="button" href="' + messages[i]['downloadUrl'] + '">Download</a> <a class="button actStartUpdate" href="' + messages[i]['downloadUrl'] + '">Start update</a><br/><br/>');
                        }
                        $container.html($container.html() +  '<div class="clear"></div>');
                    }
                }
            }

        }


        var startUpdate = function (e) {
            e.preventDefault();

            var postData = {};
            postData.aa = 'System.startUpdate';
            postData.securityToken = ip.securityToken;

            $.ajax({
                url: ip.baseUrl,
                data: postData,
                dataType: 'json',
                type: 'POST',
                success: function (response) {
                    if (!response) {
                        return;
                    }
                    if (response.status && response.status == 'success') {
                        if (response.redirectUrl) {
                            parent.document.location = response.redirectUrl;
                        }
                    } else {
                        if (response.error) {
                            alert(response.error);
                        }
                    }
                },
                error: function () {
                    alert('Unknown error. Please see logs.');
                }
            });

        }



    };

    $(function() {
        ipSystem.init();
    });

})(ip.jQuery);

