/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

// jQuery plugins for widgets
(function($) {
    $.fn.ipWidgetFaq = function() {
        return this.each(function() {
            var $faq = $(this);
            var $container = $faq.find('.ipwContainer');
            var $question = $faq.find('.ipwQuestion');
            var $answer = $faq.find('.ipwAnswer');

            // if container has 'disable' class, functionality is not added
            if (!$container.hasClass('disabled')) {
                // can start with expanded or collapsed, depending on the class found
                if ($container.hasClass('expanded')) {
                    $answer.show();
                } else {
                    $container.addClass('collapsed');
                    $answer.slideUp();
                }
                $question.click(function() {
                    $answer.slideToggle();
                    $container.toggleClass('collapsed expanded');
                });
            }
        });
    };
})(jQuery);

// hook all widgets with plugins
$(document).ready(function() {
    // handling all widgets by class
    $('.ipWidget-IpFaq').ipWidgetFaq();
});
