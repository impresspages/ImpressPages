$( document ).ready(function() {
    $('.ipsTopMenu').on('click', function(){
        var $this = $(this);
        $('.ipsTopMenu').removeClass('active');
        $this.addClass('active');
        $('.ipsStatus').addClass('ipgHide');
        $('.ipsLog').addClass('ipgHide');
        $('.ipsEmail').addClass('ipgHide');
        $('.' + $this.data('tab')).removeClass('ipgHide');
    })


});