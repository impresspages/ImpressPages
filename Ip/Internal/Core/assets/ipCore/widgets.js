
/*************
 * Form widget
 **************/

jQuery.fn.ipWidgetForm = function() {
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
    return this.each(function() {
        var $map = $(this);
        var initWaiter;
        $(this).height($(this).data('height'));

        if (!$map.data('initialized') || true) {

            var mapOptions = {
                //center: new google.maps.LatLng($(this).data('lat'), $(this).data('lng')),
                center: new google.maps.LatLng(-34.397, 150.644),
                zoom: 8, //$map.data('zoom'),
//                mapTypeId: $map.data('mapview'),

            };

            map = new google.maps.Map($(this).find('.ipsMap').get(0), mapOptions);

            if ((typeof ($map.data('markerlat') !== 'undefined')) && (typeof ($map.data('markerlng') !== 'undefined'))) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng($(this).data('markerlat'), $map.data('markerlng')),
                    map: map
                });
            }


        }



    });
};


