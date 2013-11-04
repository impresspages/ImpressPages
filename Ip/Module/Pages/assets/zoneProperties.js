

var ipPagesZoneProperties = new function () {
    "use strict";
    var curZoneName,
        curWebsiteId,
        curLanguageId;

    this.open = function (websiteId, zoneName, languageId) {
        //hide current data
        $('#pageProperties').html('');
        curZoneName = zoneName;
        curWebsiteId = websiteId;
        curLanguageId = languageId;

        //load new data
        var data = Object();
        data.zoneName = curZoneName;
        data.websiteId = curWebsiteId;
        data.languageId = curLanguageId;
        data.aa = 'Pages.getZoneProperties';

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            success: openResponse,
            dataType: 'json'
        });

    };

    var openResponse = function (response) {
        if (response && response.html) {
            $('#pageProperties').html(response.html);

            $("#pageProperties form").bind("submit", function (e) {
                e.preventDefault();
                updateZone();
                return false;
            });

            $('#pageProperties').tabs();

        }
    };

    var updateZone = function () {
        alert('start update');
    }

}

