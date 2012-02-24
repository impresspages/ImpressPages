/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

// jQuery plugins for widgets
(function($) {
    $.fn.IpFaqWidget = function() {
        return this.each(function() {
                var $this = $(this).children('h3'), state = false, answer = $this.next('div').hide().css('height','auto').slideUp();
                $this.click(function() {
                    state = !state;
                    answer.slideToggle(state);
                    answer.toggleClass('ipWidgetFaqAnswerVisible',state);
                    $this.toggleClass('ipWidgetFaqQuestionVisible',state);
                });
        });
    };

})(jQuery);

// hook all widgets with plugins
$(document).ready(function() {
    // handling all widgets by class
    $('.ipWidget-IpFaq').IpFaqWidget();
});


