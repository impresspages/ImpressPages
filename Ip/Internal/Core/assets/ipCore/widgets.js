
/*************
 * Form widget
 **************/

jQuery.fn.ipWidgetForm = function() {
    "use strict";
    return this.each(function() {
        var $ipForm = $(this);

        $ipForm.find('form').validator(validatorConfig);
        $ipForm.find('form').submit(function(e) {
            var form = $(this);

            // client-side validation OK.
            if (!e.isDefaultPrevented()) {
                $.ajax({
                    url : ip.baseUrl,
                    dataType: 'json',
                    type : 'POST',
                    data: form.serialize(),
                    success: function (response){
                        if (response.status && response.status == 'success') {
                            if (typeof ipWidgetFormSuccess == 'function'){ //custom handler exists
                                ipWidgetFormSuccess($ipForm);
                            } else { //default handler
                                $ipForm.find('.ipwSuccess').show();
                                $ipForm.find('.ipwForm').hide();
                            }
                        } else {
                            if (response.errors) {
                                form.data("validator").invalidate(response.errors);
                            }
                        }
                    }
                  });
            }
            e.preventDefault();
        });

    });
};




/*************
 * Map widget
 **************/


jQuery.fn.ipWidgetMap = function() {
    "use strict";

    return this.each(function() {
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

        }



    });
};


