

$(document).ready(function() {
    "use strict";
    $('.ipsStep3').on('click', proceedInstall);
});

function proceedInstall(e){
    "use strict";
    e.preventDefault();
    ModuleInstall.step3Click();
}

