/*************
 * Form widget
 **************/

// Form widget
jQuery('.ipWidget-Form').on('ipSubmitResponse', function (e, response) {

    function isScrolledIntoView(elem)
    {
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }

    var $widget = $(this);
    if (response.status && response.status == 'success') {
        var $success = $widget.find('.ipwSuccess');
        $success.show();
        $widget.find('.ipwForm').hide();
        if (!isScrolledIntoView($widget.find('.ipwSuccess'))) {
            $('html, body').animate({
                scrollTop: $success.offset().top
            }, 500);
        }
    }
});


/*************
 * Map widget
 **************/


jQuery.fn.ipWidgetMap = function () {
    "use strict";

    return this.each(function () {
        if (ip.isManagementState) {
            return; //management part will initialize script by itself
        }

        var $widget = $(this);
        var $map = $widget.find('.ipsMap');
        var data = $map.data();


        if (!$widget.data('initialized')) {

            //init map
            if (typeof(data.lat) == 'undefined') {
                data.lat = 0;
            }
            if (typeof(data.lng) == 'undefined') {
                data.lng = 0;
            }

            var mapOptions = {
                center: new google.maps.LatLng(data.lat, data.lng),
                zoom: 0
            };

            if (data.maptypeid) {
                mapOptions.mapTypeId = data.maptypeid;
            }
            if (data.zoom) {
                mapOptions.zoom = parseInt(data.zoom);
            }

            var map = new google.maps.Map($map.get(0), mapOptions);

            if ((typeof ($map.data('markerlat') !== 'undefined')) && (typeof ($map.data('markerlng') !== 'undefined'))) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng($map.data('markerlat'), $map.data('markerlng')),
                    map: map
                });

            }

            $widget.trigger('ipWidgetMapInit', {map: map, marker: marker});

        }


    });
};


