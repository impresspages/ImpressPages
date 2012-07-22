/* Common backend functions */

$(document).ready(function() {
    // bind behavior to modules groups links
    $('.ipAdminNavLinks > ul > li > a').click(function(event){
        event.preventDefault();
        $this = $(this);
        $this.parent('li').addClass('ipaActive').siblings('li').removeClass('ipaActive');
    });
    // bind behavior to modules links
    $('.ipAdminNavLinks ul ul a').click(function(){
        $this = $(this);
        $('.ipAdminNavLinks ul ul .ipaActive').removeClass('ipaActive');
        $this.parent('li').addClass('ipaActive');
    });
});
