$(function() {
    "use strict";

    $('.ipsGrid').on('init.grid', ipLanguages.init);


});

var ipLanguages = new function() {

    this.init = function(e) {
        $('.ipsCustomAdd').off('click').on('click', showAddModal);
    };

    var showAddModal = function() {
        alert('modal');
    };


};

