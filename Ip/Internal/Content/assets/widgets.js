/**
 * @package ImpressPages
 *
 *
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
            if (!$container.hasClass('ipwDisabled')) {
                // can start with expanded or collapsed, depending on the class found
                if ($container.hasClass('ipwExpanded')) {
                    $answer.show();
                } else {
                    $container.addClass('ipwCollapsed');
                    $answer.slideUp();
                }
                $question.click(function() {
                    $answer.slideToggle();
                    $container.toggleClass('ipwCollapsed ipwExpanded');
                });
            }
        });
    };
})(ip.jQuery);

/*************
 * Form widget
 **************/

(function($) {
    $.fn.ipWidgetForm = function() {
        return this.each(function() {
            var $ipForm = $(this);
            
            $ipForm.find('form').validator(validatorConfig);
            $ipForm.find('form').submit(function(e) {
                var form = $(this);

                // client-side validation OK.
                if (!e.isDefaultPrevented()) {
                    $.ajax({
                        url : ip.baseUrl,
                        dataType: 'json',
                        type : 'POST',
                        data: form.serialize(),
                        success: function (response){
                            if (response.status && response.status == 'success') {
                                if (typeof ipWidgetFormSuccess == 'function'){ //custom handler exists
                                    ipWidgetFormSuccess($ipForm);
                                } else { //default handler
                                    $ipForm.find('.ipwSuccess').show();
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
})(ip.jQuery);

// defining global variables
var ipWidgetForm_InitListOptions;
var ipWidgetForm_SaveListOptions;
var ipWidgetForm_InitWysiwygOptions;
var ipWidgetForm_SaveWysiwygOptions;

(function($) {
    /*
     * hook all widgets with plugins
     */
    $(document).ready(function() {
        // FAQ widget
        $('.ipWidget-Faq').ipWidgetFaq();

        // Form widget
        $('.ipWidget-Form').ipWidgetForm();
    });

    // Form widget select options
    ipWidgetForm_InitListOptions = function ($context, currentOptions) {
        var addOption = function (value) {
            var $newOption = $context.find('.ipgHide .ipaFieldOptionsTemplate').clone();
            $newOption.find('.ipaOptionLabel').val(value);
            $context.find('.ipaFieldOptionsContainer').append($newOption);
        };

        $context.find('.ipaFieldOptionsAdd').bind('click', function (e) {
            e.preventDefault();
            addOption();
        });

        $context.delegate('.ipaOptionRemove', 'click', function () {
            $(this).closest('.ipaFieldOptionsTemplate').remove();
            return false;
        });

        if (currentOptions && currentOptions.list) {
            for(var i=0; i<currentOptions.list.length; i++) {
                addOption(currentOptions.list[i]);
            }
        } else {
            addOption(); //add first empty option
        }

        $context.find(".ipaFieldOptionsContainer").sortable();
        $context.find(".ipaFieldOptionsContainer").sortable('option', 'handle', '.ipaOptionMove');
    };

    ipWidgetForm_SaveListOptions = function ($context) {
        var $options = $context.find('.ipaFieldOptionsContainer .ipaOptionLabel');
        var answer = new Array();
        answer = new Array();
        $options.each(function (i) {
            var $this = $(this);
            answer.push($this.val());
        });
        return {list : answer};
    };

    //Form widget wysiwyg options
    ipWidgetForm_InitWysiwygOptions = function ($context, currentOptions) {
        if (currentOptions && currentOptions.text) {
            $context.find(".ipaFieldOptionsRichText").val(currentOptions.text);
        }
        $context.find(".ipaFieldOptionsRichText").tinymce(ipTinyMceConfig());
    };

    ipWidgetForm_SaveWysiwygOptions = function ($context) {
        return {text:$context.find('.ipaFieldOptionsRichText').val()};
    };

})(ip.jQuery);
