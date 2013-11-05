

var ipPagesZoneProperties = new function () {
    "use strict";
    var curZoneName,
        curWebsiteId,
        curLanguageId;

    this.open = function (websiteId, zoneName, languageId) {
        //hide current data
        $('#pageProperties').tabs('destroy');
        $('#pageProperties').html('');
        curZoneName = zoneName;
        curWebsiteId = websiteId;
        curLanguageId = languageId;

        //load new data
        var data = {};
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

            var $form = $("#pageProperties form");
            $form.validator(ip.validatorConfig);

console.log('bind');
            $form.on("submit", function (e) {
//                updateZone(e);
console.log('submit');
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
                        dataType: 'json',
                        type : 'POST',
                        data: $form.serialize(),
                        success: function (response) {
                            console.log('response');
                            if (response.status && response.status === 'success') {
                                //form has been successfully submitted.
                            } else {
                                //PHP controller says there are some errors
                                if (response.errors) {
                                    form.data("validator").invalidate(response.errors);
                                }
                            }
                        }
                    });
                } else {
                    alert('error');
                }

                e.preventDefault();

                return false;
            });

            $('#pageProperties').tabs();

        }
    };

    var updateZone = function (e) {
        var form = $(this);
        // client-side validation OK.


    };

}

