(function ($) {
    "use strict";

    var ipAdministrators = new function() {

        this.init = function () {
            $('.ipsAdd').on('click', openAddPopup);

        }


        var openAddPopup = function() {
            $('.ipsAddAdministratorModal').modal();
        };


    };

    ipAdministrators.init();


})(ip.jQuery);

