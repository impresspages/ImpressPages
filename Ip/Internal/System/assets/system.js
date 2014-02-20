$(document).ready(function() {
    $('.ipsTopMenu').on('click', function(){
        var $this = $(this);
        $('.ipsTopMenu').removeClass('active');
        $this.addClass('active');
        $('.ipsStatus').addClass('hidden');
        $('.ipsLog').addClass('hidden');
        $('.ipsEmail').addClass('hidden');
        $('.' + $this.data('tab')).removeClass('hidden');
    });
});
