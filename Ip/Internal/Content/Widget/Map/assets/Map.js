/**
 * @package ImpressPages
 *
 */


var IpWidget_Map = function () {
    "use strict";

    var controllerScope = this;
    this.$widgetObject = null;
    this.data = null;
    this.map = null;
    this.marker = null;

    this.init = function ($widgetObject, data) {
        this.$widgetObject = $widgetObject;
        this.data = data;
        var context = this;
        var $map = this.$widgetObject.find('.ipsMap');

        if (!$map.length) {
            // happens if there is no Google Maps key
            return;
        }

        if (jQuery.fn.ipWidgetMap && typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
            jQuery(this.$widgetObject.get()).ipWidgetMap();
        }


        var $resizeContainer = $('<div></div>');
        $resizeContainer.height($map.height());
        $map.replaceWith($resizeContainer);
        $resizeContainer.append($map);

        this.$widgetObject.append($('#ipWidgetMapSearchBoxTemplate input').clone().detach().css('marginTop', '6px'));


        $resizeContainer.resizable({
            aspectRatio: false,
            maxWidth: context.$widgetObject.width(),
            handles: "s",
            stop: function (event, ui) {
                $map.height(ui.size.height);
                $(document).trigger('ipWidgetResized');
            }
        });


        if (typeof(google) !== 'undefined' && typeof(google.maps) !== 'undefined' && typeof(google.maps.LatLng) !== 'undefined') {
            $.proxy(initMap, this)();
        } else {
            jQuery(document).on('ipGoogleMapsLoaded', jQuery.proxy(initMap, this));
            ipLoadGoogleMaps();
        }


    };

    var initMap = function () {
        var context = this;
        var $widget = this.$widgetObject;
        var $map = $widget.find('.ipsMap');
        var data = this.data;
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

        if (data.mapTypeId) {
            mapOptions.mapTypeId = data.mapTypeId;
        }
        if (data.zoom) {
            mapOptions.zoom = parseInt(data.zoom);
        }

        mapOptions.scrollwheel = false;

        var map = new google.maps.Map($map.get(0), mapOptions);
        //map.disableScrollWheelZoom();
        this.map = map;


        if ((typeof (data.markerlat) !== 'undefined') && (typeof (data.markerlng) !== 'undefined')) {
            this.marker = new google.maps.Marker({
                position: new google.maps.LatLng(data.markerlat, data.markerlng),
                map: this.map
            });
        }

        //bind map events
        google.maps.event.addListener(this.map, 'bounds_changed', $.proxy(save, this));
        google.maps.event.addListener(this.map, 'maptypeid_changed', $.proxy(save, this));

        google.maps.event.addListener(this.map, 'click', function (event) {
            $.proxy(placeMarker, context)(event.latLng);
        });



        // Create the search box and link it to the UI element.
        var input = /** @type {HTMLInputElement} */(
            this.$widgetObject.find('.ipsWidgetMapLocationSearch')[0]);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
            /** @type {HTMLInputElement} */(input));

        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            var location = places[0].geometry.location;
            $.proxy(placeMarker, context)(location);

            var newBounds = new google.maps.LatLngBounds();
            newBounds.extend(location);
            var zoom = map.getZoom();
            map.fitBounds(newBounds);
            if (zoom < 5) {
                zoom = 5;
            }
            map.setZoom(zoom);

        });



        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(map, 'bounds_changed', function() {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);
        });
        $widget.trigger('ipWidgetMapInit', {map: map, marker: this.marker});


        $(document).on('ipWidgetResized', function () {
            google.maps.event.trigger(map, "resize");
        });
    };

    function placeMarker(location) {
        if (this.marker) {
            this.marker.setPosition(location);
        } else {
            this.marker = new google.maps.Marker({
                position: location,
                map: this.map
            });
        }
        $.proxy(save, this)();

    }


    var save = function () {

        var curLatLng = this.map.getCenter();

        var data = {};

        data.lat = curLatLng.lat();
        data.lng = curLatLng.lng();
        data.zoom = this.map.getZoom();
        data.mapTypeId = this.map.mapTypeId;
        data.height = parseInt(this.$widgetObject.height());

        if (this.marker) {
            var markerPos = this.marker.getPosition();
            data.markerlat = markerPos.lat();
            data.markerlng = markerPos.lng();
        }

        this.$widgetObject.save(data, 0);
    }


};

