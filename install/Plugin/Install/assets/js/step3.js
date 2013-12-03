

$(document).ready(function() {
    "use strict";
    $('.button_act').on('click', proceedInstall);
});

function proceedInstall(e){
    "use strict";
    e.preventDefault();
    ModuleInstall.step3Click();
}

