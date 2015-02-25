/**
 * @package ImpressPages
 *
 */


/**
 * Options popup
 */
(function($) {
    "use strict";

    var methods = {
        init : function(options) {
            if (!options) {
                options = {};
            }

            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipWidget_ipForm_options');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    data = {
                        fieldTypes : options.fieldTypes
                    };
                    $this.data('ipWidget_ipForm_options', data);
                }

                return $this;
            });
        },

        showOptions : function(fieldType, currentOptions) {
            var $this = this;
            var $optionsModal = $this;
            var fieldType = $this.ipWidget_ipForm_options('getFieldType', fieldType);
            $this.find('.modal-body').html(fieldType.optionsHtml);

            var $confirm = $this.find('.ipsConfirm');
            //$('#ipWidgetFormPopup').modal('hide');
            $('#ipWidgetFormPopup').hide();


            $confirm.off().on('click', function() {
                var options = window[fieldType.optionsSaveFunction]($optionsModal);
                //$('#ipWidgetFormPopup').modal('show');
                $('#ipWidgetFormPopup').show();
                $optionsModal.modal( "hide" );
                $optionsModal.trigger('saveOptions.ipWidget_ipForm', [options]);
            });

            $optionsModal.on('hide.bs.modal', function() {
                //$('#ipWidgetFormPopup').modal('show');
                $('#ipWidgetFormPopup').show();
            });

            $optionsModal.on('hidden.bs.modal', function(e) {
                if($('.modal[aria-hidden=false]').length) {
                    $(document.body).addClass('modal-open');
                }
            });

            $this.modal();

            window[fieldType.optionsInitFunction]($this, currentOptions);
        },



        getFieldType : function (fieldType) {
            var $this = this;
            var data = $this.data('ipWidget_ipForm_options');
            return data.fieldTypes[fieldType];
        },

        optionsAvailable : function (fieldTypeKey) {
            var $this = this;
            var fieldType = $this.ipWidget_ipForm_options('getFieldType', fieldTypeKey);
            return (fieldType && (fieldType.optionsInitFunction || fieldType.optionsHtml));

        }
    };

    $.fn.ipWidget_ipForm_options = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget_ipForm_options');
        }
    };

})(jQuery);


// defining global variables
var ipWidgetForm_InitListOptions;
var ipWidgetForm_SaveListOptions;
var ipWidgetForm_InitWysiwygOptions;
var ipWidgetForm_SaveWysiwygOptions;

(function($) {
    "use strict";
    // Form widget select options
    ipWidgetForm_InitListOptions = function ($context, currentOptions) {
        var addOption = function (value) {
            var $newOption = $context.find('.hidden .ipsFieldOptionsTemplate').clone();
            $newOption.find('.ipsOptionLabel').val(value);
            $context.find('.ipsFieldOptionsContainer').append($newOption);
        };

        $context.find('.ipsFieldOptionsAdd').bind('click', function (e) {
            e.preventDefault();
            addOption();
        });

        $context.delegate('.ipsOptionRemove', 'click', function () {
            $(this).closest('.ipsFieldOptionsTemplate').remove();
            return false;
        });

        if (currentOptions && currentOptions.list) {
            for(var i=0; i<currentOptions.list.length; i++) {
                addOption(currentOptions.list[i]);
            }
        } else {
            addOption(); //add first empty option
        }


        $( ".ipsFieldOptionsContainer" ).sortable({
            handle: '.ipsOptionMove',
            cancel: false
        });
    };

    ipWidgetForm_SaveListOptions = function ($context) {
        var $options = $context.find('.ipsFieldOptionsContainer .ipsOptionLabel');
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
        var $textarea = $context.find("textarea[name=text]");
        var curMceInstance = $textarea.tinymce();
        if (curMceInstance) {
            curMceInstance.remove();
        }

        $textarea.data('ipFormRichText', null);

        if (currentOptions && currentOptions.text) {
            $textarea.val(currentOptions.text);
        }
        ipInitForms();
    };

    ipWidgetForm_SaveWysiwygOptions = function ($context) {
        var answer = {text:$context.find('textarea[name=text]').val()};
        $context.find("textarea[name=text]").tinymce().remove();
        return answer;
    };

})(jQuery);
