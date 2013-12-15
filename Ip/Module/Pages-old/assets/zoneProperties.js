

var ipPagesZoneProperties = new function () {
    "use strict";
    var curZoneName,
        curWebsiteId,
        curLanguageId;

    this.open = function (websiteId, zoneName, languageId) {
        showLoader();
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
            type: 'GET',
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
            $form.validator(validatorConfig);

            $form.on("submit", function (e) {
                updateZone(e, $form);
                return false;
            });

            $('#pageProperties').tabs();

        }
        hideLoader();
    };

    var updateZone = function (e, $form) {
        if (!e.isDefaultPrevented()) {
            showLoader();
            $.ajax({
                url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
                dataType: 'json',
                type : 'POST',
                data: $form.serialize(),
                success: function (response) {
                    hideLoader();
                    if (response.status && response.status === 'success') {
                        //form has been successfully submitted.
                    } else {

                        //PHP controller says there are some errors
                        if (response.errors) {
                            $form.data("validator").invalidate(response.errors);
                        }
                    }

                },
                error: function (response) {
                    hideLoader();
                }

            });
        }
    };


    var showLoader = function () {
        $('.ipsLoading').removeClass('ipgHide');
        $('.ipsContent').addClass('ipgHide');
    };


    var hideLoader = function () {
        $('.ipsLoading').addClass('ipgHide');
        $('.ipsContent').removeClass('ipgHide');
    };

}

