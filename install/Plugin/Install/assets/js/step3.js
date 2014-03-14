

$(document).ready(function() {
    "use strict";

    $('.ipsStep3').on('click', function(e) {
        e.preventDefault();
        $('.ipsForm').submit();
    });
    $('.ipsForm').on('submit', function(e){
        e.preventDefault();
        ModuleInstall.step3Click();
    });

    $('#db_server').focus();
});

