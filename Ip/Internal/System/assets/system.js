$( document ).ready(function() {
    $('.ipsTopMenu').on('click', function(){
        var $this = $(this);
        $('.ipsTopMenu').removeClass('active');
        $this.addClass('active');
        $('.ipsStatus').addClass('hide');
        $('.ipsLog').addClass('hide');
        $('.ipsEmail').addClass('hide');
        $('.' + $this.data('tab')).removeClass('hide');
    })


});
