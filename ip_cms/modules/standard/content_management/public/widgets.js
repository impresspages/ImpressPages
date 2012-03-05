/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

/************
 * FAQ widget
 ************/
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

/*************
 * IpForm widget
 **************/

(function($) {
    $.fn.ipWidgetIpForm = function() {
        return this.each(function() {
            $ipForm = $(this);
            
            $ipForm.find('form').validator();
            $ipForm.find('form').submit(function(e) {
                var form = $(this);

                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url: ip.baseUrl,
                        dataType: 'json',
                        type : 'POST',
                        data: form.serialize(),
                        success: function (response){
                            if (response.status && response.status == 'success') {
                                if (typeof ipWidgetipForm_success == 'function'){ //custom handler exists
                                    ipWidgetipForm_success($ipForm);
                                } else { //default handler
                                    $ipForm.find('.ipwThankYou').show();
                                    $ipForm.find('.ipwThankYou').height($ipForm.find('.ipwForm').height());
                                    $ipForm.find('.ipwForm').hide();
                                }
                            } else {
                                if (response.errors) {
                                    form.data("validator").invalidate(response.errors);
                                }
                            }
                        }
                      });
                }
                e.preventDefault();
            });

        });
    };
})(jQuery);




// IpForm widget select options
ipWidgetIpForm_InitListOptions = function ($context, currentOptions) {
    var addOption = function (value) {
        var $newOption = $context.find('.ipgHide .ipaOptionTemplate').clone();
        $newOption.find('.ipaOption').val(value);
        $context.find('.ipaContainer').append($newOption);
    };
    
    $context.find('.ipaAdd').bind('click', function () {
        addOption();
    });
    
    $context.delegate('.ipaRemove', 'click', function () {
        $(this).closest('.ipaOptionTemplate').remove();
        return false;
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

ipWidgetipForm_SaveListOptions = function ($context) {
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
ipWidgetipForm_InitWysiwygOptions = function ($context, currentOptions) {
    if (currentOptions && currentOptions.text) {
        $context.find(".ipaContainer").val(currentOptions.text);
    }
    $context.find(".ipaContainer").tinymce(ipTinyMceConfigMin);
};

ipWidgetipForm_SaveWysiwygOptions = function ($context) {
    return {text:$context.find('.ipaContainer').val()};
};



/*************
 * hook all widgets with plugins
 */
$(document).ready(function() {
    // FAQ widget
    $('.ipWidget-IpFaq').ipWidgetFaq();
    
    // IpForm widget
    $('.ipWidget-IpForm').ipWidgetIpForm();

});
