/*!
 ImpressPages core init
 */

var ipGoogleMapsLoading = false;
var ipGoogleMapsLoaded = false;
var ipPingInterval;
var gmapsApiKey;

/*
 * hook all widgets with plugins
 */

$(document).ready(function () {

    // Map widget
    if ($('.ipWidget-Map').length) {
        $(document).on('ipGoogleMapsLoaded', function () {
            $('.ipWidget-Map').ipWidgetMap();
        });

        if (typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
            ipGoogleMapsLoaded = true;
        } else {
            ipLoadGoogleMaps();
        }
    }

    ipInitForms();

    if (typeof(ipSessionRefresh) !== 'undefined') {
        ipPingInterval = setInterval(ipPing, ipSessionRefresh * 1000);
    }



    // add ipHas... classes when core elements load
    $(document).on('ipContentManagementInit', function () {
        $(document.body).addClass('ipHasAdminPanel');
    });
    $(document).on('ipAdminPanelInit', function () {
        $(document.body).addClass('ipHasAdminNavbar');
    });

});

var ipGoogleMapsLoadedCallback = function (e) {
    ipGoogleMapsLoading = false;
    ipGoogleMapsLoaded = true;
    $(document).trigger('ipGoogleMapsLoaded');
};

var ipLoadGoogleMaps = function () {
    if (ipGoogleMapsLoaded) {
        ipGoogleMapsLoadedCallback();
    }

    if (ipGoogleMapsLoading) {
        return;
    }
    ipGoogleMapsLoading = true;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&' +
        'callback=ipGoogleMapsLoadedCallback&key=' + ip.gmapsApiKey;
    document.body.appendChild(script);
};


var ipPing = function () {
    $.ajax({
        url: ip.baseUrl,
        data: {pa: 'Core.ping'},
        method: 'GET'
    })
};
