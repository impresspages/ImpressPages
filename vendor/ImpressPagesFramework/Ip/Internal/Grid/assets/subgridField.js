(function ($) {
    "use strict";

    $('.ipsGrid .ipsSubgrid').off('click.subgrid').on('click.subgrid', function (e) {
        e.preventDefault();
        window.location = '#grid&'
    });

});
