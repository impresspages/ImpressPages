

/*
 * hook all widgets with plugins
 */

$(document).ready(function() {
    // Form widget
    $('.ipWidget-Form').ipWidgetForm();
    // Map widget
    if ($('.ipWidget-Form').length) {
        if (typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
            ipGooglemapsLoaded();
        } else {

            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&' +
                'callback=ipGoogleMapsLoaded';
            document.body.appendChild(script);
        }
    }

});

var ipGoogleMapsLoaded = function () {
    $('.ipWidget-Map').ipWidgetMap();
}

