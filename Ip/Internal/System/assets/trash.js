(function($){
    "use strict";
    if ($('.ipsEmptyTrashForm').length) {

        $('.ipsEmptyTrashForm').on('ipSubmitResponse', function (e, data) {
            if (data.result == true) {
                window.location.href = window.location.href;
            } else {
                alert('There was an error removing pages.');
            }
        });
    }

})(jQuery);
