
$(document).ready(function() {
    "use strict";

    $('.ipsStep4').on('click', function(e){
        e.preventDefault();
        $('.ipsForm').submit();
    });
    $('.ipsForm').on('submit', function(e){
        e.preventDefault();
        ModuleInstall.step4Click();
    });

    $('#configSiteName').focus();
});

