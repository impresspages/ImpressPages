
var ipGoogleMapsLoading = false;


/*
 * hook all widgets with plugins
 */

$(document).ready(function() {
    // Form widget
    $('.ipWidget-Form').ipWidgetForm();


    // Map widget
    if ($('.ipWidget-Map').length) {
        $('body').on('ipGoogleMapsLoaded', function () {
            $('.ipWidget-Map').ipWidgetMap();
        });

        if (typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
            ipGoogleMapsLoaded();
        } else {
            ipLoadGoogleMaps();
        }
    }

});

var ipGoogleMapsLoaded = function () {
    $('body').trigger('ipGoogleMapsLoaded');
}

var ipLoadGoogleMaps = function () {
    if (ipGoogleMapsLoading) {
        return;
    }
    ipGoogleMapsLoading = true;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&' +
        'callback=ipGoogleMapsLoaded';
    document.body.appendChild(script);
}


var ipInitForms = function () {
    ipModuleFormAdmin.init();
    ipModuleFormPublic.init();
}
