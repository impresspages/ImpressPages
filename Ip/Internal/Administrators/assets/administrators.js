(function ($) {
    "use strict";

    var ipAdministrators = new function() {

        this.init = function () {
            $('.ipsAdd').on('click', openAddPopup);

        }


        var openAddPopup = function () {
            $('.ipsAddAdministratorModal').modal();

            $('.ipsAddAdministratorModal form').off('submit').on('submit', addSubmit);
        };

        var addSubmit = function (e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                url: ip.baseUrl, //we assume that for already has m, g, a parameters which will lead this request to required controller
                dataType: 'json',
                type : 'POST',
                data: form.serialize(),
                success: function (response){
                    if (response.status && response.status == 'success') {
                        //form has been successfully submitted.
                    } else {
                        //PHP controller says there are some errors
                        if (response.errors) {
                            //form.data("validator").invalidate(response.errors);
                        }
                    }
                }
            });
        }


    };

    ipAdministrators.init();


})(ip.jQuery);

