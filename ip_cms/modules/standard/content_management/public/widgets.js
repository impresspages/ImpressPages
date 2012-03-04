/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

// FAQ widget
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

// IpForm widget select options

IpForm_InitListOptions = function ($context, currentOptions) {
    var addOption = function (value) {
        var $newOption = $context.find('.ipaOptionTemplate').clone();
        $newOption.removeClass('ipaOptionTemplate');
        $newOption.find('.ipaOption').val(value);
        $context.find('.ipaContainer').append($newOption);
    };
    
    $context.find('.ipaAdd').bind('click', function () {
        addOption();
    });
    
    if (currentOptions && currentOptions.list) {
        for(var i=0; i<currentOptions.list.length; i++) {
            addOption(currentOptions.list[i]);
        }
    } else {
        addOption(); //add first empty option
    }
    
    $context.find(".ipaContainer").sortable();
    $context.find(".ipaContainer").sortable('option', 'handle', '.ipaOptionMove');
    
    
};

IpForm_SaveListOptions = function ($context) {
    var $options = $context.find('.ipaContainer .ipaOption');
    var answer = new Array();
    answer = new Array();
    $options.each(function (i) {
        var $this = $(this);
        answer.push($this.val());
    });
    return {list : answer};
};


//IpForm widget wysiwyg options

IpForm_InitWysiwygOptions = function ($context, currentOptions) {
    if (currentOptions && currentOptions.text) {
        $context.find(".ipaContainer").val(currentOptions.text);
    }
    $context.find(".ipaContainer").tinymce(ipTinyMceConfigMin);
};

IpForm_SaveWysiwygOptions = function ($context) {
    return {text:$context.find('.ipaContainer').val()};
};

// hook all widgets with plugins
$(document).ready(function() {
    // handling all widgets by class
    $('.ipWidget-IpFaq').ipWidgetFaq();
});
